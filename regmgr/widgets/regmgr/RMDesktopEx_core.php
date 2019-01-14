<?php
/**//** ----= CLASS mwRMDesktopEx_core		=----------------------------------------------------------------------\**//** \
 *
 *	Some description here
 *
 \***//** ---------------------------------------------------------------=	by SerStoVik @ Morad Media Inc. =------/** //**/
class mwRMDesktopEx_core extends mwRMDesktopEx {
	
	public function column_status ($alias, $row, $cfg) {

		// Getting both statuses captions
		$major	= $row['status_major'];
		$minor	= $row['status_minor'];

		// Getting captions
		$mjCap	= $major ? rmCfg()->getStatuses($major) : $major;
		$mnCap	= $minor ? rmCfg()->getStatuses($minor) : $minor;

		// Returning based on config - both by default
		if ( empty($cfg['type']) or $cfg['type'] == 'both' )
		
			echo trim("{$mjCap} / {$mnCap}", ' /');
			
		elseif ( $cfg['type'] == 'major' )
		
			echo $mjCap;
			
		else
			echo $mnCap;

	} //FUNC column_status
	
	public function column_edit ($alias, $row, $cfg) {
		
		$template = $cfg['value'];
		
		$cfg['alias'] = $alias;
		
		echo $this->renderTemplate($row, '<a class="edit" href="#">' . $template . '</a>', $cfg);
		
	} //FUNC column_edit
	
	public function column_delete ($alias, $row, $cfg) {
		
		$template = $cfg['value'];
		
		$cfg['alias'] = $alias;
		
		if (empty($cfg['message']))
			$cfg['message'] = 'Are you sure you want to delete this application?';
		
		echo $this->renderTemplate($row, '<a rel="' . $cfg['message'] . '" class="Button Delete regmgr-del-' . $row['id'] . '" href="#">' . $template . '</a>', $cfg);
		
	} //FUNC column_delete

	public function column_date ($alias, $row, $cfg) {
		
		// Defauting config
		$cfg	= array_merge([
			'field'		=> 'created',		// Using creation date if else not specified
			'format'	=> true,		// Formatting using mwDate format by default
		], $cfg);
		
		// Getting date
		$date	= $row[ $cfg['field'] ];
		
		// Formatting it if specified
		if ( !$cfg['format'] )
		
			echo $date;
		
		elseif ( $cfg['format'] === true )
		
			echo mwDate($date);
			
		else 
		
			echo date( $cfg['format'], getTime($date) );
			
	} //FUNC column_date
	
} //CLASS mwRMDesktopEx_core