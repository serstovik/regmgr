<?php
/** //** ----= CLASS mwIndex	=--------------------------------------------------------------------------------------\**//** \
*
* 	Default regMgr controller. Manages applications history, and addon sections.
*
* 	@package	Morweb
* 	@subpackage	regMgr
* 	@category	controller
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwIndex extends mwController {

	function __init	() {

		$this->load->model('rmApplication');
		$this->load->model('rmCfg');
	
	} //FUNC init

	function index () {
	
		// As current replacement for app history - redirect to last user application.
		// Eventually this should be replaced with aplications history functionality.

		// Checking if user is in, getting ID, looking for last application
		// Loading it from DB, and generating url
		
		// ToDo: add support for application extensions
		
		// Getting user ID
		$uId	= User::ID();
	
		// Nothing to do if unlogged.
		// ToDo: redirect or message instead.
		if ( !$uId )
			return;

		$app	= new rmApplication();
		
		// Composing query manually to simplify. This is temporary anyway.
		// Ordering by ID to get latest application, since ID is autoincrement and is indexed.
		// Need only type and ID
		$sql	= "
			SELECT `id`, `type`
			FROM `{$app->Table}`
			WHERE `user_id` = {$uId}
			ORDER BY `id` DESC
			LIMIT 1
		";
	
		$row	= mwDB()->query($sql)->asRow();

		// If no application found - redirecting to new application
		if ( empty($row['id']) ) {
			
			// Since don't know which application should be used - using first application from config for yet
			// In most cases there is only one type anyway
			// Normal UI should be implemented instead of this anyway
			$types	= rmCfg()->getTypes();
			reset($types);
			$type	= key($types); 

			$url = fullURL( compileURL($this->wgt->Page->url, 'application', $type) );

			redirect($url);
			
			return;
			
		} //IF no applications

		$url = fullURL( compileURL($this->wgt->Page->url, 'application', $row['type'], $row['id']) );

		// Have enough to compose url and redirect onto it
		redirect($url);
	
	} //FUNC index

} //CLASS mwIndex
