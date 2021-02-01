<?php
namespace Synclogic\Classes;

use Synclogic\Helpers\CurlHelper;

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
        $curl = new CurlHelper();
        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Programme/1/2/all/'.CLIENT_ID.'/'.PROJECT_ID);
        $data = json_decode($data);
        $programme = $data->Programme;

        $query = "INSERT INTO {$this->table_name} (
            abstract,
            abstract_body,
            abstract_driven,
            abstract_number,
            abstract_status,
            abstract_title,
            abstract_author,
            acptrej,
            all_speakers,
            all_speakers_list,
            all_authors,
            description,
            online_programme_is_viewable,
            person_id,
            presentation_attachment_type,
            presentation_body,
            presentation_id,
            presentation_preference,
            presentation_title,
            profile_allow_submission,
            profile_edit_start_date,
            profile_edit_end_date,
            profile_uploads_start_date,
            profile_uploads_end_date,
            public_caption,
            sequence_number,
            session_id,
            start_time,
            status_code,
            use_for_feedback,
            use_for_virtual_presentation,
            virtual_room_link_presentation,
            virtual_room_link_presentation_recorded
        ) VALUES ";

        $values = '';

        foreach ($programme->Days as $days) :
        foreach ($days->Session_Groups as $sessions) :
        foreach ($sessions->Sessions as $key => $session) :
        foreach($session->Presentations as $presentation) :
            $values .= $this->wpdb->prepare(
                "(%s, %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %d, %d, %s, %s),",
                json_encode($presentation->Abstract),
                $presentation->Abstract_Body,
                $presentation->Abstract_Driven,
                $presentation->Abstract_Number,
                $presentation->Abstract->AbstractAuthor ?? NULL,
                $presentation->Abstract_Status,
                $presentation->Abstract_Title,
                $presentation->AcptRej,
                json_encode($presentation->AllSpeakers),
                json_encode($presentation->AllSpeakersList),
                json_encode($presentation->Abstract->Authors),
                $presentation->Description,
                $presentation->Online_Programme_Is_Viewable,
                $presentation->Person_Id,
                $presentation->Presentation_Attachment_Type,
                $presentation->Presentation_Body,
                $presentation->Presentation_Id,
                $presentation->Presentation_Preference,
                $presentation->Presentation_Title,
                $presentation->Profile_Allow_Submission,
                $presentation->Profile_Edit_Start_Date,
                $presentation->Profile_Edit_End_Date,
                $presentation->Profile_Uploads_Start_Date,
                $presentation->Profile_Uploads_End_Date,
                $presentation->Public_Caption,
                $presentation->Sequence_Number,
                $presentation->Session_Id,
                $presentation->Start_Time,
                $presentation->Status_Code,
                $presentation->Use_For_Feedback,
                $presentation->Use_For_Virtual_Session,
                $presentation->Virtual_Room_Link,
                $presentation->Virtual_Room_Link_Recorded
            );
        endforeach;
        endforeach;
        endforeach;
        endforeach;

        if(! $values) {
            return false;
        }

        $query = rtrim($query.$values, ',') . ';';
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