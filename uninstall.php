//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$option_name = 'w2ski_settings';

delete_option( $option_name );

remove_shortcode('w2ski');