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

		$notes	= $this->getNotes($appId);
		//__($notes);
		if ($notes && is_array($notes)){

			$notesHtml	= '<section id="rmnotes_list">';

			foreach ($notes as $note){

				$notesHtml	.= "<dl  class='mwDialog' id='".$note['id']."'>";

				$notesHtml	.= '<dt class="rmnotes-date"><strong>Date:</strong> '.$note['modified'].'</dt>';
				$notesHtml	.= '<dt class="rmnotes-text"><strong>Note:</strong> '.$note['text'].'</dt>';
				$notesHtml	.= '<br/>';

				$notesHtml	.= '<div class="rmnotes-update-section '.$note['id'].'"><textarea name="update_note_'.$note['id'].'"></textarea></div>';

				//$notesHtml	.= '<button rel="'.$note['id'].'" class="rmnotes-edit-btn '.$note['id'].'">Update</button>';
				//$notesHtml	.= '<button rel="'.$note['id'].'" class="rmnotes-remove-btn">Remove</button>';
				$notesHtml	.= '<hr />';

				$notesHtml	.= '</dl>';

			} //FOREACH

			$notesHtml	.= '</section>';

		} // if

		echo $notesHtml;
	?>

		<dl class="mwDialog">

			<dt>Admin Notes</dt>
			<dd><textarea name="admin_notes" style="height: 200px;"></textarea></dd>

			<button rel="<?=$appId?>" class="regmgr-submit-note">Add</button>
		</dl>

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

	function getNotes($appId){

	 	mwLoad('regmgr_notes')->model('rmnotes');
	 	$RMNotes	= new rmNotes();
	 	
	 	$notes		= $RMNotes->getNotesByAppId($appId);
	 	
	 	if (!empty($notes))
	 		return $notes;

	 	return false;
	} //FUNC getNotes

} //CLASS mwRMEditorEx_notes