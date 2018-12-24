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

	function get ($param = '') {
/**/
		if ( $param )
			if ( isset($this->cfg['core'][$param]) ) {
				return $this->cfg['core'][$param];
			} //IF parameter set in core
			else {
				return false;
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

	function getStatuses ($status = '') {

		return $this->getBranch('statuses', $status);

	} //FUNC getStatuses

	function getEmails ($event = '') {

		return $this->getBranch('emails', $event);

	} //FUNC getStatuses

} //CLASS rmCfg