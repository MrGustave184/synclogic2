<?php
namespace Synclogic\Classes;

use Synclogic\Helpers\CurlHelper;

class Synclogic
{
    private $wpdb;
    private $routes;
    private $tables;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->routes = [];
        $this->tables = [];
    }

    public function install()
    {
        foreach($this->tables as $table) {
            $table->create();
            // $table->fill();
        }

        $this->fillTables();

        add_option("synclogic_data", null, '', 'yes');
    }

    public function fillTables()
    {
        $curl = new CurlHelper();
        $blogID = get_current_blog_id();
        $prefix = $this->wpdb->prefix;
        $charset = $this->wpdb->get_charset_collate();

        $facultiesPerSessionTable = $prefix . 'eventlogic_facultiessessions' . $blogID;
        $presentationTable = $prefix . 'eventlogic_presentations' . $blogID;


 
        // Faculties
        $table_name = $prefix . 'eventlogic_faculties' . $blogID;
        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Faculty/'.CLIENT_ID.'/'.PROJECT_ID);
        $data = json_decode($data);

        $query = "INSERT INTO {$table_name} (
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


        // Sessions and presentations
        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Programme/1/2/all/'.CLIENT_ID.'/'.PROJECT_ID);
        $data = json_decode($data);
        $programme = $data->Programme;
        $sessionsTable = $prefix . 'eventlogic_programme' . $blogID;

        $sessionsQuery = "INSERT INTO {$sessionsTable} (
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
        foreach ($sessions->Sessions as $session) :
            $data_session = json_decode($curl->get(ODIN_API . "/Sessions/{$session->Session_Id}/".CLIENT_ID.'/'.PROJECT_ID));

            if((gettype($data_session) == 'array') && $data_session[0]->Hide_this_session_from_online_programme != '1') {
                $aux_all_faculties = "";
                $aux_chairs = "";
                $aux_chairs_Sequence_No = "";
                $aux_faculties_Sequence_No = "";

                $facultiesPerSessionQuery = "INSERT INTO {$facultiesPerSessionTable} (
                    session_id,
                    faculty_id,
                    sequence_number,
                    role_id
                ) VALUES ";

                $values = '';

                foreach ($data_session[0]->Session_Faculty as $faculty) {
                    if ($faculty->Role_Id == '1') {
                        $aux_chairs = $faculty->Faculty_Id . ";" . $aux_chairs;
                        $aux_chairs_Sequence_No = $faculty->Sequence_No . "-" . $faculty->Faculty_Id . ";" . $aux_chairs_Sequence_No;
                    } else {
                        $aux_all_faculties = $faculty->Faculty_Id . ";" . $aux_all_faculties;
                        $aux_faculties_Sequence_No = $faculty->Sequence_No . "-" . $faculty->Faculty_Id . ";" . $aux_faculties_Sequence_No;
                    }

                    // fill facultiessessions
                    $values .= $this->wpdb->prepare(
                        "(%d, %d, %d, %d),",
                        $session->Session_Id,
                        $faculty->Faculty_Id,
                        $faculty->Sequence_No,
                        $faculty->Role_Id
                    );
                }

                if($values) {
                    $facultiesPerSessionQuery = rtrim($facultiesPerSessionQuery.$values, ',') . ';';
                    $this->wpdb->query($facultiesPerSessionQuery);
                }

                $sessionsQuery .= $this->wpdb->prepare(
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

                // presentations query
                $presentationsQuery = "INSERT INTO {$presentationTable} (
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

                $presentationValues = '';

                // go to presentation level
                foreach($session->Presentations as $presentation) :
                    $presentationValues .= $this->wpdb->prepare(
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

                if($presentationValues) {
                    $presentationsQuery = rtrim($presentationsQuery.$presentationValues, ',') . ';';
                    $this->wpdb->query($presentationsQuery);
                }
            }
            
        endforeach;
        endforeach;
        endforeach;

        $sessionsQuery = rtrim($sessionsQuery, ',') . ';';
        $this->wpdb->query($sessionsQuery);

    }

    public function uninstall()
    {
        foreach($this->tables as $table) {
            $table->destroy();
        }
        
        delete_option('synclogic_data');
    }

    public function menuPage()
    {
        $page_title = 'Synclogic';
        $menu_title = 'Synclogic';
        $capability = 'manage_options';
        $menu_slug = 'synclogic';
        $callback = [$this, 'synclogic_menu'];
        $icon_url = 'dashicons-update';
        $position = 6;

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position);
    }

    public function synclogic_menu()
    {
        if ( is_file( SYNCLOGIC_BASE_PATH . 'layout.php' ) ) {
            include_once SYNCLOGIC_BASE_PATH . 'layout.php';
        }
    }

    public function register() 
    {
        register_activation_hook(SYNCLOGIC_FILE_PATH, [$this, 'install']);
        register_deactivation_hook(SYNCLOGIC_FILE_PATH, [$this, 'uninstall']);

        $this->registerElement('routes');

        // Add menu page
        add_action('admin_menu', [$this, 'menuPage']);
    }

    public function registerElement($property)
    {
        if(! property_exists($this, $property)) {
            return false;
        }

        if(count($this->$property)) {
            foreach($this->$property as $element) {
                $element->register();
            }
        }
    }

    public function addElement($property, array $elements)
    {
        if(! property_exists($this, $property)) {
            return false;
        }

        foreach($elements as $element) {
            array_push($this->$property, $element);
        }

        return true;
    }
}