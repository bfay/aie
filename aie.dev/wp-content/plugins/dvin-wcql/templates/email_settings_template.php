 <form id='settingsform' name='settingsform' method='POST'>
 <input type='hidden' name='action' id='action' value='preview_update'> <!--used for ajax call action -->
	<input type='hidden' name='plugin_weburl' id='plugin_weburl' value='<?php echo DVIN_QLIST_PLUGIN_WEBURL?>'>
		<table class='widefat dvin_table' cellpadding='5' cellspacing='2' width='100%'>
				<thead>
				<tr>
					<td colspan='2' style='text-align:left;'><h1><?php _e('Email Settings','dvinwcql')?></h1></td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?php _e('Admin Email','dvinwcql');?></td>
					<td>
						<input type="text" name="dvin_wcql_admin_email" value="<?php if(isset($dvin_wcql_admin_email)) echo $dvin_wcql_admin_email; else echo 'dummy_email@domainxxx.com';?>" style="width:525px;">
					</td>
				</tr>
				<tr>
					<td><?php _e('Send Copy to Requestor','dvinwcql');?></td>
					<td>
						<input type="checkbox" name="dvin_wcql_copy_toreq" />
					</td>
				</tr>
				<tr>
					<td><?php _e('Subject','dvinwcql');?></td>
					<td>
						<input type="text" name="dvin_wcql_email_subject" value="<?php if(isset($dvin_wcql_email_subject)) echo $dvin_wcql_email_subject;?>" style="width:525px;">
					</td>
				</tr>
				<tr>
					<td><?php _e('Body','dvinwcql')?></td>
					<td>
						<textarea name="dvin_wcql_email_msg" cols="80" rows="20" style="width:525px;"><?php if(isset($dvin_wcql_email_msg)) echo $dvin_wcql_email_msg;?></textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php echo __('Allowed tags','dvinwcql').':<br/>
					[%req_name%] 	-'.__('Filled in Name field in the form','dvinwcql').' <br/>
					[%req_email%] 	-'.__('Filled in Email field in the form','dvinwcql').' <br/>
					[%quotelist%]-'.__("User's Quotelist in table format",'dvinwcql').' <br/>
					[%total_price%]-'.__('Total Price','dvinwcql').' <br/>
					[%comments%]-'.__('Comments','dvinwcql');?>
									
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<center><input class='button-primary' type="submit" id="updateEmailSettings" name="updateEmailSettings" value="<?php _e('Update','dvinwcql');?>" /></center>
					</td>
				</tr>
			</table>
			
			
		<div id="lefttinycontainer">
			<div id="div_preview"></div>
		</div>
</form>
<script type='text/javascript'>
<?php
if( $dvin_wcql_copy_toreq == "on") {?>
jQuery('input[name=dvin_wcql_copy_toreq]').attr('checked', true);
<?php } ?>
</script>