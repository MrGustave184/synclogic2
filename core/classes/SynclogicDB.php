<?php
namespace Synclogic\Classes;

class SynclogicDB
{
    protected $wpdb;
    protected $tables;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $blogID = get_current_blog_id();
        $prefix = $this->wpdb->prefix;
        $charset = $this->wpdb->get_charset_collate();

        $this->tables = [
            'faculties' => [
                'name' => $prefix . 'eventlogic_faculties' . $blogID,
                'sql' => "CREATE TABLE IF NOT EXISTS :table_name (
                    speaker_id varchar(200) NOT NULL,
                    speaker_name varchar(255) NOT NULL,
                    speaker_family_name varchar(255),
                    category_id varchar(255),
                    company varchar(255),
                    image_profile varchar(255),
                    biography text,
                    job_title varchar(255),
                    PRIMARY KEY  (speaker_id)
                ) $charset;"
            ],

            'presentations' => [
                'name' => $prefix . 'eventlogic_presentations' . $blogID,
                'sql' => "CREATE TABLE IF NOT EXISTS :table_name (
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
                ) $charset;"
            ],

            'programme' => [
                'name' => $prefix . 'eventlogic_programme' . $blogID,
                'sql' => "CREATE TABLE IF NOT EXISTS :table_name (
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
                ) $charset;"
            ]
        ];
    }

    public function tables()
    {
        return $this->tables;
    }

    public function getTable($table)
    {
        if(! array_key_exists($table, $this->tables)) {
            return false;
        }

        return $this->tables[$table]['name'];
    }

    public function getSQL($table)
    {
        $sql = $this->tables[$table]['sql'];
        $table_name = $this->tables[$table]['name'];

        $sql = str_replace(':table_name', $table_name, $sql);
        return $sql;
    }
}