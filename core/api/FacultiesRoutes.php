<?php
namespace Synclogic\Api;
use Synclogic\Helpers\CurlHelper;

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
        $curl = new CurlHelper();
        $response = $curl->get(ODIN_API . '/Faculty/MEETEXPE/LRAV2020');

        return json_decode($response);
    }
}