<?php
namespace Synclogic\Api;

use Synclogic\Helpers\CurlHelper;
use Synclogic\Classes\Synclogic;

class SynclogicRoutes
{
    private $wpdb;
    private $curl;
    private $synclogic;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->curl = new CurlHelper();
        $this->synclogic = new Synclogic();
    }

    public function register() 
    {
        add_action ('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() 
    {
        register_rest_route('synclogic', 'sync', [
            'methods' => 'POST',
            'callback' => [$this, 'sync']
        ]);
    }

    public function sync()
    {
        return $this->synclogic->synchronize();
    }

}