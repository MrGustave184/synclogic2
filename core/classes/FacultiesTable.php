<?php
namespace Synclogic\Classes;

use Synclogic\Classes\SynclogicDB;

class FacultiesTable
{
    private $wpdb;
    private $name;
    private $sql;

    public function __construct()
    {
        global $wpdb;
        $synclogicDB = new SynclogicDB();


        $this->wpdb = $wpdb;
    }

    public function fill()
    {
        $curl = new CurlHelper();
        $data = $curl->get(ODIN_API . get_option('synclogic_data') . '/Faculty/MEETEXPE/LRAV2020');

        return json_decode($response);
    }

    public function truncate()
    {
        // 
    }

    public function delete()
    {
        // 
    }
}