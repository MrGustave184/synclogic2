<?php
/**
 * Plugin Name: Synclogic2
 * Description: 
 * Version: 1.0
 * Author: Shocklogic Team
 * Author URI: https://shocklogic.com/
 */

define('SYNCLOGIC_BASE_PATH', plugin_dir_path(__FILE__));
define('SYNCLOGIC_BASE_URL', plugin_dir_url(__FILE__));
define('SYNCLOGIC_FILE_PATH', __FILE__);
define('SYNCLOGIC_API', 'http://virtualogic2.test/wp-json/synclogic');
define('ODIN_API', 'https://clients.shocklogic.com/odin/wp-json/shocklogic');

require SYNCLOGIC_BASE_PATH . 'vendor/autoload.php';

use Synclogic\Classes\Synclogic;
use Synclogic\Classes\SynclogicDB;
use Synclogic\Api\FacultiesRoutes;

$synclogic = new Synclogic();

$synclogic->addElement('routes', [
    new FacultiesRoutes()
]);

$synclogic->register();