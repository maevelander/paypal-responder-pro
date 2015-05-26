<?php
/*
Plugin Name: PayPal Responder Pro
Plugin URI: http://www.enigmaplugins.com
Description: A really simple PayPal plugin. It processes payment for a product via PayPal, then sends an email responder to the customer and returns them to a URL of your choice. That's it.
Author: Enigma Plugins
Version: 1.0.4
Author URI: http://www.enigmaplugins.com
*/

add_action('admin_menu','wp_paypal_menu');
require 'includes/db-settings.php';

register_activation_hook( __FILE__,'wp_paypal_db');
function wp_paypal_menu() {
    add_menu_page( 'WP Paypal Products', 'PayPal Resp +', 'manage_options', 'wp-paypal', 'wp_paypal_settings',plugin_dir_url( __FILE__ )."paypal-icon.png" );
    add_submenu_page('wp-paypal','','','manage_options','wp-paypal','wp_paypal_settings');
    add_submenu_page('wp-paypal','Settings','Settings','manage_options','wp-paypal','wp_paypal_settings');
    add_submenu_page( 'wp-paypal', 'Manage Products', 'Manage Products', 'manage_options', 'paypal_products', 'wp_paypal_products' );
    add_submenu_page( 'wp-paypal', 'Manage Responders', 'Manage Responders', 'manage_options', 'paypal_responders', 'wp_paypal_responders' );
    add_submenu_page( 'wp-paypal', 'Plugin License', 'Activate License', 'manage_options', 'paypal-license', 'pp_pro_license_page' );
	
}

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'PP_PRO_STORE_URL', 'http://enigmaplugins.com' );
// you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'PP_PRO_ITEM_NAME', 'Paypal Responder Pro' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
    // load our custom updater
    include( dirname( __FILE__ ) . '/paypal_updater.php' );
}

function pp_plugin_updater() {
    // retrieve our license key from the DB
    $license_key = trim( get_option( 'pp_pro_license_key' ) );

    // setup the updater
    $edd_updater = new EDD_SL_Plugin_Updater( PP_PRO_STORE_URL, __FILE__, array(
                    'version' 	=> '1.0.4', 				// current version number
                    'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
                    'item_name' => PP_PRO_ITEM_NAME, 	// name of this plugin
                    'author' 	=> 'Enigma Plugins'  // author of this plugin
            )
    );
}
add_action( 'admin_init', 'pp_plugin_updater', 0 );


/************************************
* the code below is just a standard
* options page. Substitute with
* your own.
*************************************/

function pp_pro_license_page() {
    $license 	= get_option( 'pp_pro_license_key' );
    $status 	= get_option( 'pp_pro_license_status' );
?>
    <div class="wrap">
        <h2><?php _e('Plugin License Options'); ?></h2>
        <p>
            <strong>Please enter and activate your license key in order to receive automatic updates and support for this plugin</strong>
        </p>
	<form method="post" action="options.php">
	<?php settings_fields('pp_pro_license'); ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('License Key'); ?>
                        </th>
                        <td>
                            <input id="pp_pro_license_key" name="pp_pro_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
                            <label class="description" for="pp_pro_license_key"><?php _e('Enter your license key'); ?></label>
                        </td>
                    </tr>
                <?php
                    if( false !== $license ) { ?>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Activate License'); ?>
                        </th>
                        <td>
                    <?php
                        if( $status !== false && $status == 'valid' ) {
                    ?>
                        <span style="color:green;"><?php _e('active'); ?></span>
                        <?php wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' ); ?>
                        <input type="submit" class="button-secondary" name="pp_pro_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                    <?php
                        } else {
                        wp_nonce_field( 'pp_pro_nonce', 'pp_pro_nonce' );
                    ?>
                        <input type="submit" class="button-secondary" name="pp_pro_license_activate" value="<?php _e('Activate License'); ?>"/>
                    <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php submit_button(); ?>

        </form>
<?php
}

function pp_pro_register_option() {
    // creates our settings in the options table
    register_setting('pp_pro_license', 'pp_pro_license_key', 'pp_sanitize_license' );
}
add_action('admin_init', 'pp_pro_register_option');

function pp_sanitize_license( $new ) {
    $old = get_option( 'pp_pro_license_key' );
    if( $old && $old != $new ) {
        delete_option( 'pp_pro_license_status' ); // new license has been entered, so must reactivate
    }
    return $new;
}

/************************************
* this illustrates how to activate
* a license key
*************************************/

function pp_pro_activate_license() {
    // listen for our activate button to be clicked
    if( isset( $_POST['pp_pro_license_activate'] ) ) {

        // run a quick security check
        if( ! check_admin_referer( 'pp_pro_nonce', 'pp_pro_nonce' ) )
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $license = trim( get_option( 'pp_pro_license_key' ) );

        // data to send in our API request
        $api_params = array(
                'edd_action'=> 'activate_license',
                'license' 	=> $license,
                'item_name' => urlencode( PP_PRO_ITEM_NAME ), // the name of our product in EDD
                'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( PP_PRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
                return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "valid" or "invalid"

        update_option( 'pp_pro_license_status', $license_data->license );
    }
}
add_action('admin_init', 'pp_pro_activate_license');

/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/

function pp_pro_deactivate_license() {

    // listen for our activate button to be clicked
    if( isset( $_POST['pp_pro_license_deactivate'] ) ) {

        // run a quick security check
        if( ! check_admin_referer( 'pp_pro_nonce', 'pp_pro_nonce' ) )
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $license = trim( get_option( 'pp_pro_license_key' ) );

        // data to send in our API request
        $api_params = array(
                'edd_action'=> 'deactivate_license',
                'license' 	=> $license,
                'item_name' => urlencode( PP_PRO_ITEM_NAME ), // the name of our product in EDD
                'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( PP_PRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "deactivated" or "failed"
        if( $license_data->license == 'deactivated' )
            delete_option( 'pp_pro_license_status' );
    }
}
add_action('admin_init', 'pp_pro_deactivate_license');

/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function pp_pro_check_license() {

    global $wp_version;

    $license = trim( get_option( 'pp_pro_license_key' ) );

    $api_params = array(
            'edd_action'    => 'check_license',
            'license'       => $license,
            'item_name'     => urlencode( PP_PRO_ITEM_NAME ),
            'url'           => home_url()
    );

    // Call the custom API.
    $response = wp_remote_post( PP_PRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

    if ( is_wp_error( $response ) )
        return false;

    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    if( $license_data->license == 'valid' ) {
        echo 'valid'; exit;
        // this license is still valid
    } else {
        echo 'invalid'; exit;
        // this license is no longer valid
    }
}

add_filter('widget_text', 'do_shortcode');

add_action('admin_enqueue_scripts','wp_payapl_admin_scripts');
function wp_payapl_admin_scripts(){
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery');
    wp_enqueue_style('wp-paypal-css',plugin_dir_url( __FILE__ )."includes/wp-paypal.css");
    wp_enqueue_style('thickbox');	
}
//====================
 
//================
function wp_paypal_products(){
    require 'products/manag-products.php';
}

function wp_paypal_responders(){
    require 'responders/manag-responders.php';
}

function wp_paypal_settings(){
    require 'includes/settings.php';
}

add_action( 'admin_init', 'register_wp_paypal_settings' );
add_action( 'admin_init', 'register_wp_paypal_settings1' );
function register_wp_paypal_settings() {
    register_setting( 'baw-settings-group', 'is_test' );
    register_setting( 'baw-settings-group', 'paypalID' );
    register_setting( 'baw-settings-group', 'currency' );
    register_setting( 'baw-settings-group', 'upload_image' );
    register_setting( 'baw-settings-group', 'return_url' );
}

function register_wp_paypal_settings1() {
    register_setting( 'baw-settings-group1', 'email_subject' );
    register_setting( 'baw-settings-group1', 'email_message' );
    register_setting( 'baw-settings-group1', 'from_email' );
}

function wp_paypal_product($atts){
    extract(shortcode_atts(array(
	'prodname' => 'no value'
    ),$atts) );
    
    global $wpdb;
    $table              =	$wpdb->prefix.'paypal_products';
    $prodname           =	str_replace('-',' ',$prodname);
    $shorcode_product	=	$wpdb->get_row("SELECT * FROM $table WHERE product_name ='$prodname'");
    $product_name	=	$shorcode_product->product_name;
    $product_price	=	$shorcode_product->product_price; 
    $return_url		=	$shorcode_product->return_url;
    $responderID	=	$shorcode_product->responder;
    $paypalID		=	get_option('paypalID');
    $currency		=	get_option('currency');
    $upload_image	=	get_option('upload_image');
    $email_subject	=	get_option('email_subject');
    $email_message	=	get_option('email_message');
    
    if(!$upload_image){
	$upload_image	=	'http://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif';	
    }
    
    if((get_option('is_test'))=="1"){
        $output	=   '<form name="_xclick" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                            <input type="hidden" name="cmd" value="_xclick">
                            <input type="hidden" name="business" value="'.$paypalID.'">
                            <input type="hidden" name="return" value="'.$return_url.'">
                            <input type="hidden" name="currency_code" value="'.$currency.'">
                            <input type="hidden" name="item_name" value="'.$product_name.'">
                            <input type="hidden" name="amount" id="p'.$product_id.'" value="'.$product_price.'">
                            <input type="hidden" name="custom" value="'.$responderID.'">
                            <input name="notify_url" value="'.plugin_dir_url( __FILE__ ).'ipn_sandbox.php" type="hidden">
                            <input type="image" src="'.$upload_image.'" border="0" name="submit" alt="Make payments with PayPal - its fast, free and secure!"> 
                        </form>';

        $output .= "<script>jQuery(document).ready(function(){var a=".$product_price.";jQuery('form[name=_xclick]').submit(function(c){var b=jQuery('input[id=p".$product_id."]').val();if(b==a){return}else{c.preventDefault()}})});</script>";
    }
    
    if((get_option('is_test'))!="1"){
	$output     =	'<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                            <input type="hidden" name="cmd" value="_xclick">
                            <input type="hidden" name="business" value="'.$paypalID.'">
                            <input type="hidden" name="return" value="'.$return_url.'">
                            <input type="hidden" name="currency_code" value="'.$currency.'">
                            <input type="hidden" name="item_name" value="'.$product_name.'">
                            <input type="hidden" name="amount" id="p'.$product_id.'" value="'.$product_price.'">
                            <input type="hidden" name="custom" value="'.$responderID.'">
                            <input name="notify_url" value="'.plugin_dir_url( __FILE__ ).'ipn.php" type="hidden">
                            <input type="image" src="'.$upload_image.'" border="0" name="submit" alt="Make payments with PayPal - its fast, free and secure!"> 
			</form>';
        
        $output .= "<script>jQuery(document).ready(function(){var a=".$product_price.";jQuery('form[name=_xclick]').submit(function(c){var b=jQuery('input[id=p".$product_id."]').val();if(b==a){return}else{c.preventDefault()}})});</script>";
    }
    
    return $output;		
}
add_shortcode('wp-paypal-product','wp_paypal_product');
