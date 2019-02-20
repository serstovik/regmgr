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
		
		//__($event, $data);
		//__($data);
		
		//check is this status event
		if ( !empty($event->descriptor[1]) and $event->descriptor[1] == 'status' and is_object($data) ) {
			
			$data = $data->asArray();
			
			if ( !empty($event->descriptor[2]) ) {
				
				$cfg = rmCfg()->getEmails('status.' . $event->descriptor[2]);
				//__($cfg);
				
				foreach( $cfg as $k => $v ) {
					
					$user = $this->getUsers($data['user_id']);
					$adminEmail = mwCfg('System/AdminEmail');
					
					$to = str_replace('@user', $user['email'], $v['to']);
					$to = str_replace('@admin', $adminEmail, $to);
					
					$from = str_replace('@user', $user['email'], $v['from']);
					$from = str_replace('@admin', $adminEmail, $from);
					
					$subject = arrayToTemplate($data, $v['subject']);
					
					//__($user);
					if ( is_array($user) )
						$data = array_merge($user, $data);

					$file = compilePath(SITE_TEMPLATES_PATH, 'regmgr/emails', $v['template']);
					$body = loadView($file, $data);
					//$body = loadView($file, []);
					
					$body = (new vTpl2($body))->parse()->vars($data)->html();
					
					//__($to, $from, $body, $subject);
					$this->email($to, $from, $body, $subject);
					
				}
			}
			
		}
		
		return;
		
	} //FUNC trigger
	
	function email( $to, $from, $body, $subject  ) {
		//__($to, $from, $body, $subject);
		loadVITLib('email');
		$mail = new vMailer();
		$mail->To = $to;
		$mail->From = $from;
		$mail->Body = $body;
		$mail->Subject = $subject;
		$mail->send();
		
		return true;
		
	} //FUNC email
	
	function getUsers($id = 0) {
		
		if ( !$id ) return false;
		
		$where = 'WHERE id = ' . $id;
		
		return mwDB()->query('
			SELECT
			  *
			FROM users
			' . $where . '
		')->asRow();
		
	} //METHOD getUsers
	
} //CLASS mwSystemEvent_regmgrMailer
?>