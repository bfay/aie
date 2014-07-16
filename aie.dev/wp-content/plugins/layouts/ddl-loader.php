<?php
if( defined('WPDDL_VERSION') ) return;
define('WPDDL_VERSION', '0.9.1');

function ddl_load_embedded_views() {
    if (!defined('WPV_VERSION')) {
		// Load embedded Views
		if (defined ('WPDDL_IN_THEME_MODE')) {
			define('WPV_FOLDER', basename(dirname(__FILE__)) . '/embedded-views');
		}
        require_once dirname(__FILE__) . '/embedded-views/views.php';
    }
}

if (defined ('WPDDL_IN_THEME_MODE')) {
	ddl_load_embedded_views();
} else {
	add_action('after_setup_theme', 'ddl_load_embedded_views', 2);
}

// Load embedded Module Manager.
if (is_admin()) {
	//if (!(defined('MODMAN_RUN_MODE'))) {
	//	if (defined ('WPDDL_IN_THEME_MODE')) {
	//		define('MODMAN_PLUGIN_FOLDER', basename(dirname(__FILE__)) . '/embedded-modules-manager');
	//	} else {
	//		define('MODMAN_PLUGIN_FOLDER', basename(dirname(__FILE__)) . '/embedded-modules-manager');
	//		define('MODMAN_PLUGIN_URL',plugins_url() . '/' . MODMAN_PLUGIN_FOLDER);
	//	}
	//   require_once dirname(__FILE__) . '/embedded-modules-manager/plugin.php';
	//}
}


define( 'WPDDL_ABSPATH', dirname( __FILE__ ) );
define( 'WPDDL_INC_ABSPATH', WPDDL_ABSPATH . '/inc' );
define( 'WPDDL_INC_RELPATH', WPDDL_RELPATH . '/inc' );
define( 'WPDDL_CLASSES_ABSPATH', WPDDL_ABSPATH . '/classes' );
define( 'WPDDL_CLASSES_RELPATH', WPDDL_RELPATH . '/classes' );
define( 'WPDDL_RES_ABSPATH', WPDDL_ABSPATH . '/resources' );
define( 'WPDDL_RES_RELPATH', WPDDL_RELPATH . '/resources' );
define( 'WPDDL_GUI_ABSPATH', WPDDL_ABSPATH . '/inc/gui/' );
define( 'WPDDL_GUI_RELPATH', WPDDL_RELPATH . '/inc/gui/' );

define( 'WPDDL_EMBEDDED_ABSPATH', WPDDL_ABSPATH  . '/embedded' );
define( 'WPDDL_COMMON_ABSPATH', WPDDL_EMBEDDED_ABSPATH  . '/common' );

define( 'WPDDL_EMBEDDED_REL', WPDDL_RELPATH  . '/embedded' );
define( 'WPDDL_COMMON_REL', WPDDL_EMBEDDED_REL  . '/common' );

if( !defined('WPDDL_DEBUG') ) define('WPDDL_DEBUG', false);

define('WPDDL_GENERAL_OPTIONS', 'ddlayouts_options');
define('WPDDL_CSS_OPTIONS', 'layout_css_settings');
define('WPDDL_LAYOUTS_CSS', 'layout_css_styles');

define('DDL_ITEMS_PER_PAGE', 10 );

require_once WPDDL_INC_ABSPATH . '/constants.php';
require_once WPDDL_INC_ABSPATH . '/help_links.php';


require_once WPDDL_CLASSES_ABSPATH . '/wpddl.layout.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.json2layout.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.layout-render.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.registered_cell.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.registered_layout_theme_section.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.editor.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.file-manager.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.cssmanager.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.optionsmanager.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.scripts.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.post-types-manager.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.post-types-manager.class.php';
require_once WPDDL_CLASSES_ABSPATH . '/wpddl.cssframerwork.options.class.php';
require_once WPDDL_CLASSES_ABSPATH .'/wpddl.layouts-listing.class.php';

require_once WPDDL_GUI_ABSPATH . '/dialogs/dialogs.php';
require_once WPDDL_GUI_ABSPATH . '/editor/editor.php';

require_once WPDDL_INC_ABSPATH . '/api/ddl-fields-api.php';

require_once WPDDL_INC_ABSPATH . '/api/ddl-theme-api.php';

require_once WPDDL_INC_ABSPATH . '/api/ddl-features-api.php';

include_once WPDDL_RES_ABSPATH. '/log_console.php';

add_action( 'init', 'init_layouts_plugin', 9 );

function init_layouts_plugin()
{
	global $wpddlayout;
	$wpddlayout = new WPDD_Layouts();
}