<?php
/** //** ----= CLASS rmMailer		=------------------------------------------------------------------------------\**//** \
 *
 *	Main class to work with emails
 *
 * 	@package           Registration Manager
 * 	@subpackage        mailer
 * 	@category          model
 *
 *\**//** ----------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
class rmMailer extends vSimpleMailer
{


//	$condition = 'rmEvent.status';
//	$condition = 'rmEvent.action';

	public $template	= false;
	public $status		= false;


	public function sendEmail(  ) {


	} //FUNC sendEmail

//	function trigger ($major, $minor, $application, $data);
//	function trigger ($action, $application, $data);


} //CLASS rmMailer