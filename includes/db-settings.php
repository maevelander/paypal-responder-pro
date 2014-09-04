<?php
//Database table versions
global $wp_paypal_prod_table;
$wp_paypal_prod_table = "1.2";
global $wp_paypal_resp_table;
$wp_paypal_resp_table = "1.3";
global $wp_paypal_txn_table;
$wp_paypal_txn_table = "1.2";
//Create database tables needed by the player
function wp_paypal_db(){
    wp_paypal_create_prod_table();
	wp_paypal_create_txn_table();
	wp_paypal_create_resp_table();
}
//Create prod table
function wp_paypal_create_prod_table(){
    //Get the table name with the WP database prefix
    global $wpdb;
    $table_name = $wpdb->prefix . "paypal_products";
    global $wp_paypal_prod_table;
    $installed_ver1 = get_option( "wp_paypal_prod_table" );
     //Check if the table already exists and if the table is up to date, if not create it
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name
            ||  $installed_ver1 != $wp_paypal_prod_table ) {
        $sql = "CREATE TABLE ".$table_name." (
				`id` INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`product_name` TEXT NOT NULL ,
				`product_price` TEXT NOT NULL ,
				`responder` TEXT NOT NULL ,
				`return_url` TEXT NOT NULL ,
				`shortcode` TEXT NOT NULL
				) ENGINE = MyISAM;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option( "wp_paypal_prod_table", $wp_paypal_prod_table );
}
   //Add database table versions to options
    add_option("wp_paypal_prod_table", $wp_paypal_prod_table);
}
//Create txn table
function wp_paypal_create_txn_table(){
    //Get the table name with the WP database prefix
    global $wpdb;
    $table_name = $wpdb->prefix . "paypal_transactions";
    global $wp_paypal_txn_table;
    $installed_ver1 = get_option( "wp_paypal_txn_table" );
     //Check if the table already exists and if the table is up to date, if not create it
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name
            ||  $installed_ver1 != $wp_paypal_txn_table ) {
        $sql = "CREATE TABLE " . $table_name . " (
              `id` INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `txn_id` TEXT NOT NULL,
			  `product_name` TEXT NOT NULL,
			  `product_price` TEXT NOT NULL,
			  `payer_email` TEXT NOT NULL
            ) ENGINE = MyISAM;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option( "wp_paypal_txn_table", $wp_paypal_txn_table );
}
    //Add database table versions to options
    add_option("wp_paypal_txn_table", $wp_paypal_txn_table);
}
//Create responder table
function wp_paypal_create_resp_table(){
    //Get the table name with the WP database prefix
    global $wpdb;
    $table_name = $wpdb->prefix . "paypal_responders";
    global $wp_paypal_resp_table;
    $installed_ver1 = get_option( "wp_paypal_resp_table" );
     //Check if the table already exists and if the table is up to date, if not create it
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name
            ||  $installed_ver1 != $wp_paypal_resp_table ) {
        $sql = "CREATE TABLE " . $table_name . " (
              `id` INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `resp_name` TEXT NOT NULL,
			  `from_email` TEXT NOT NULL,
			  `subject` TEXT NOT NULL,
			  `message_body` TEXT NOT NULL,
			  `attachment` TEXT NOT NULL,
			  `att_name` TEXT NOT NULL,
			  `att_secure` TEXT NOT NULL
            ) ENGINE = MyISAM;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option( "wp_paypal_resp_table", $wp_paypal_resp_table );
}
    //Add database table versions to options
    add_option("wp_paypal_resp_table", $wp_paypal_resp_table);
}