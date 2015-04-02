<?php

/**
 * Plugin Name:       Where2Ski widget
 * Plugin URI:        http://where2ski.net
 * Description:       Provides a widget to use from the Where2Ski.net website.
 * Version:           0.1.3
 * Author:            SiteUP&trade;
 * Author URI:        http://www.siteup.org.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       w2s-stp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


require plugin_dir_path( __FILE__ ) . 'includes/class-w2ski-widget.php';


function run_w2ski_widget(){
        $plugin = new W2Ski_Widget();       
}
run_w2ski_widget();




