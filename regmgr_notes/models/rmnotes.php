<?php
/** //** ----= CLASS rmNotes	=--------------------------------------------------------------------------------------\**//** \
 *
 * @package        	Morweb
 * @subpackage		RegMgr
 * @category
 *
 * \**//** ---------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/

 class rmNotes extends vDBObject {

 	// ---- SETTINGS ----

	public	$Table			= 'regmgr_notes'; 	// DB table to use.

// ---- FIELDS ----

	public	$id			= 0;				// DB id.
	public	$appID			= 0;				// Application ID.
	public	$created		= '';				// |- Creation and last modification dates.
	public	$modified		= '';				// |
	public	$userID			= 0;				// |- User ID.
	public	$text			= '';				// |


 	function __construct () {

		$this

			->setField('id')->Validations('isID')
			->setField('appID', 'app_id')->Validations('isID')
			->setField('created')->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, 'CURRENT_TIMESTAMP', false)
			->setField('modified')->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, true, true)
			->setField('userID', 'user_id')->Validations('isID')
			->setField('text')->Validations('trim|cleanXSS')

		; //OBJECT $this

	} //CONSTRUCTOR

 	function saveNote($appID, $userID, $text){
 		$sql	= "UPDATE {$this->Table} SET `text` = '{$text}' WHERE `app_id` = '{$appID}' AND `user_id` = '{$userID}'";
 		mwDB()->query($sql);
	} //FUNC saveNote

	function getNotesByAppId($appId){
 		
 		$sql	= "SELECT * FROM {$this->Table} WHERE `app_id` = {$appId}";
 		$notes	= mwDB()->query($sql)->asArray();
 		//adding users to the notes
 		return $this->addUsersToNotes($notes);
 		
	} //FUNC getNotesById

	//return singe note
	function getNoteById($noteId){
		$sql		= "SELECT * FROM {$this->Table} WHERE `id` = {$noteId}";
		$noteData	= mwDB()->query($sql)->asRow();
		return $this->addUsersToNotes($noteData, false);
	} //FUNC getNoteById

	function updateNoteById($noteId, $newText){
		$sql	= "UPDATE {$this->Table} SET `text`= '{$newText}' WHERE `id` = {$noteId}";
		mwDB()->query($sql);
	} //FUNC updateNoteById

	function removeNote($noteId){
		$sql	= "DELETE FROM {$this->Table} WHERE `id` = {$noteId}";
		mwDB()->query($sql);
	} //FUNC removeNote
	 
	function addUsersToNotes($notes = array(), $isList = true){
		
		//getting all existing users
		$sql	= 'SELECT id, login, email FROM `users`';
		$usersData	= mwDB()->query($sql)->asArray();
		if($isList){
			
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
			
		}
		else {
			foreach($usersData as $user){
				if($notes['user_id'] == $user['id']){
					$notes['user_data']	= $user;
					break;
				}
				else {
					$notes['user_data']	= null;
				}//if
			}//foreach
		}//if
		
		return $notes;		
		
	}//func addUsersToNotes	

 } //CLASS rmNotes