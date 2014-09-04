<div class="wrap">

<div id="icon-options-general" class="icon32"><br></div><h2>Manage Responders</h2>


<?php

	require 'process.php';

 ?>

 <?php if($resp){    echo "<div class='updated' style='margin-top:10px;'><p>$resp </p></div>";

} ?>

 <table class="inner-setings" style="margin-top:20px;"> 

 

 <?php if($action=='update'): ?>

    <form name="frm1" method="post" enctype="multipart/form-data">

     <tr height="60">

		<td width="120"><strong>Responder Name</strong></td>

        <td><input name="resp_name" type="text" id="resp_name" value="<?php echo $singleresp -> resp_name; ?>" /></td>

	</tr>

    <tr height="60"> 

		<td width="120"><strong>From Email</strong></td>

        <td><input name="from_email" type="text" id="from_email" value="<?php echo $singleresp -> from_email; ?>"  /></td>

	</tr>

    <tr height="60">  

		<td width="120"><strong>Email Subject</strong></td>

        <td><input name="subject" type="text" id="subject" value="<?php echo $singleresp -> subject; ?>"  /></td>

	</tr>

    <tr height="60">

    	<td width="120" style="vertical-align:top;"><strong>Attachment</strong></td>

        <td><input type="file" name="file" id="file" /><span>Upload a new file. PS. This latest file will be sent as attachment in responder mail.</span>

       <?php if(($singleresp -> att_name) !="" ) { ?> <p> Previously you uploaded file named <strong><?php echo $singleresp -> att_name ?></strong></p><?php }?></td>

    </tr>

    <tr height="60"> 

		<td width="120" valign="top"><strong>Message</strong></td>

        <td><textarea name="message_body" id="message_body" style="height:150px;"><?php echo $singleresp -> message_body; ?></textarea></td>

	</tr>

    <tr>

        <td colspan="2" style="line-height:27px;">Enter the email that is sent to users after completing a successful purchase. HTML is accepted. Available template tags:<br /> Item Name: [item_name]<br />Transaction ID: [txn_id]<br /> Payment Amount: [mc_gross]<br /> Seller ID: [receiver_email]<br />Buyer ID: [payer_email]<br />Payment Currency : [mc_currency]<br />Site Url : [site_url]</td>

    </tr>

    

    <tr>

    	<td><input name="updateresp" type="submit" class="button-primary"  value="<?php _e('Update Responder') ?>" /></td>

        <td><?php if($prod): ?><div class="success">Success</div><?php elseif($eprod): ?><div class="errorr">All fields are required</div><?php endif; ?></td>

    </tr>

    </form>

 <?php else: ?>

 <form name="frm1" method="post" enctype="multipart/form-data">

 <tr height="60">

		<td width="120"><strong>Responder Name</strong></td>

        <td><input type="text" name="resp_name" /></td>

	</tr>

    <tr height="60"> 

		<td width="120"><strong>From Email</strong></td>

        <td><input type="text" name="from_email" /></td>

	</tr>

    <tr height="60">  

		<td width="120"><strong>Email Subject</strong></td>

        <td><input type="text" value="" name="subject"  /></td>

	</tr>

    <tr height="60">

    	<td width="120" style="vertical-align:top;"><strong>Attachment</strong></td>

        <td><input type="file" name="file" id="file" /><span>Upload a file (if necessary) that will be sent as an attachment (Only Zip and pdf files are allowed)</span></td>

    </tr>

    <tr> 

		<td width="120" valign="top"><strong>Message</strong></td>

        <td><textarea name="message_body" style="height:150px;"></textarea></td>

	</tr>

    <tr>

        <td colspan="2" style="line-height:27px;">Enter the email that is sent to users after completing a successful purchase. HTML is accepted. Available template tags:<br /> Item Name: [item_name]<br />Transaction ID: [txn_id]<br /> Payment Amount: [mc_gross]<br /> Seller ID: [receiver_email]<br />Buyer ID: [payer_email]<br />Payment Currency : [mc_currency]<br />Site Url : [site_url]</td>

    </tr>

    <tr>

    	<td><input type="submit"  class="button-primary" value="<?php _e('Add Responder') ?>" name="addresp" /></td>

        <td><?php if($prod): ?><div class="success">Success</div><?php elseif($eprod): ?><div class="errorr">All fields are required</div><?php endif; ?></td>

    </tr>

   </form>

 <?php endif; ?>

 </table>

  <table class="widefat posts" cellspacing="0" style="margin-top:20px;">

  	<thead>

    <tr>

        <th scope="col" width="30%"><a href="javascript:;">Responder</a></th>

        <th scope="col" width="30%"><a href="javascript:;">From</a></th>

        <th scope="col" width="20%"><a href="javascript:;">Subject</a></th>

        <th scope="col" width="35%"><a href="javascript:;">Attachment</a></th>

        <th scope="col" width="10%"><a href="javascript:;">Edit</a></th>	

        <th scope="col" width="10%"><a href="javascript:;">Delete</a></th>	

    </tr>

    </thead>

    <tfoot>

    <tr>

        <th scope="col" width="30%"><a href="javascript:;">Responder</a></th>

        <th scope="col" width="30%"><a href="javascript:;">From</a></th>

        <th scope="col" width="20%"><a href="javascript:;">Subject</a></th>

        <th scope="col" width="35%"><a href="javascript:;">Attachment</a></th>

        <th scope="col" width="10%"><a href="javascript:;">Edit</a></th>	

        <th scope="col" width="10%"><a href="javascript:;">Delete</a></th>		

    </tr>

    </tfoot>

    <tbody>

         <?php

		$sql = "SELECT * FROM $table";

		$results = $wpdb -> get_results($sql);

		?>

	<?php if( !empty( $results ) ) : ?>

    <?php foreach( $results as $result ): ?>

        <tr>

        	<td><?php echo $result -> resp_name; ?></td>             

            <td><?php echo $result -> from_email; ?></td>

            <td><?php echo $result -> subject; ?></td>

            <td><?php echo $result -> att_name; ?></td>

          <td><a href="admin.php?page=paypal_responders&action=update&id=<?php echo $result -> id; ?>">Update</a></td>

            <td><a href="admin.php?page=paypal_responders&action=delete&id=<?php echo $result -> id; ?>">Delete</a></td>

        </tr>

        <?php endforeach; ?>

	 

	<?php else: ?>

    <tr>

    	<td colspan="5">No Responders</td>

    </tr>

	<?php endif; ?>

    </tbody>

  </table>

  

</div>