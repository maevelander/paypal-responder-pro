<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>General Settings</h2>
    
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#upload_image_button').click(function() {
                formfield = jQuery('#upload_image').attr('name');
                tb_show('', 'media-upload.php?type=image&TB_iframe=true');
                return false;
            });
	
            window.send_to_editor = function(html) {
                imgurl = jQuery('img',html).attr('src');
                jQuery('#upload_image').val(imgurl);
                tb_remove();
            }
        });
    </script>
    <?php
        if ( isset( $_GET['settings-updated'] ) ) {
            echo "<div class='updated' style='margin-top:10px;'><p> Paypal Responder Pro settings updated successfully</p></div>";
        }
    ?>
 
    <table class="wp-paypal" style="margin-top:20px;"> 
        <tr>
            <td valign="top" width="76%" style="padding-right:20px;">
            <table class="widefat posts" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col">Paypal Configuration</th>	
                    </tr>
                </thead>
                <tbody id="the-list">
                    <tr valign="top">
                        <td>
                        <form name="frm" method="post" action="options.php">
                        <?php settings_fields( 'baw-settings-group' ); ?>
                    	<table class="inner-setings">
                            <tr>
                                <td width="150" style="line-height:27px;">Testing Mode</td>
                                <td>
                                    <input type="checkbox" name="is_test" value="1" <?php if((get_option('is_test'))!="") {?> checked="checked" <?php } ?>  />
                                    <span>Check this if you want to enable testing mode </span>
                                </td>
                            </tr>
                            <tr>
                            	<td width="150" style="line-height:27px;">Paypal ID</td>
                                <td><input type="text" name="paypalID" value="<?php echo get_option('paypalID'); ?>"  />
                                    <span>Paypal account where you will receive payments</span>
                                </td>
                            </tr>
                            <tr>
                            	<td width="150" style="line-height:27px;">Currency</td>
                                <td>
                                    <select name="currency">
                                	<option value="AUD" <?php if(get_option('currency')=='AUD'){echo 'selected="selected"';} ?>>Australian Dollars</option>
                                        <option value="CAD" <?php if(get_option('currency')=='CAD'){echo 'selected="selected"';} ?>>Canadian Dollars</option>
                                        <option value="CHF" <?php if(get_option('currency')=='CHF'){echo 'selected="selected"';} ?>>Swiss Franc</option>
                                        <option value="CZK" <?php if(get_option('currency')=='CZK'){echo 'selected="selected"';} ?>>Czech Koruna</option>
                                        <option value="DKK" <?php if(get_option('currency')=='DKK'){echo 'selected="selected"';} ?>>Danish Krone</option>
                                        <option value="EUR" <?php if(get_option('currency')=='EUR'){echo 'selected="selected"';} ?>>Euros</option>
                                        <option value="GBP" <?php if(get_option('currency')=='GBP'){echo 'selected="selected"';} ?>>Pounds Sterling</option>
                                        <option value="HKD" <?php if(get_option('currency')=='HKD'){echo 'selected="selected"';} ?>>Hong Kong Dollar</option>
                                        <option value="HUF" <?php if(get_option('currency')=='HUF'){echo 'selected="selected"';} ?>>Hungarian Forint</option>
                                        <option value="ILS" <?php if(get_option('currency')=='ILS'){echo 'selected="selected"';} ?>>Israeli Shekel</option>
                                        <option value="JPY" <?php if(get_option('currency')=='JPY'){echo 'selected="selected"';} ?>>Japanese Yen</option>
                                        <option value="MXN" <?php if(get_option('currency')=='MXN'){echo 'selected="selected"';} ?>>Mexican Peso</option>
                                        <option value="NOK" <?php if(get_option('currency')=='NOK'){echo 'selected="selected"';} ?>>Norwegian Krone</option>
                                        <option value="NZD" <?php if(get_option('currency')=='NZD'){echo 'selected="selected"';} ?>>New Zealand Dollar</option>
                                        <option value="PLN" <?php if(get_option('currency')=='PLN'){echo 'selected="selected"';} ?>>Polish Zloty</option>
                                        <option value="SEK" <?php if(get_option('currency')=='SEK'){echo 'selected="selected"';} ?>>Swedish Krona</option>
                                        <option value="SGD" <?php if(get_option('currency')=='SGD'){echo 'selected="selected"';} ?>>Singapore Dollar</option>
                                        <option value="USD" <?php if(get_option('currency')=='USD'){echo 'selected="selected"';} ?>>United States Dollars</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                            	<td width="150" style="line-height:27px;">Buy Button Image</td>
                                <td>
                                    <input id="upload_image" type="text" name="upload_image" value="<?php echo get_option('upload_image'); ?>" />
                                    <input id="upload_image_button" type="button" value="Upload Image" class="button-primary" />
                                    <span>Upload a custom button image here or leave blank to use payapl default Buy Now</span>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
   			</p>
                        </form>
                        </td>
                    </tr>
                </tbody>
            </table>
            </td>
            <td valign="top" width="28%">
            <table cellpadding="0" class="widefat donation" style="margin-bottom:10px; border:solid 2px #008001;" width="50%">
            	<thead>
                    <th scope="col"><strong style="color:#008001;">Help Improve This Plugin!</strong></th>
                </thead>
                <tbody>
                    <tr>
                    	<td style="border:0;">
                            Enjoyed this plugin? All donations are used to improve and further develop this plugin. Thanks for your contribution.
                        </td>
                    </tr>
                    <tr>
                    	<td style="border:0;">
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="A74K2K689DWTY">
                            <input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
                            <img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
			</form>
                        </td>
                    </tr>
                    <tr>
                    	<td style="border:0;">You can also help by
                            <a href="http://wordpress.org/extend/plugins/paypal-responder/" target="_blank">rating this plugin on wordpress.org</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" class="widefat" border="0">
            	<thead>
                    <th scope="col">Need Support?</th>
                </thead>
                <tbody>
                    <tr>
                    	<td style="border:0;">
                            If you are having problems with this plugin please talk about them on the
                            <a href="http://wordpress.org/support/plugin/paypal-responder" target="_blank">Support Forum</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            </td>
        </tr>
    </table>
</div>
