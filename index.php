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

require SYNCLOGIC_BASE_PATH . 'vendor/autoload.php';

use Synclogic\Classes\SynclogicDB;
use Synclogic\Classes\Synclogic;

// $db = new SynclogicDB();
// echo $db->getSQL('test');
// print_r($db->tables());die;
// print_r($db->tables());die;


$synclogic = new Synclogic();

$synclogic->register();