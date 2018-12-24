<?php


class PageBlock_RegMgr extends mwPageBlock {

// ---- SETTINGS ----

	public	$Caption		= 'RegMgr';
	public	$Description		= 'Registration Manager';
	public	$IconClass		= 'Form';

	public	$gridHidden		= false; 		// Set TRUE to hide block from pallete grid.

// ---- Resources -----

// ---- FIELDS ----

	public	$template		= 'application.php';
	public	$type			= '';

// --- Privilegies ---

	function __construct () {
		parent::__construct();
	} //CONSTRUCTOR

	function render () {

		// Setting up uri for loader
		$this->load->uri = $this->Page->uri;

		// Using controllers for uri processing
		list( $obj, $method, $params ) = $this->load->controllerURI(CMS_FRONTEND, 2, false);

		// Initialising object
		$obj->wgt	= $this;

		// Calling specified method.
		$html = call_user_func_array( [$obj, '_ob_'.$method], $params );

		// Parsing embedded contents
		$html = $this->parseCommon($html);
/*
		// Parsing custom vars in content
		// Using vTpl to support nested vars
		// Always calling, to make sure unknown/unused vars are cleared
		$tpl = new vTpl2($html);
		$tpl
			->varWrap('[]')
			->parse()
			->vars($obj->contentVars)
			// Can't clear vars, as it will remove some intended stuff
			// ToDo: implement contents targeted parsing
			//	->cleanVars()
		; //$tpl

		$html = $tpl->html();
*/
		return $html;

	}// render()

	function editor() {
		echo 'no functions yet';

//		todo: add select with types

	} //editor()

}//CLASS mwPageBlock_ReqMgr