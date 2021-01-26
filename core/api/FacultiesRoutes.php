<?php
namespace Synclogic\Api;
use Synclogic\Helpers\CurlHelper;
use Synclogic\Classes\FacultiesTable;

class FacultiesRoutes
{
    private $wpdb;
    private $curl;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->curl = new CurlHelper();
    }

    public function register_routes() {
        register_rest_route('synclogic', 'faculties', [
            'methods' => 'GET',
            'callback' => [$this, 'fillFacultiesTable']
        ]);
    }

    public function register() 
    {
        add_action ('rest_api_init', [$this, 'register_routes']);
    }

    public function fillFacultiesTable()
    {
        $table = new FactultiesTable();
        $table->fill();
    }
}