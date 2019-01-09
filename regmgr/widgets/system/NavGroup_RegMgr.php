<?php
/**//** ----= CLASS NavGroup_RegMgr		=----------------------------------------------------------------------\**//** \
 *
 *
 *
 *\**//** --------------------------------------------------------------=	by SerStoVik @ Morad Media Inc. =------/** //**/
class mwNavGroup_RegMgr extends mwNavGroup
{

	public	$ParentGroup	= 'Intouch';

	public	$Items		= array(

		//array('Registration Manager', 'section:/', 'RegMgr', 'section_RegMgr')

	); //ARRAY items
	
	function __init () {

		$this->load->model('rmCfg');
		
		$coreNavigation = rmCfg()->get('navigation');
		//__($coreNavigation);
		
		//add core navigation
		if ( !empty($coreNavigation) )
			$this->Items[] = array( $coreNavigation, 'section:/', rmCfg()->get('section', 'Registrations Manager') );
		
		$types = rmCfg()->getTypes();
		
		// generate menu
		foreach ( $types as  $k => $v ) {
			
			$this->Items[] = array( $v['navigation'], 'section:/' . $k, ( !empty($v['section']) ) ? $v['section'] : $v['navigation'] );
			
		} //foreach ( $modes as $mode )
		
		// Loading into modules in mw3
		if ( CMS_CORE == 'mw3' )
			$this->ParentGroup = 'Mods';
		
	} //FUNC init
	
}//NavGroup_RegMgr{}