<?php
namespace Synclogic\Classes;

use Synclogic\Helpers\CurlHelper;

class FacultiesSessionsTable
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

        $this->table_name = $prefix . 'eventlogic_facultiessessions' . $blogID;

        // ADD PRIMARY/FOREIGN KEY!!!
        $this->sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            faculty_id INT,
            session_id INT NOT NULL,
            sequence_number INT,
            role_id INT,
            role_name VARCHAR(100)
        ) $charset;";
    }

    public function create()
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($this->sql);
    }

    public function fill()
    {

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