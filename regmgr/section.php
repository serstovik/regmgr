<?php

class mwRegmgrInit extends mwSectionInit {

	public	$Title		= 'RegMgr';
	public 	$Description	= 'Registration Manager';

	public $Privileges	= array(
		'applicant' 	=> array('Applicant User', FALSE),
		'reviewer' 	=> array('Reviewer User', FALSE)
	);

} //CLASS mwRegmgrInit