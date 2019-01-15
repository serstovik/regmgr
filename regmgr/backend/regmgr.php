<?php
/**//** ----= CLASS mwRegmgr			=----------------------------------------------------------------------\**//** \
 *
 *	Main backend controller to work with the applications
 *
 *\**//** ---------------------------------------------------------=		by SerStoVik @ Morad Media Inc. =------/** //**/
class mwRegmgr extends mwController
{

	public 	$AutoIndex	= true;

	function __init() {

		$this->load->java('regMgr_backend.js');

		// Creating and initializing main model using template html
		$this->load->model('rmApplication');
		$this->load->model('rmCfg');

	} //_init()

	function index ($type = '') {

		$app		= new rmApplication();
		
		// Ensuring applications DB meets minimum requirements
		$app->createTable()->updateTable();
		
		$option = [];
		if ( !empty($type) ) $option['type'] = $type;

		$rows		= $app->getList($option);
		
		$tData		= [];
		
		$this->_renderIndex($rows, $tData);
		
		if ( $this->isAjax )
			return;

		$this->load->editor('applicationEd')->loadJS();
		
		$section = 'Register Manager';
		
		//check section name for global editor
		if ( !$type ) {
			
			$section = rmCfg()->get('section', $section);
			
		}
		//check section name for current type
		else {
			
			//get section title for current type
			$tmpSection = rmCfg()->getBranch('types', $type, 'section');
			
			//check is it not empty
			if ( !empty($tmpSection) )
				$section = $tmpSection;
			
		}
		
		return $this->loadIndex('desktop', array(
			'section'	=> $section,
			'barContent'	=> $this->_getBarHtml(),
		));

	} //FUNC index
	
	function _renderIndex($rows, $tData) {
		
		$columnsCFG = rmCfg()->getBranch('backend', 'index');
		//__($columns_cfg);
		
		//heads for index
		$tableHeads = [];
		
		//loop index part of config
		foreach( $columnsCFG as $cfgKey => $cfgVal ) {
			
			$columnWidget = false;
			
			//init column widget
			//check is there no extension in config - use base column render
			if ( empty($cfgVal['extension']) ) {
				
				$widget		= 'base';
				$method		= 'column';
				
			}// base column render
			else { //exstension is added to config
				
				//expected extension built from 2 parts
				//extention name and column method name
				$ext = explode('.', $cfgVal['extension']);
				
				//check is both parameters (extention name and method) provided
				//additional parameters (3+) will be ignored and provided as part of config to extention
				if ( sizeof($ext) >= 2 ) {
					
					$widget		= $ext[0];
					$method		= 'column_' . $ext[1];
					
					//trying to load extention
					$columnWidget = $this->load->widget('RMDesktopEx', $widget);
					
					//check is such extention and column method exists - use default column if no
					if ( !$columnWidget or !method_exists($columnWidget, $method) ) {
						
						$columnWidget	= false; //reset widget to reload it after with base widget
						$widget		= 'base';
						$method		= 'column';
						
					}// use default column
					
				} //both parameter provided
				else { // 1 parameter provided - use default column
					
					$widget		= 'base';
					$method		= 'column';
					
				}
				
			}
			
			//check is widget loaded
			//no widget or no method for widget
			if ( !$columnWidget ) {
				
				//load base widget
				$columnWidget = $this->load->widget('RMDesktopEx', $widget);
				
				
			}
			
			//check is current column is "head" - <th>
			if ( !empty($cfgVal['class']) and strpos($cfgVal['class'], 'head') != -1 ) {
			
				$cellWrap = 'th';
				
			}
			else {
				
				$cellWrap = 'td';
				
			}
			
			//generate and push table header
			$tableHeads[] = '<' . $cellWrap . '>' . $cfgVal['label'] . '</' . $cellWrap . '>';
			
			//prepare column data
			foreach($rows as $rowKey => $rowVal) {
				
				//init columns array
				if ( !isset($rows[$rowKey]) )
					$rows[$rowKey]['RMColumns'] = [];
				
				//generate cell html
				$cellHtml = call_user_func( [$columnWidget, '_ob_' . $method], $cfgKey, $rowVal, $cfgVal );
				
				//wrap template on in cell tag
				$cellHtml = '<' . $cellWrap . '>' . $cellHtml . '</' . $cellWrap . '>';
				
				//generate column html
				$rows[$rowKey]['RMColumns'][] = $cellHtml;
				
			}
			
		}
		
		$this->loadContent('table', 'index', array(
			'heads' => $tableHeads,
			'rows' => $rows
		));
		
	} //FUNC _renderIndex
	
	function _getBarHtml() {
		
		$sortingCFG = rmCfg()->getBranch('core', 'sorting');
		
		//__($sortingCFG);
		
		$html = '';
		
		if ( !empty($sortingCFG) ) {
			
			foreach($sortingCFG as $sortField => $sortVal) {
				
				if ( !empty($sortVal['text']) )
					$text = $sortVal['text'];
				else
					$text = 'Order By ' . $sortField;
				
				if ( !empty($sortVal['asc']) )
					$asc = $sortVal['asc'];
				else
					$asc = 'Order By ' . $sortField . ' ASC';
				
				if ( !empty($sortVal['desc']) )
					$desc = $sortVal['desc'];
				else
					$desc = 'Order By ' . $sortField . ' DESC';
				
				$html .= '<select data-order="' . $order . '" id="sorting_' . $sortField . '">';
				$html .= '<option value="">' . $text . '</option>';
				$html .= '<option value="asc">' . $asc . '</option>';
				$html .= '<option value="desc">' . $desc . '</option>';
				
				$html .= '</select>';
				
			}
			
		}
		
		return $html;
		
	} //FUNC _getBarHtml
	
/* ==== Helpers ============================================================================================================= */

	function _getHeaders (){

		//getting list of table headers
		if (!$this->indexHeaders){

			//formatted list of fields from cfg
			$configHeaders	= $this->config->getBackendIndexHeaders();

			//existing object fields
			$dbFields	= $this->app->_listFields();

			$this->indexHeaders	= array_intersect_key($configHeaders, $dbFields);

		}//if

		return $this->indexHeaders;

	} //FUNC getHeaders

	//prepare and  format application list for our index
	function _getApplications (){


		//get list of  all apps
		$applications	= $this->app->getFullList();
		$headers	= $this->getHeaders();

		$list		= [];
		$Arr		= [];
		foreach ($applications as $application){
			foreach ($headers as $k => $v){
				$list[$k]	= $application[$k];
			}//foreach
				$Arr[]	= $list;
		}//foreach

		return $Arr;

	} //FUNC getApplications
	
	function _getDescValues($field) {
		
		$sql	= '
			SELECT DISTINCT ' . $field . ' FROM regmgr_applications
		';

		return mwDB()->query($sql)->asArray();
		
	}
	
}//CLASS mwRegmgr