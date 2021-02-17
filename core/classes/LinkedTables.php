<?php
namespace Synclogic\Classes;

use Synclogic\Helpers\CurlHelper;

class LinkedTables
{
    private $wpdb;
    private $tables;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $blogID = get_current_blog_id();
        $prefix = $this->wpdb->prefix;
        $charset = $this->wpdb->get_charset_collate();

        $facultiessessions = $prefix . 'eventlogic_facultiessessions' . $blogID;
        $facultiespresentations = $prefix . 'eventlogic_facultiespresentations' . $blogID;

        $this->tables = [
            'facultiessessions' => [
                'name' => $facultiessessions,
                'sql' => "CREATE TABLE IF NOT EXISTS {$facultiessessions} (
                    faculty_id INT,
                    session_id INT NOT NULL,
                    sequence_number INT,
                    role_id INT,
                    role_name VARCHAR(100)
                ) $charset;"
            ],

            'facultiespresentations' => [
                'name' => $facultiespresentations,
                'sql' => "CREATE TABLE IF NOT EXISTS {$facultiespresentations} (
                    faculty_id INT,
                    presentation_id INT NOT NULL
                ) $charset;"
            ]
        ];
    }

    public function create()
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        foreach($this->tables as $table) {
            dbDelta($table['sql']);
        }
    }

    public function fill()
    {
        $curl = new CurlHelper();
        $facultiesPerSessionTable = $this->tables['facultiessessions']['name'];
        $facultiesPresentationsTable = $this->tables['facultiespresentations']['name'];

        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Programme/1/2/all/'.CLIENT_ID.'/'.PROJECT_ID);
        $data = json_decode($data);
        $programme = $data->Programme;

        foreach ($programme->Days as $days) :
        foreach ($days->Session_Groups as $sessions) :
        foreach ($sessions->Sessions as $session) :
            $data_session = json_decode($curl->get(ODIN_API . "/Sessions/{$session->Session_Id}/".CLIENT_ID.'/'.PROJECT_ID));

            if((gettype($data_session) == 'array') && $data_session[0]->Hide_this_session_from_online_programme != '1') {
                $facultiesPerSessionQuery = "INSERT INTO {$facultiesPerSessionTable} (
                    session_id,
                    faculty_id,
                    sequence_number,
                    role_id
                ) VALUES ";

                $facultiesPerSessionValues = '';

                foreach ($data_session[0]->Session_Faculty as $faculty) {
                    $facultiesPerSessionValues .= $this->wpdb->prepare(
                        "(%d, %d, %d, %d),",
                        $session->Session_Id,
                        $faculty->Faculty_Id,
                        $faculty->Sequence_No,
                        $faculty->Role_Id
                    );
                }

                if($facultiesPerSessionValues) {
                    $facultiesPerSessionQuery = rtrim($facultiesPerSessionQuery.$facultiesPerSessionValues, ',') . ';';
                    $this->wpdb->query($facultiesPerSessionQuery);
                }

                $facultiesPresentationsQuery = "INSERT INTO {$facultiesPresentationsTable} (
                    faculty_id,
                    presentation_id
                ) VALUES ";

                $facultiesPresentationsValues = '';

                // go to presentation level
                foreach($session->Presentations as $presentation) {
                    foreach($presentation->AllSpeakers as $faculty) {                      
                        $facultiesPresentationsValues .= $this->wpdb->prepare(
                            "(%d, %d),",
                            $faculty->Faculty_Id,
                            $presentation->Presentation_Id
                        );
                    }
                }

                if( $facultiesPresentationsValues) {
                    $facultiesPresentationsQuery = rtrim($facultiesPresentationsQuery.$facultiesPresentationsValues, ',') . ';';
                    $this->wpdb->query($facultiesPresentationsQuery);
                }
            }
            
        endforeach;
        endforeach;
        endforeach;
    }

    public function truncate()
    {
        foreach($this->tables as $table) {
            $table_name = $table['name'];
            $this->wpdb->query("TRUNCATE TABLE {$table_name};");
        }
    }

    public function destroy()
    {
        foreach($this->tables as $table) {
            $table_name = $table['name'];
            $this->wpdb->query("DROP TABLE IF EXISTS {$table_name};");
        }
    }
}