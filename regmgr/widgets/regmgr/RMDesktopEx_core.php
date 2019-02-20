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
		// Correcting to defaults if no config found
		$mjCap	= ( is_string($mjCap = rmCfg()->getStatuses($major)) ) ? $mjCap : $major; 
		$mnCap	= ( is_string($mnCap = rmCfg()->getStatuses($minor)) ) ? $mnCap : $minor; 

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
	
	function renderFilter($cfg) {
		//__($cfg);
		
		
		
	} //FUNC renderFilter
	
	function filter_status($rows, $cfg, $filter = false) {
		
		//__($cfg, $value);
		
		//check is options list required
		if ( !$filter ) {
			
			$statuses = rmCfg()->getStatuses();
			//__($statuses);
			
			$options = '<option value="">Filter By Status</option>';
			
			foreach( $statuses as $k => $v )
				$options .= '<option value="' . $k . '">' . $v . '</option>';
			
			return $options;
			
		}
		//return sql where conditions
		else {
			
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
			
		}
		
		return $rows;
		
	} //FUNC filterStatus
	
} //CLASS mwRMDesktopEx_core