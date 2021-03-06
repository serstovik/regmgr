<?php
/** //** ----= CLASS mwRMEditorEx_notes		=----------------------------------------------------------------------\**//** \
 *
 * 	@package        Morweb
 *	@subpackage	regMgr
 *	@category	widget extension
 *
 * \**//** ---------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
 class mwRMEditorEx_notes extends mwRMEditorEx
{

	public	$tabs	= [
		'notes'	=> 'Notes'
	]; //$tabs


	function editor_notes ($appId = '') {
		
		$this->load->model('rmnotes');
	 	$RMNotes	= new rmNotes();
	 	$RMNotes->createTable()->updateTable();

	 	if (empty($appId))
			$appId	= $this->application->id;
		
		$notes		= $RMNotes->getNotesByAppId($appId);
		
		if ($notes && is_array($notes)){
			
			$notes		= $this->addUsersToNotes($notes);
			
			$notesHtml	= '<section id="rmnotes_list">';

			foreach ($notes as $note){

				$notesHtml	.= "<dl  class='mwDialog' id='".$note['id']."'>";

				$notesHtml	.= '<dt class="rmnotes-user-data"><div class="rmnotes-user-data" style="float: left;"><strong>'.$note['user_data']['email'].'</strong></div><div style="float: right;" class="rmnotes-date">'.$note['modified'].'</div></dt>';
				//$notesHtml	.= '<dt class="rmnotes-date" style="float: right;"><strong>Date: </strong>'.$note['modified'].'</dt>';
				$notesHtml	.= '<dt class="rmnotes-text" style="height: auto;">'.$note['text'].'</dt>';
				
				$notesHtml	.= '<br/>';

				$notesHtml	.= '<div class="rmnotes-update-section '.$note['id'].'"><textarea name="update_note_'.$note['id'].' style="height: auto;""></textarea></div>';

				//$notesHtml	.= '<button rel="'.$note['id'].'" class="rmnotes-edit-btn '.$note['id'].'">Update</button>';
				//$notesHtml	.= '<button rel="'.$note['id'].'" class="rmnotes-remove-btn">Remove</button>';
				$notesHtml	.= '<hr />';

				$notesHtml	.= '</dl>';

			} //FOREACH

			$notesHtml	.= '</section>';

		}
		else {
			$notesHtml	= '<section id="rmnotes_list"></section>';
		}

	?>

		<dl class="mwDialog">

			<dt>Add Note:</dt>
			<dd><textarea name="admin_notes" rows="5" style="height: 100px; value=""></textarea></dd>
<?php
			/*
			<button rel="<?=$appId?>" class="regmgr-submit-note">Add</button>
			*/
?>
			<input rel="<?=$appId?>" class="regmgr-submit-note" type="button" value="Add" />
		</dl>
		<br>
		<hr>
		<br>
		
		<?=$notesHtml;?>
		
		<script type="text/javascript" src="/res/regmgr_notes/js/regmgr_notes.js"></script>
		<script type="text/javascript">
			//todo: move it to css file
			jQuery('.rmnotes-update-section').hide();
			setTimeout(function () {
				rmNotes();
			},300);
		</script>
	<?php

	} //FUNC editor_notes
	
	function addUsersToNotes($notes = array()){
		
		//getting all existing users
		$sql	= 'SELECT id, login, email FROM `users`';
		$usersData	= mwDB()->query($sql)->asArray();
		
		foreach($notes as $k => $note){
			foreach($usersData as $user){
				if($note['user_id'] == $user['id']){
					$notes[$k]['user_data']	= $user;
					break;
				}
				else {
					$notes[$k]['user_data']	= null;
				}//if
			}//foreach
		}//foreach
		
		return $notes;		
		
	}//func addUsersToNotes
	 
} //CLASS mwRMEditorEx_notes