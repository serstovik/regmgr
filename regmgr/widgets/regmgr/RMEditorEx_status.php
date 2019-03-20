<?php
/**//** ----= CLASS mwRMEditorEx_status	=------------------------------------------------------------------------------\**//** \
*
*
*
\**//** -----------------------------------------------------------------------------= by Alex @ Morad Media Inc. =----/** //**/
class mwRMEditorEx_status extends mwRMEditorEx
{

	function editor () {
		
		//__($this->application->statusList);
		//__($this->data);
		
		//$this->load->model('rmCfg'); // how to load lib?
		
		$statusesMajor = rmCfg()->getStatuses('', RM_STATUS_SCOPE_MAJOR);
		$statusesMinor = rmCfg()->getStatuses('', RM_STATUS_SCOPE_MINOR);
		
		$statusesMinor = array_merge([0 => 'Select Status'], $statusesMinor);
		//__($statuses);
		
		?>
		
		<div id="postHistory" class="winContent">
			<table class="mwDialog tall">
				<tr><th>Current Status: <span id="regmgr_approve_text"></span></th></tr>
				<tr><td>
					<input hint="Approve application" class="Hi full cell-60" type="button" value="Approve" onClick="regmgr_update_approve('approved')" />
					<input hint="Decline application" class="Red full cell-40" type="button" value="Decline" onClick="regmgr_update_approve('declined');"/>
					<!--input id="regmgr_approval_value" type="hidden" name="approval_value" /-->
				</td></tr>
				
				<?php if( sizeof($statusesMinor) > 1 ):?>
				<tr><th>Custom Statuses:</th></tr>
				<tr><td>
					<select name="status_minor" id="regmgr_minor_status" onChange="jQuery('[name=status_minor]').val(jQuery(this).val());">
					<?foreach($statusesMinor as $k => $v):?>
						<option value="<?=$k?>"><?=$v?></option>
					<?endforeach;?>
					</select>
				</td></tr>
				<?php endif;?>
			</table>
		</div>
		<script>
		jQuery(function(){
			//___(11111);
			
			var update_text = function() {
				
				var val = jQuery('[name=status_major]').val();
				
				var text = jQuery('#regmgr_approve_text');
				
				var statuses = <?=json_encode($statusesMajor)?>;
				
				text.html(statuses[val]);
					
			};
			
			jQuery('[rel=tab_status]').off().click(function(){
				
				//update major status text
				update_text();
				
			});
			
			regmgr_update_approve = function(val) {
				
				jQuery('[name=status_major]').val(val);
				update_text();
				
			};
			
		});
		</script>
		<?php
	} //FUNC editor

} //CLASS mwRMEditorEx_status