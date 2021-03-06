<?php
/** //** ----= CLASS mwRMEditorEx		 =---------------------------------------------------------------------\**//** \
*
*	Base model for application editor extensions.
*
* 	@package	Morweb
* 	@subpackage	regmgr
* 	@category	widget
*
\**//** ------------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
class mwRMEditorEx extends mwWidget {

	public	$extName	= '';		// Extension name. If omited - widget name will be used.

	public	$application	= false;	// Current application.
	public	$data		= [];		// Extension data.
	public	$cfg		= [];		// Config used to load extension

	function editor () {
	} //FUNC editor

	function validate ($data) {
		return $data;
	} //FUNC validate

	function save ($data) {
		return $data;
	} //FUNC save

	function initResources () {
	} //FUNC initResources

} //CLASS mwRMEditorEx