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
define('CLIENT_ID', 'MEETEXPE');
define('PROJECT_ID', 'LRAV2020');

require SYNCLOGIC_BASE_PATH . 'vendor/autoload.php';

use Synclogic\Classes\Synclogic;
use Synclogic\Api\FacultiesRoutes;
use Synclogic\Classes\FacultiesTable;
use Synclogic\Classes\ProgrammeTable;
use Synclogic\Classes\PresentationsTable;
use Synclogic\Classes\FacultiesPresentationsTable;
use Synclogic\Classes\FacultiesSessionsTable;

$synclogic = new Synclogic();

$synclogic->addElement('routes', [
    new FacultiesRoutes()
]);

$synclogic->addElement('tables', [
    new FacultiesTable(),
    new ProgrammeTable(),
    new PresentationsTable(),
    new FacultiesPresentationsTable(),
    new FacultiesSessionsTable()
]);

$synclogic->register();