<?php
namespace Synclogic\Classes;

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
            session_id_system varchar(255),
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
        // 
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