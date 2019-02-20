<?php
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

	function getStatuses ($status = 'major') {
		
		//define major statuses
		$major = ['new', 'open', 'submit', 'ready', 'approved', 'declined', 'closed'];
		
		$listCfg = $this->getBranch('statuses');
		
		$result = [];
		
		if ( $status == 'both' || $status == 'major' ) {
			
			foreach($major as $k => $v ) {
				
				if ( !empty( $listCfg[$v] ) )
					$result[$v] = $listCfg[$v];
				else
					$result[$v] = $v;
				
			}
		}
		
		if ( $status == 'both' || $status == 'minor' ) {
			
			foreach($listCfg as $k => $v ) {
				
				if ( !in_array($k, $major) )
					$result[$v] = $v;
				
			}
			
		}
		
		return $result;

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