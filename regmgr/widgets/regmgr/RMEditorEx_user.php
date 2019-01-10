<?php
/** //** ----= CLASS mwRMEditorEx_status	=----------------------------------------------------------------------\**//** \
*
* 	
*
* 	@package	morweb
* 	@subpackage	regmgr
* 	@category	plugin
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwRMEditorEx_user extends mwRMEditorEx {

	public	$user	= false;

	function editor_contact () {
		
		// Loading user, group and form
		$userId		= $this->application->userId;
		$user		= $this->loadUser($userId);

		// User row is loading with groups and forms already, so it's ready to use
		// User is also initiated, so it can be used to get data
		$data		= $user->asArray();
		
		// Clearing sensitive data
		foreach ( ['password', 'repass', 'hash', 'activation_code'] as $f )
			unset($data[$f]);

		// Merging data to make sure to keep source data
		$this->data	+= $data;

		// Using first loaded form. RegMgr is designed to keep users in one group 
		// (unless special user, which will be edited through users section anyway)
		$form		= reset($user->Forms);
		
		// Outputting form, wrapping it into mwDialog
		if ( !empty($form) ) {
	?>
		<div class="winContainer mwDialog"><?=$form->HTML()?></div>
	<?php
		}
		else {
	?>
		<div class="winContainer mwDialog">No user form found.</div>
	<?php
		}
		
	} //FUNC editor_contact

	function validate ($data) {
		
		if ( empty($_POST['user_id']) or !isID($_POST['user_id']) )
			throw( new Exception('Invalid user ID.') );

		$userId			= $_POST['user_id'];
		$data['id']		= $userId;
		
		// Loading user for better validations
		$user			= $this->loadUser($userId);

		// Validating user data
		if ( $v = $this->user->validate($data) )
			throw( new mwValidationEx('Wrong info provided.', $v) );
		
		return $data;
		
	} //FUNC validate

	function save ($data) {

		// Post is validated at this point, so it's safe to user values from there		
		// Loading by ID right away for data preserving and forms setup
		$user			= $this->loadUser($_POST['user_id']);

		// Saving user
		$user->fromArray($data)->save();
		
		// Clearing post data, as it does not need to save with application
		return [];
		
	} //FUNC save

	function loadUser ($id = 0) {

		// No need to laod user twice
		if ( $this->user and $this->user->ID == $id )
			return $this->user;

		// Creating new user object
		// Using userRow for faster environment setup
		// Although it's ineffective, this works fine
		mwLoad('system')->model('users');

		$this->user = new mwUserRow;

		// If ID provided - loading user data
		// This will also setup groups and forms
		if ( $id )
			$this->user->loadByID($id);
		
		return $this->user;
		
	} //FUNC loadUser

} //CLASS mwRMEditorEx_user