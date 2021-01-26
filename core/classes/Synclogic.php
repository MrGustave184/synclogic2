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

    public function register() 
    {
        register_activation_hook(SYNCLOGIC_FILE_PATH, [$this, 'install']);
        register_deactivation_hook(SYNCLOGIC_FILE_PATH, [$this, 'uninstall']);

        $this->registerElement('routes');
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