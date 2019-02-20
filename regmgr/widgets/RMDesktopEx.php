<?php
/**//** ----= CLASS mwRMDesktopEx		=----------------------------------------------------------------------\**//** \
 *
 *	Some description here
 *
 *\**//** ---------------------------------------------------------------=	by SerStoVik @ Morad Media Inc. =------/** //**/
class mwRMDesktopEx extends mwWidget
{
	
	/** //** ----= render	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Generate column html. Expect column alias, row data and template.
	* 
	*	@alias		string		- column alias from config
	* 	@row		array		- row data
	*	@template	string		- template html
	*
	\**//** ---------------------------------------------------------------------= by Alex @ Morad Media Inc. =----/** //**/
	public function column($alias, $row, $cfg) {
		
		$template = $cfg['value'];
		
		$cfg['alias'] = $alias;
		
		if ( !empty($row['extensions']) ) {
			
			$row['custom'] = [];
			foreach( $row['extensions'] as $v )
				$row['custom'] = array_merge($row['custom'], $v);
			
		}
		
		echo $this->renderTemplate($row, $template, $cfg);
		
	} //FUNC column

	function renderTemplate($row, $template, $options = []) {
	
		$def = [
			'alias'		=> '',		// Column alias from config
			'wrap'		=> false,	// Cell wrapper. FALSE for no wrapper, 'button' to wrap as button, 'link' to wrap as common link
			'class'		=> '',		// Class to add to wrapper
			'url'		=> '',		// If wrapper used - will add href
			'click'		=> '',		// Adds specified js onClick to wrapper
		]; //$def
		
		$options = array_merge($def, $options);
		
		// Parsing with tpl for nested arrays support
		$html	= (new vTpl2($template))->parse()->vars($row)->cleanVars()->html();
		
	//	$html = arrayToTemplate($row, $template);
		
		return $html;
		
	} //FUNC renderTemplate
	
	function renderFilter($cfg) {
		//__($cfg);
		
		$statuses = rmCfg()->getStatuses();
		//__($statuses);
		
		$options = '<option value="">Filter By Status</option>';
		
		foreach( $statuses as $k => $v )
			$options .= '<option value="' . $k . '">' . $v . '</option>';
		
		return $options;
		
	} //FUNC renderFilter
	
	function filter_status($rows, $cfg, $filter) {
		
		//check is some rows exitst
		//check is filter not empty
		//check is filter key status
		//check is filter value not empty
		if ( !empty($rows) && !empty($filter) && $filter['key'] == 'status' && !empty($filter['value']) ) {
			
			//loop rows and filter by status value 
			foreach( $rows as $k => $v ) {
				
				if ( $v['status_major'] != $filter['value'] && $v['status_minor'] != $filter['value'] )
					unset($rows[$k]);
				
			}
		}
		
		return $rows;
		
	} //FUNC filterStatus
	
} //CLASS mwRMDesktopEx