<?php
namespace Synclogic\Classes;

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
            speaker_id varchar(200) NOT NULL,
            speaker_name varchar(255) NOT NULL,
            speaker_family_name varchar(255),
            category_id varchar(255),
            company varchar(255),
            image_profile varchar(255),
            biography text,
            job_title varchar(255),
            PRIMARY KEY  (speaker_id)
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
        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Faculty/MEETEXPE/LRAV2020');

        return json_decode($response);
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