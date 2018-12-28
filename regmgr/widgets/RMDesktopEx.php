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
	* 	@row_data	array
	*	@template	string
	*
	\**//** ---------------------------------------------------------------------= by Alex @ Morad Media Inc. =----/** //**/
	public function render($alias, $template, $row_data) {
		
		if ( $alias == 'delete' && empty($template) )
			$html = '<td><a class="Button Delete"></a></td>';
		else {
			
			//update template with data
			$html = arrayToTemplate($row_data, $template);
			
		}
		
		return $html;
		
	}

} //CLASS mwRMDesktopEx