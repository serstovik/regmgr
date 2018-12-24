<?php

class PageBlock_RegMgrGrading extends mwPageBlock {

// ---- SETTINGS ----

	public	$Caption		= 'RegMgr_grading';
	public	$Description		= 'Grading system';
	public	$IconClass		= '';

// ---- Resources -----

// ---- FIELDS ----

// --- Privilegies ---

	function __construct () {
		parent::__construct();
	} //CONSTRUCTOR

	function render () {

		echo 'hello from render';
		
	}// render()

	function editor() {
		echo 'no functions yet';
	} //editor()

}//CLASS mwPageBlock_ReqMgr