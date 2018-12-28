<?php
/**//** ----= CLASS mwRMEditorEx_status	=------------------------------------------------------------------------------\**//** \
*
*
*
\**//** -----------------------------------------------------------------------------= by Alex @ Morad Media Inc. =----/** //**/
class mwRMEditorEx_status extends mwRMEditorEx
{

	public	$tabs	= [
		'approval'	=> 'Approval',
	]; //$tabs

	function editor_approval () {
		
		//__($this->application->statusList);
		//__($this->data);
		
		//$this->load->model('rmCfg'); // how to load lib?
		
		$statuses = rmCfg()->getStatuses();
		$statuses = array_merge([0 => 'Select Status'], $statuses);
		//__($statuses);
		
		?>
		
		<div id="postHistory" class="winContent">
			<table class="mwDialog tall">
				<tr><th>Current Status: <span id="regmgr_approve_text"></span></th></tr>
				<tr><td>
					<input hint="Approve application" class="Hi full" type="button" value="Approve" onClick="regmgr_update_approve('approved')" />
					<input hint="Decline application" class="Red full" type="button" value="Decline" onClick="regmgr_update_approve('declined');"/>
					<!--input id="regmgr_approval_value" type="hidden" name="approval_value" /-->
				</td></tr>
				
				<tr><th>Custom Statuses:</th></tr>
				<tr><td>
					<select name="status_minor" id="regmgr_minor_status" onChange="jQuery('[name=status_minor]').val(jQuery(this).val());">
					<?foreach($statuses as $k => $v):?>
						<option value="<?=$k?>"><?=$v?></option>
					<?endforeach;?>
					</select>
				</td></tr>
			</table>
		</div>
		<script>
		jQuery(function(){
			//___(11111);
			var update_text = function() {
				
				var val = jQuery('[name=statusmajor]').val();
				
				var text = jQuery('#regmgr_approve_text');
				
				var statuses = {
					'new'		: 'New',
					'open'		: 'Open',
					'submit'	: 'Submit',
					'ready'		: 'Ready',
					'approved'	: 'Approved',
					'declined'	: 'Declined',
					'closed'	: 'Closed',
				};
				
				text.html(statuses[val]);
					
			};
			
			jQuery('[rel=status_approval]').off().click(function(){
				
				//update major status text
				update_text();
				
			});
			
			regmgr_update_approve = function(val) {
				
				jQuery('[name=statusmajor]').val(val);
				update_text();
				
			};
			
		});
		</script>
		<?php
	} //FUNC editor_approval

} //CLASS mwRMEditorEx_status