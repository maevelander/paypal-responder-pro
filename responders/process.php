<?php 

error_reporting(0);

@ini_set('display_errors', 0);

	if(isset($_GET['action'])){

	 $action	=	$_GET['action'];

	}

	if(isset($_GET['id'])){

	$id		=	$_GET['id'];	

	}

	

	global $wpdb;

	$table	=	$wpdb->prefix.'paypal_responders';

	

	

	

	if($action=='update'){

		

		

		if($_POST['updateresp']){

				$resp_name					=	$_POST['resp_name'];

				$from_email					=	$_POST['from_email'];

				$subject					=	$_POST['subject'];

				$message_body				=	$_POST['message_body'];

				$att_name                   =   $_FILES["file"]["name"];

				if(filter_var($from_email, FILTER_VALIDATE_EMAIL)) {

					

     			

				

				$dir = "../uploads";

				

				$ext = substr(strrchr($att_name,'.'),1);

					

						if ($ext == "pdf" || $ext == "zip" || $ext == "") {

				

				move_uploaded_file($_FILES["file"]["tmp_name"],$dir.'/'.basename($_FILES['file']['name']));

				chmod($dir.'/'.basename($_FILES['file']['name']), 0777);  // octal; correct value of mode

				$attachments = home_url().'/uploads/'.$_FILES["file"]["name"];

				

				if($ext!=""){

				$data	=	array(

				'resp_name' 				=> $resp_name,

				'from_email' 				=> $from_email,

				'subject' 					=> $subject,

				'message_body' 				=> $message_body,

				'attachment'                => $attachments,

				'att_name'                  => $att_name,



			);

			}

			else{

				$data	=	array(

				'resp_name' 				=> $resp_name,

				'from_email' 				=> $from_email,

				'subject' 					=> $subject,

				'message_body' 				=> $message_body,

				'attachment'                => $attachments,



			);

				}

				if(!empty($resp_name) && !empty($from_email) && !empty($subject) && !empty($message_body)){

				$ID		=	array('id'	=>	$id);

				$update			=	$wpdb->update($table,$data,$ID);

					$resp	=	"Responder Updated Successfullly";

				}else{

					$eresp	=	"All fields are required";	

				}

		}

		else {

						$resp = "Only Zip and pdf files are allowed";

					}

		}

		else {

			$resp = "Please enter valid email address";

			}

	}

		

	}

	elseif($action=='delete'){

	    $delete		=	$wpdb->query("DELETE FROM $table WHERE id='$id'");

	}else{

	

			if($_POST['addresp']){

				$resp_name					=	$_POST['resp_name'];

				$from_email					=	$_POST['from_email'];

				$subject					=	$_POST['subject'];

				$message_body				=	$_POST['message_body'];

				$att_name                   =   $_FILES["file"]["name"];
				

			if(filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
					
					
					$dir = "../uploads/";
					
					mkdir($dir);
					
					$file = '../uploads/.htaccess';
					$handle = fopen($file,'w');
					$data = "Options -Indexes";
					fwrite($handle,$data);
					fclose($handle);

					$ext = substr(strrchr($att_name,'.'),1);

					

				if ($ext == "pdf" || $ext == "zip" || $ext == ""){

				
				$fileBase = pathinfo($_FILES['file']['name'],PATHINFO_BASENAME);
				$fileExt = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
				$fileName = pathinfo($_FILES['file']['name'],PATHINFO_FILENAME);
				
				
				$fileHash = md5($fileBase.rand(2,9999)).'.'.$fileExt;
				
				
				move_uploaded_file($_FILES["file"]["tmp_name"],$dir.$fileHash);

				chmod($dir.'/'.basename($_FILES['file']['name']), 0777);  // octal; correct value of mode

				$attachments = home_url().'/uploads/'.$fileHash;

				//wp_mail('faisal.sarfraz@base29.com', $subject, $message, $headers, $attachments);
				
				//Secure File Name
				

				$data	=	array(

				'resp_name' 				=> $resp_name,

				'from_email' 				=> $from_email,

				'subject' 					=> $subject,

				'message_body' 				=> $message_body,

				'attachment'                => $attachments,

				'att_name'                  => $att_name,

				'att_secure'                => $fileHash

			);

				if(!empty($resp_name) && !empty($from_email) && !empty($subject) && !empty($message_body)){

	

				$insert		=	$wpdb->insert($table,$data) or die(mysql_error());

					$resp	=	"Responder Added Successfullly";

				}else{

					$eresp	=	"All fields are required";

				}

			}

			

					else {

						$resp = "Only Zip and pdf files are allowed";

					}

			}

			else{

						$resp = "Please Enter Valid email address";



				}

		}

	

	}

$singleresp	 = 	$wpdb->get_row("SELECT * FROM $table WHERE id ='$id'"); 

?>