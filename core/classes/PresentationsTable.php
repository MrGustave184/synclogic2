<?php
namespace Synclogic\Classes;

class PresentationsTable
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

        $this->table_name = $prefix . 'eventlogic_presentations' . $blogID;

        $this->sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            abstract TEXT null,
            abstract_body TEXT null,
            abstract_driven TINYINT default 0,
            abstract_number varchar(64),
            abstract_status varchar(3),
            abstract_title TEXT,
            abstract_author TEXT,
            acptrej varchar(15),
            all_speakers TEXT,
            all_speakers_list TEXT,
            all_authors TEXT,
            description VARCHAR(50),
            online_programme_is_viewable TINYINT,
            person_id INT,
            presentation_attachment_type INT,
            presentation_body TEXT,
            presentation_id INT NOT NULL,
            presentation_preference VARCHAR(50),
            presentation_title TEXT,
            profile_allow_submission VARCHAR(50),
            profile_edit_start_date DATETIME,
            profile_edit_end_date DATETIME,
            profile_uploads_start_date DATETIME,
            profile_uploads_end_date DATETIME,
            public_caption VARCHAR(50),
            sequence_number INT,
            session_id VARCHAR(50),
            start_time TIME,
            status_code VARCHAR(50),
            use_for_feedback TINYINT,
            use_for_virtual_presentation TINYINT,
            virtual_room_link_presentation TEXT,
            virtual_room_link_presentation_recorded TEXT,
            count_speaker INT,
            PRIMARY KEY  (presentation_id)
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