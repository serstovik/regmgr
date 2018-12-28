<?php
/**//** ----= CLASS mwRMDesktopEx_core		=----------------------------------------------------------------------\**//** \
 *
 *	Some description here
 *
 \***//** ---------------------------------------------------------------=	by SerStoVik @ Morad Media Inc. =------/** //**/
class mwRMDesktopEx_core extends mwRMDesktopEx
{
	
	public function column_edit($alias, $row, $cfg) {
		
		$template = $cfg['value'];
		
		$cfg['alias'] = $alias;
		
		echo $this->renderTemplate($row, '<a class="edit" href="#">' . $template . '</a>', $cfg);
		
	} //FUNC column_edit
	
	public function column_delete($alias, $row, $cfg) {
		
		$template = $cfg['value'];
		
		$cfg['alias'] = $alias;
		
		if (empty($cfg['message']))
			$cfg['message'] = 'Are you sure you want to delete this application?';
		
		echo $this->renderTemplate($row, '<a rel="' . $cfg['message'] . '" class="Button Delete regmgr-del-' . $row['id'] . '" href="#">' . $template . '</a>', $cfg);
		
	} //FUNC column_delete
	
} //CLASS mwRMDesktopEx_core