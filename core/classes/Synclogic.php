<?php
namespace Synclogic\Classes;
use Synclogic\Classes\SynclogicDB;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Synclogic
{
    private $wpdb;
    private $synclogicDB;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->synclogicDB = new SynclogicDB();
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

    public function register() 
    {
        register_activation_hook(SYNCLOGIC_FILE_PATH, [$this, 'install']);
        register_deactivation_hook(SYNCLOGIC_FILE_PATH, [$this, 'uninstall']);
    }

    private function createTable($sql)
    {
        dbDelta($sql);
    }
}