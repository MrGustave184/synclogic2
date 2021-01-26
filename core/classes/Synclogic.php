<?php
namespace Synclogic\Classes;
use Synclogic\Classes\SynclogicDB;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Synclogic
{
    private $wpdb;
    private $synclogicDB;
    private $routes;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->synclogicDB = new SynclogicDB();
        $this->routes = [];
    }

    public function install()
    {
        foreach($this->synclogicDB->tables() as $alias => $table) {
            $tableSQL = $this->synclogicDB->getSQL($alias);
            $this->createTable($tableSQL);
        }

        add_option("synclogic_data", null, '', 'yes');
    }

    public function uninstall()
    {
        foreach($this->synclogicDB->tables() as $alias => $table) {
            $table_name = $table['name'];
            $sql = "DROP TABLE IF EXISTS $table_name;";
            $this->wpdb->query($sql);
        }
        
        delete_option('synclogic_data');
    }

    public function createTable($sql)
    {
        dbDelta($sql);
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