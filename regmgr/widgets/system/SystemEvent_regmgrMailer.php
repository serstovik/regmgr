<?php
/** //** ----= CLASS mwSystemEvent_regmgrMailer	=----------------------------------------------------------------------\**//** \
*
* 	Handles RegMgr mailing events.
* 
* 	@package	MorwebCMS
* 	@subpackage	regmgr
* 	@category	Widget
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwSystemEvent_regmgrMailer extends mwSystemEvent {

	public	$on		= 'regmgr';

	function __construct	() {
		parent::__construct();
	} //CONSTRUCTOR
	
	/** //** ----= trigger	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Executes listener. 
	*
	*	@param	object	$event	- Event descriptor object.	
	*	@return	param	[*]	- Custom data passed to event.
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function trigger ($event, $data) {

		

		
	} //FUNC trigger

} //CLASS mwSystemEvent_regmgrMailer
?>