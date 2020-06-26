<?php

/**
 * Plugin Name:       WP2Static Add-on: Mindfav- Erweitertes Deployment.
 * Plugin URI:        https://www.mindfav.com
 * Description:       Plugin add-on for WP2Static.
 * Version:           1.0-alpha-001
 * Author:            Steve Krämer
 * Author URI:        https://www.mindfav.com
 * License:           GPLv2
 * License URI:       http://unlicense.org
 * Text Domain:       wp2static-addon-mv-enhanced-deployment
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WP2STATIC_MV_ENHANCEMENT_ADDON_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP2STATIC_MV_ENHANCEMENT_VERSION', '1.0-alpha-006' );

require_once(WP2STATIC_MV_ENHANCEMENT_ADDON_PATH . 'Controllers/Controller.php');

/*
if ( file_exists( WP2STATIC_BOILERPLATE_PATH . 'vendor/autoload.php' ) ) {
    require_once WP2STATIC_BOILERPLATE_PATH . 'vendor/autoload.php';
}
*/

function run_wp2static_addon_mv_enhanced_deployment() {
    $controller = new WP2StaticMvEnhancedDeployment\Controller();
    $controller->run();
}

register_activation_hook(
    __FILE__,
    [ 'WP2StaticMvEnhancedDeployment\Controller', 'activate' ]
);

register_deactivation_hook(
    __FILE__,
    [ 'WP2StaticMvEnhancedDeployment\Controller', 'deactivate' ]
);

run_wp2static_addon_mv_enhanced_deployment();