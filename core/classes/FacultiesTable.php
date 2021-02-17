<?php
namespace Synclogic\Classes;

use Synclogic\Helpers\CurlHelper;

class FacultiesTable
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

        $this->table_name = $prefix . 'eventlogic_faculties' . $blogID;

        $this->sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            faculty_id varchar(200) NOT NULL,
            faculty_name varchar(255) NOT NULL,
            faculty_family_name varchar(255),
            category_id varchar(255),
            company varchar(255),
            image_profile varchar(255),
            biography text,
            job_title varchar(255),
            PRIMARY KEY  (faculty_id)
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
        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Faculty/'.CLIENT_ID.'/'.PROJECT_ID);
        $data = json_decode($data);

        $query = "INSERT INTO {$this->table_name} (
            faculty_id, faculty_name, 
            faculty_family_name, 
            category_id, company, 
            image_profile, 
            biography, 
            job_title
        ) VALUES ";

        foreach($data as $faculty) {
            $job_title = $faculty->job_title ?? $faculty->ExtraField01;

            $query .= $this->wpdb->prepare(
                "(%s, %s, %s, %d, %s, %s, %s, %s),",
                $faculty->Faculty_Id,
                $faculty->First_Name,
                $faculty->Family_Name,
                $faculty->Category_Code,
                $faculty->Company,
                $faculty->Image01,
                $faculty->Biography,
                $job_title
            );
        }
        
        $query = rtrim($query, ',') . ';';
        $this->wpdb->query($query);
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