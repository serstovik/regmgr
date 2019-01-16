<?php
mwLoad('regmgr')->widget('RMEditorEx', 'status');
/**//** ----= CLASS mwRMEditorEx_prStatus	=----------------------------------------------------------------------\**//** \
*
*
*
\**//** -----------------------------------------------------------------------------= by Alex @ Morad Media Inc. =----/** //**/
class mwRMEditorEx_prStatus extends mwRMEditorEx_status
{
	
	function editor() {
		
		//__($this->application->extensions['eShop']['items']);
		
		$statuses = rmCfg()->getStatuses();
		//__($this->cfg);
		//$cfg = rmCfg()->getBranch('backend', 'editor');
		
		//default statuses names
		$coreDefault = [
			'new'		=> 'New',
			'open'		=> 'Open',
			'submit'	=> 'Submit',
			'ready'		=> 'Ready',
			'approved'	=> 'Approved',
			'declined'	=> 'Declined',
			'closed'	=> 'Closed',
		];
		
		foreach($coreDefault as $k => $v){
			
			//check is core status exists in config
			if ( !empty($statuses[$k]) ) {
				
				//use caption from config
				$coreList[$k] = $statuses[$k];
				
				//remove core status from config list
				unset($statuses[$k]);
				
			}
			else {
				
				//use default caption
				$coreList[$k] = $coreDefault[$k];
				
			}
		}
		
		$statuses = array_merge([0 => 'Select Status'], $statuses);
		//__($statuses);
		
		// Decoding items data
		$cart = json_decode($this->application->extensions['eShop']['cart'], true);
		//__($cart);
		
		//custom tab
		$custom_html = '';
		if( !empty($this->cfg['template']) ) {
			
			$file = compilePath(SITE_TEMPLATES_PATH, 'regmgr', $this->cfg['template']);
			//$body = loadView($file, $data);
			$custom_html = loadView($file, []);
			
		}
		
		?>
		
		<div id="postHistory" class="winContent">
			<table class="mwDialog tall">
				<tr><th>Current Application Status: <b><span id="regmgr_approve_text"></span></b></th></tr>
				<?php if( !empty($cart) ): ?>
				<tr><th>Select Pet to Approve:</th></tr>
				<tr><td>
					<select class="approve-products-list" name="product">
						<option value="">--</option>
						<?php foreach($cart as $v): ?>
						<option value="<?=$v['id']?>"><?=$v['item']['title']?></option>
						<?php endforeach;?>
					</select>
				</td></tr>
				<?php endif; ?>
				<tr><td>
					<input hint="Approve application" class="Hi full cell-60 approve-btn" type="button" value="Approve" onClick="regmgr_update_approve('approved')" />
					<input hint="Decline application" class="Red full cell-40" type="button" value="Decline" onClick="regmgr_update_approve('declined');"/>
					<!--input id="regmgr_approval_value" type="hidden" name="approval_value" /-->
				</td></tr>
				<?php if( sizeof($statuses) > 1 ):?>
				<tr><th>Application Status:</th></tr>
				<tr><td>
					<select name="status_minor" id="regmgr_minor_status" onChange="jQuery('[name=status_minor]').val(jQuery(this).val());">
					<?foreach($statuses as $k => $v):?>
						<option value="<?=$k?>"><?=$v?></option>
					<?endforeach;?>
					</select>
				</td></tr>
				<?php endif;?>
				<?=$custom_html?>
			</table>
		</div>
		<script>
		jQuery(function(){
			//___(11111);
			
			var update_text = function() {
				
				var val = jQuery('[name=status_major]').val();
				
				var text = jQuery('#regmgr_approve_text');
				
				var statuses = <?=json_encode($coreList)?>;
				
				text.html(statuses[val]);
				
				/*
				if ( jQuery('[name=status_major]').val() == 'approved' || !jQuery('.approve-products-list').val() ) {
					___(1111);
					jQuery('.approve-btn').addClass('disabled');
				
				}
				else {
					___(2222);
					jQuery('.approve-btn').removeClass('disabled');
					
				}
				*/
			};
			
			jQuery('[rel=tab_status]').off().click(function(){
				
				//update major status text
				update_text();
				
			});
			
			/*
			jQuery('.approve-products-list').off().change(function(e){
				___(333, jQuery(this).val());
				if ( jQuery(this).val() )
					jQuery('.approve-btn').removeClass('disabled');
				else
					jQuery('.approve-btn').addClass('disabled');
				
			});
			*/
			
			regmgr_update_approve = function(val) {
				
				//___(444, jQuery('.approve-products-list').val());
				//if ( !jQuery('.approve-btn').hasClass('disabled') ) {
				//if ( jQuery('.approve-products-list').val() ) {
					
					jQuery('[name=status_major]').val(val);
					update_text();
					
				//}
				
			};
			
		});
		</script>
		<?php
		
	} //FUNC editor
	
	function editor1 () {
		
		//__($this->application->statusList);
		//__($this->data);
		
		//$this->load->model('rmCfg'); // how to load lib?
		
		$statuses = rmCfg()->getStatuses();
		
		//default statuses names
		$coreDefault = [
			'new'		=> 'New',
			'open'		=> 'Open',
			'submit'	=> 'Submit',
			'ready'		=> 'Ready',
			'approved'	=> 'Approved',
			'declined'	=> 'Declined',
			'closed'	=> 'Closed',
		];
		
		foreach($coreDefault as $k => $v){
			
			//check is core status exists in config
			if ( !empty($statuses[$k]) ) {
				
				//use caption from config
				$coreList[$k] = $statuses[$k];
				
				//remove core status from config list
				unset($statuses[$k]);
				
			}
			else {
				
				//use default caption
				$coreList[$k] = $coreDefault[$k];
				
			}
		}
		
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
				
				<?php if( sizeof($statuses) > 1 ):?>
				<tr><th>Custom Statuses:</th></tr>
				<tr><td>
					<select name="status_minor" id="regmgr_minor_status" onChange="jQuery('[name=status_minor]').val(jQuery(this).val());">
					<?foreach($statuses as $k => $v):?>
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
				
				var statuses = <?=json_encode($coreList)?>;
				
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

} //CLASS mwRMEditorEx_prStatus