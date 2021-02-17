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

// START DEBUG
define('temp_file', ABSPATH.'/_temp_out.txt' );

add_action("activated_plugin", "activation_handler1");
function activation_handler1(){
    $cont = ob_get_contents();
    if(!empty($cont)) file_put_contents(temp_file, $cont );
}

add_action( "pre_current_active_plugins", "pre_output1" );
function pre_output1($action){
    if(is_admin() && file_exists(temp_file))
    {
        $cont= file_get_contents(temp_file);
        if(!empty($cont))
        {
            echo '<div class="error"> Error Message:' . $cont . '</div>';
            @unlink(temp_file);
        }
    }
}
// END DEBUG

require SYNCLOGIC_BASE_PATH . 'vendor/autoload.php';

use Synclogic\Classes\Synclogic;
use Synclogic\Api\FacultiesRoutes;
use Synclogic\Classes\FacultiesTable;
use Synclogic\Classes\ProgrammeTable;
use Synclogic\Classes\PresentationsTable;
use Synclogic\Classes\FacultiesPresentationsTable;
use Synclogic\Classes\FacultiesSessionsTable;
use Synclogic\Classes\LinkedTables;

$synclogic = new Synclogic();

$synclogic->addElement('routes', [
    new FacultiesRoutes()
]);

$synclogic->addElement('tables', [
    // new FacultiesTable(),
    // new ProgrammeTable(),
    // new PresentationsTable(),
    // new FacultiesPresentationsTable(),
    new LinkedTables(),
    // new FacultiesSessionsTable()
]);

$synclogic->register();