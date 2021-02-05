<?php
namespace Synclogic\Classes;

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

        add_option("synclogic_data", null, '', 'yes');
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