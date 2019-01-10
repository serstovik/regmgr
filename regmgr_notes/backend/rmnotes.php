<?php
/** //** ----= CLASS rmNotes	=--------------------------------------------------------------------------------------\**//** \
 *
 * @package		Morweb
 * @subpackage		RegMgr
 * @category
 *
 * \**//** ---------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/

class mwRmNotes extends mwController {

	function addNote(){

 		if (!isset($_POST['appId']) || empty($_POST['appId']))
 			return false;

 		$appId	= $_POST['appId'];
 		$text	= $_POST['text'];
 		$userId		= User::ID();

 		$this->load()->model('rmnotes');
 		$RMNotes	=  new rmNotes();
 		$RMNotes->createTable()->updateTable();

 		$notesData	= ['app_id' => $appId, 'user_id' => $userId, 'text' => $text];
		$res 		= $RMNotes->fromArray($notesData)->toDB();

		//$wgt	= mwLoad('regmgr')->widget('RMEditorEx', 'notes');
		//return $wgt->editor_notes($appId);
		//__($wgt);

		//return data to js
		if (isset($res->id))
			return $RMNotes->getNoteById($res->id);

	} //FUNC updateNote

	function updateNote() {

 		if (!isset($_POST['noteId']) || empty($_POST['noteId']))
 			return false;

 		$noteId	= $_POST['noteId'];

 		$this->load()->model('rmnotes');
 		$RMNotes	=  new rmNotes();

 		//checking for new text for note
		if (!empty($_POST['newText']))
			return $RMNotes->updateNoteById($noteId, $_POST['newText']);

 		$noteData	= $RMNotes->getNoteById($noteId);

 		if (!empty($noteData))
 			return $noteData;
		//todo: check userId if not equal with current then show warning

	} //FUNC updateNote

	function removeNote(){

		if (!isset($_POST['noteId']) || empty($_POST['noteId']))
 			return false;

 		$noteId	= $_POST['noteId'];

 		$this->load()->model('rmnotes');
 		$RMNotes	=  new rmNotes();
 		$RMNotes->removeNote($noteId);

	} //FUNC removeNote

 } //CLASS mwRmNotes