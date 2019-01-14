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
		$html	= (new vTpl2($template))->parse()->vars($row)->html();
		
	//	$html = arrayToTemplate($row, $template);
		
		return $html;
		
	} //FUNC
	
} //CLASS mwRMDesktopEx