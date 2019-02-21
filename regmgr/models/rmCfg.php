<?php
//define statuses scopes (types)
define('RM_STATUS_SCOPE_NONE',	'none');
define('RM_STATUS_SCOPE_MAJOR',	'major');
define('RM_STATUS_SCOPE_MINOR',	'minor');
define('RM_STATUS_SCOPE_BOTH',	'both');

//define statuses
define('RM_STATUS_NEW',		'new');
define('RM_STATUS_OPEN',	'open');
define('RM_STATUS_SUBMIT',	'submit');
define('RM_STATUS_READY',	'ready');
define('RM_STATUS_APPROVED',	'approved');
define('RM_STATUS_DECLINED',	'declined');
define('RM_STATUS_CLOSED',	'closed');

/** //** ----= CLASS rmCfg	=--------------------------------------------------------------------------------------\**//** \
 *
 *	RegMgr config helper.
 *
\**//** ----------------------------------------------------------------------------------------------= by Mr.V!T =----/** //**/
function rmCfg () {

	if ( $obj = getGlobalObject('rmCfg') )
		return $obj;

	return setGlobalObject('rmCfg', new rmCfg() );

} //FUNC rmCfg

class rmCfg extends vObject {

	public	$cfg			= [];
	public	$fileName		= 'regmgr.cfg';

	function __construct () {

		$this->__initGlobals();

		if ( !$this->cfg ) {

			$this->cfg = readCFG(SITE_CFG_PATH.'/'.$this->fileName, true);
			$this->initCfg();

		} //IF config is not loaded

	}//FUNC constructor

	function __initGlobals () {

		// Sharing config between instances
		$this->__assignGlobals(['cfg']);

		return $this;

	} //FUNC __initGlobals

	function initCfg () {

	// ---- Emails ----

		// Preparing emails, formatting config into common syntax
		if ( !empty($this->cfg['emails']) ) {
			foreach ( $this->cfg['emails'] as $event => &$rows )
				if ( !empty($rows) and !is_array( reset($rows) ) )
					$rows = [$rows];

		} //IF email set

		return $this;

	} //FUNC initCfg

	function get ($param = '', $default = false) {
/**/
		if ( $param )
			if ( isset($this->cfg['core'][$param]) ) {
				return $this->cfg['core'][$param];
			} //IF parameter set in core
			else {
				return $default;
			} //IF no param
/**/
		return $this->cfg;

	} //FUNC get

	function getBranch ($root, $branch = '', $param = '') {

		if ( !isset($this->cfg[$root]) )
			return [];

		if ( !$branch )
			return $this->cfg[$root];

		if ( !isset($this->cfg[$root][$branch]) )
			return [];

		if ( !$param )
			return $this->cfg[$root][$branch];

		if ( !isset($this->cfg[$root][$branch][$param]) )
			return '';

		return $this->cfg[$root][$branch][$param];

	} //FUNC getBranch

	function getTypes ($type = '', $param = '') {

		return $this->getBranch('types', $type, $param);

	} //FUNC getTypes

	function getStatuses ($status = '', $scope = RM_STATUS_SCOPE_BOTH) {
		
		//define major statuses
		static $major = [
			RM_STATUS_NEW		=> 'New',
			RM_STATUS_OPEN		=> 'Open',
			RM_STATUS_SUBMIT	=> 'Submit',
			RM_STATUS_READY		=> 'Ready',
			RM_STATUS_APPROVED	=> 'Approved',
			RM_STATUS_DECLINED	=> 'Declined',
			RM_STATUS_CLOSED	=> 'Closed'
		];
		
		static $statuses;
		
		//loading statuses from config if necesssary
		if ( empty($statuses) )
			$statuses = array_merge($major, $this->getBranch('statuses'));
		
		//return all statuses
		if ( !$status && $scope == RM_STATUS_SCOPE_BOTH )
			return $statuses;
		
		//status not empty - checking against scope and returning caption
		if ( $status ) {
			
			//if no such status exists - return false
			if (  !array_key_exists($status, $statuses) )
				return false;
			
			//if scope both - just return current status lable
			if ( $scope == RM_STATUS_SCOPE_BOTH )
				return $statuses[$status];
			
			//checking is status belong to given scope
			//if current status belong to major scope
			if ( array_key_exists($status, $major) ) {
				
				if ( $scope == RM_STATUS_SCOPE_MAJOR )
					return $statuses[$status];
				
			}//status belong to major scope
			else {
				
				if ( $scope == RM_STATUS_SCOPE_MINOR )
					return $statuses[$status];
				
			}//status belong to minor scope
			
			//no match found
			return false;
			
		}// if status not empty
		else {
			
			$result = [];
			
			//loop statuses list and return only required by scope statuses
			foreach($statuses as $k => $v ) {
				
				//check if current status is major
				if ( array_key_exists($k, $major) ) {
					
					if ( $scope == RM_STATUS_SCOPE_MAJOR )
						$result[$k] = $v;
					
				}// current status is major
				else {
					
					if ( $scope == RM_STATUS_SCOPE_MINOR )
						$result[$k] = $v;
					
				}//current status is minor
				
			}//loop statuses
			
			return $result;
			
		} //stastus is empty

	} //FUNC getStatuses

	function getEmails ($event = '') {

		return $this->getBranch('emails', $event);

	} //FUNC getEmails
	
	function getSorting () {
		
		//load sorting data from cfg
		$sortingCFG = rmCfg()->getBranch('core', 'sorting');
		
		//sorting result array
		$result = [];
		
		//check is sorting data exists
		if ( !empty($sortingCFG) ) {
			
			foreach($sortingCFG as $k => $v) {
				
				//check is sorting not empty at all
				if ( !empty($v) ) {
					
					//init sorting
					$result[$k] = [];
					
					//split on 2 parts
					$exp = explode('|', $v);
					
					//2nd part is display for select
					if ( !empty($exp[1]) ) {
						
						$result[$k]['value'] = trim($exp[0]);
						$result[$k]['title'] = trim($exp[1]);
						
					}
					else {
						
						$result[$k]['value'] = trim($exp[0]);
						$result[$k]['title'] = trim($exp[0]);
						
					}
				}
			}
		}
		
		return $result;

	} //FUNC getSorting
	
} //CLASS rmCfg