<?php
namespace Synclogic\Classes;

use Synclogic\Helpers\CurlHelper;

class ProgrammeTable
{
    private $wpdb;
    private $table_name;
    private $sql;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $blogID = get_current_blog_id();
        $prefix = $this->wpdb->prefix;
        $charset = $this->wpdb->get_charset_collate();

        $this->table_name = $prefix . 'eventlogic_programme' . $blogID;

        $this->sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            session_id varchar(200) NOT NULL,
            session_day DATE,
            session_day_name varchar(255),
            session_title varchar(255),
            session_type varchar(255),
            session_html text,
            room_id varchar(255),
            room_name varchar(255),
            start_time varchar(255),
            end_time varchar(255),
            all_faculties varchar(255),
            all_faculties_sequence_No varchar(255),
            all_chairs varchar(255),
            all_chairs_sequence_No varchar(255),                
            virtual_room_link TEXT,
            virtual_room_link_recorded TEXT,
            virtual_widget_link_3 TEXT,
            virtual_widget_link_3_caption TEXT,
            count_presentations int,
            PRIMARY KEY  (session_id)
        ) $charset;";
    }

    public function create()
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($this->sql);
    }

    public function fill()
    {
        $curl = new CurlHelper();
        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Programme/1/2/all/MEETEXPE/RIVERBED');
        $data = json_decode($data);
        $programme = $data->Programme;

        $query = "INSERT INTO {$this->table_name} (
            session_id,
            session_day,
            session_day_name,
            session_title,
            session_type,
            session_html,
            room_id,
            room_name,
            start_time,
            end_time,
            all_faculties,
            all_faculties_sequence_No,
            all_chairs,
            all_chairs_sequence_No,                
            virtual_room_link,
            virtual_room_link_recorded,
            virtual_widget_link_3,
            virtual_widget_link_3_caption
        ) VALUES ";

        foreach ($programme->Days as $days) :
        foreach ($days->Session_Groups as $sessions) :
        foreach ($sessions->Sessions as $key => $session) :
            $data_session = json_decode($curl->get(ODIN_API . "/Sessions/{$session->Session_Id}/MEETEXPE/RIVERBED"));

            if((gettype($data_session) == 'array') && $data_session[0]->Hide_this_session_from_online_programme != '1') {
                $aux_all_faculties = "";
                $aux_chairs = "";
                $aux_chairs_Sequence_No = "";
                $aux_faculties_Sequence_No = "";

                foreach ($data_session[0]->Session_Faculty as $sefac) {
                    if ($sefac->Role_Id == '1') {
                        $aux_chairs = $sefac->Faculty_Id . ";" . $aux_chairs;
                        $aux_chairs_Sequence_No = $sefac->Sequence_No . "-" . $sefac->Faculty_Id . ";" . $aux_chairs_Sequence_No;
                    } else {
                        $aux_all_faculties = $sefac->Faculty_Id . ";" . $aux_all_faculties;
                        $aux_faculties_Sequence_No = $sefac->Sequence_No . "-" . $sefac->Faculty_Id . ";" . $aux_faculties_Sequence_No;
                    }
                }

                $query .= $this->wpdb->prepare(
                    "(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s),",
                    $session->Session_Id,
                    $session->Session_Date,
                    $days->Date_String,
                    $session->Session_Title,
                    $session->Session_Type,
                    $data_session[0]->Session_HTML,
                    $session->Room->Room_Id ?? NULL,
                    $session->Room->Room_Name ?? NULL,
                    $data_session[0]->Session_Start_Time,
                    $data_session[0]->Session_End_Time,
                    $aux_all_faculties,
                    $aux_faculties_Sequence_No,
                    $aux_chairs,
                    $aux_chairs_Sequence_No,
                    $data_session[0]->Virtual_Room_Link ?? NULL,
                    $data_session[0]->Virtual_Room_Link_Recorded ?? NULL,
                    $data_session[0]->Virtual_Widget_Link3 ?? NULL,
                    $data_session[0]->Virtual_Widget_Link3_Caption ?? NULL
                );
            }
        endforeach;
        endforeach;
        endforeach;

        $query = rtrim($query, ',') . ';';
        return $this->wpdb->query($query);
    }

    public function truncate()
    {
        $sql = "TRUNCATE TABLE {$this->table_name};";
        $this->wpdb->query($sql);
    }

    public function destroy()
    {
        $sql = "DROP TABLE IF EXISTS {$this->table_name};";
        $this->wpdb->query($sql);
    }
}