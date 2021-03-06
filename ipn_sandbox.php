<?php
// STEP 1: Read POST data
// reading posted data from directly from $_POST causes serialization 
// issues with array data in POST
// reading raw POST data from input stream instead. 

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);

$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode ('=', $keyval);
    if (count($keyval) == 2)
        $myPost[$keyval[0]] = urldecode($keyval[1]);
}
    
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

if(function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
}

foreach ($myPost as $key => $value) {        
    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
        $value = urlencode(stripslashes($value)); 
    } else {
        $value = urlencode($value);
    }
    $req .= "&$key=$value";
}

$path = $_SERVER['DOCUMENT_ROOT'];

// STEP 2: Post IPN data back to paypal to validate
$ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if( !($res = curl_exec($ch)) ) {
    // error_log("Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit; 
}

curl_close($ch);
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;
if (strcmp ($res, "VERIFIED") == 0) {

    $item_name          =   $_POST['item_name'];
    $item_number        =   $_POST['item_number'];
    $payment_status     =   $_POST['payment_status'];
    $payment_amount     =   $_POST['mc_gross'];
    $payment_currency   =   $_POST['mc_currency'];
    $txn_id             =   $_POST['txn_id'];
    $receiver_email     =   $_POST['receiver_email'];
    $payer_email        =   $_POST['payer_email'];
    $responderID        =   $_POST['custom'];
    $name               =   $_POST['first_name'];
    $payment_status     =   $_POST['payment_status'];
    $site_url           =   get_bloginfo('wpurl');
    $table_resp         =	$wpdb->prefix.'paypal_responders';
    $responder_to_use   =	$wpdb->get_row("SELECT * FROM $table_resp WHERE id ='$responderID'");

    $subject        =   $responder_to_use->subject;
    $from		=   $responder_to_use->from_email;

    $attachment     =   $responder_to_use->attachment;

    $att_secure     =   $responder_to_use->att_secure;
    $message	.=  $responder_to_use->message_body;
    $message        .=  '<br /><br />
                        <a title="Click here to download Attachment" href="'.plugin_dir_url(__FILE__).'responders/download.php?filename='.$att_secure.'" width="150" height="150" target="_blank">Click here to download Attachment</a>';	

    if($message){
        $message	=   str_replace('[item_name]',$item_name,$message);
        $message	=   str_replace('[txn_id]',$txn_id,$message);
        $message	=   str_replace(' [mc_gross]',$payment_amount,$message);
        $message	=   str_replace('[mc_currency]',$payment_currency,$message);
        $message	=   str_replace('[receiver_email]',$receiver_email,$message);
        $message	=   str_replace('[payer_email]',$payer_email,$message);
        $message	=   str_replace('[name]',$name,$message);
        $message	=   str_replace('[site_url]',$site_url,$message);
        $message	=   str_replace('[payment_status]',$payment_status,$message);
    }else{
        $message	=   'Dear '.$name.',
                        Thank you for your purchase from '.$site_url.'. The details of your purchase are below.
                        Transaction ID: '.$txn_id.'
                        Item Name: '.$item_name.'
                        Payment Amount: '.$payment_amount.'
                        Payment Amount: '.$payment_status.'
                        Paid to: '.$receiver_email.'
                        Thanks and Enjoy!
                        ~Enigma Digital <br /><br />
                        <a title="Click here to download Attachment" href="'.plugin_dir_url(__FILE__).'responders/download.php?filename='.$att_secure.'" width="150" height="150" target="_blank">Click here to download Attachment</a>';	
    }

    $table          =	$wpdb->prefix . "paypal_transactions";
    $txn_id_check	= 	$wpdb->get_results("SELECT * FROM $table WHERE txn_id ='$txn_id'");

    if(!$txn_id_check){ 
        $data	=   array(
                                'txn_id' 					=> $txn_id,
                                'product_name' 				=> $item_name,
                                'product_price' 			=> $payment_amount,
                                'payer_email' 				=> $payer_email,
                        );
        $wpdb->insert($table,$data) or die(mysql_error());
        $num        =   md5(time());
        $headers    .=  'From: ' .$from. "\r\n" . 'Reply-To: ' .$from . "\r\n";
        $headers    .=  'MIME-Version: 1.0' . "\r\n";
        $headers    .=  "Content-Type: text/html; charset=iso-8859-1 ";
        $headers    .=  "--".$num."--";

        //mail to buyer
        @mail( $payer_email , $subject, $message, $headers );
    }
}