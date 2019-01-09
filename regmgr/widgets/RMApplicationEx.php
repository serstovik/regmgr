<?php
/** //** ----= CLASS mwRMApplicationEx		 =---------------------------------------------------------------------\**//** \
*
*	Base model for application form extensions.
*
* 	@package	Morweb
* 	@subpackage	regmgr
* 	@category	widget
*
\**//** ------------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
class mwRMApplicationEx extends mwWidget {

	public	$extName	= '';		// Extension name. If omited - widget name will be used.

	public	$application	= false;	// Current application.
	public	$data		= [];		// Extension data.

	public	$tpl		= false;	// Callee template model.

	function render ($node) {
	} //FUNC render

	function validate () {
	} //FUNC validate

	function save () {
	} //FUNC save

} //CLASS mwRMApplicationEx