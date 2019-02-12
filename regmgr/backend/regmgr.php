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

		$this->app = new rmApplication();
		
		// Ensuring applications DB meets minimum requirements
		$this->app->createTable()->updateTable();
		
		//init regmgr session
		if ( !isset($_SESSION['regmgr']) )
			$_SESSION['regmgr'] = [];
		
		//init list_options
		if ( !isset($_SESSION['regmgr']['list_options']) )
			$_SESSION['regmgr']['list_options'] = [];
		
		//todo: improve type to fix bugs in case you saving from non type page after there was save from type page
		//type stored in session makes non type page index updates as last type saved
		
		//set list_options type
		if ( !$this->isAjax )
			$_SESSION['regmgr']['list_options']['type'] = $type;
		//__($_SESSION['regmgr'], $type);
		
		//set list_options sorting
		if ( empty($_POST['sorting']) ) {
			
			//load sorting cfg data
			$sortingCFG = rmCfg()->getSorting();
			//__($sortingCFG);
			
			if ( !empty($sortingCFG['default']['value']) )
				$_SESSION['regmgr']['list_options']['sorting'] = $sortingCFG['default']['value'];
			else
				$_SESSION['regmgr']['list_options']['sorting'] = '';
			
		}
		else
			$_SESSION['regmgr']['list_options']['sorting'] = $_POST['sorting'];
		
		$rows = $this->app->getList($_SESSION['regmgr']['list_options']);

		//set list_options filter
		if ( !empty( $_POST['filterKey'] ) ) {
		
			$_SESSION['regmgr']['list_options']['filter'] = ['key' => $_POST['filterKey'], 'value' => $_POST['filterValue']];
			
		}
		
		//reset filter on page load
		if ( !$this->isAjax )
		$_SESSION['regmgr']['list_options']['filter'] = false;
		
		//get filters list
		$filterCFG = rmCfg()->getBranch('core', 'filter');
		//__($filterCFG);
		
		//get column widgets list
		$columnList = $this->_getColumnWidgets($filterCFG);
		//__($columnList);
		
		//loop column list and call widgets to filter data
		foreach( $columnList as $k => $v ) {
			
			//generate cell html
			$rows = call_user_func(
				[$columnList[$k]['widget'], 'filter_' . $columnList[$k]['method_base']],
				$rows, $filterCFG[$k], $_SESSION['regmgr']['list_options']['filter']
			);
			
		}
		
		$tData = [];
		
		$this->_renderIndex($rows, $tData);
		
		if ( $this->isAjax )
			return;
		
		$this->load->editor('applicationEd', ['item' => $this->app])->loadJS();
		
		$section = 'Register Manager';
		
		//check section name for global editor
		if ( !$_SESSION['regmgr']['filters']['type'] ) {
			
			$section = rmCfg()->get('section', $section);
			
		}
		//check section name for current type
		else {
			
			//get section title for current type
			$tmpSection = rmCfg()->getBranch('types', $_SESSION['regmgr']['filters']['type'], 'section');
			
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
		
		$columnsCFG = rmCfg()->getBranch('backend', 'index');
		//__($columns_cfg);
		
		$widgetsList = $this->_getColumnWidgets($columnsCFG);
		//__($widgetsList);
		
		//loop index part of config
		foreach( $columnsCFG as $cfgKey => $cfgVal ) {
			
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
				$cellHtml = call_user_func( [$widgetsList[$cfgKey]['widget'], '_ob_' . $widgetsList[$cfgKey]['method']], $cfgKey, $rowVal, $cfgVal );
				
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
	
	function _getColumnWidgets($cfg) {
		
		$widgetsList = [];
		
		//loop index part of config
		foreach( $cfg as $cfgKey => $cfgVal ) {
			
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
					$method_base	= $ext[1];
					$method		= 'column_' . $method_base;
					
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
			
			//init widget
			$widgetsList[$cfgKey] = [];
			$widgetsList[$cfgKey]['widget'] = $columnWidget;
			$widgetsList[$cfgKey]['method'] = $method;
			$widgetsList[$cfgKey]['method_base'] = $method_base;
			
		}
		
		return $widgetsList;
		
	} //FUNC _getColumnWidgets
	
	function _getBarHtml() {
		
		//get sorting config data
		$sortingCFG = rmCfg()->getSorting();
		//__($sortingCFG);
		
		//generate sorting select
		$html = '';
		$html .= '<select id="regmgr_sorting">';
		
		foreach( $sortingCFG as $v )
			$html .= '<option value="' . $v['value'] . '">' . $v['title'] . '</option>';
		
		$html .= '</select>';
		
		//get filters data
		$filterCFG = rmCfg()->getBranch('core', 'filter');
		//__($filterCFG);
		
		//get column widgets list
		$columnList = $this->_getColumnWidgets($filterCFG);
		//__($columnList);
		
		//loop column list and call widgets to filter data
		foreach( $columnList as $k => $v ) {
			
			//generate cell html
			$html .= '<select id="' . $k . '" class="regmgr_filter">';
			$html .= call_user_func([$columnList[$k]['widget'], 'renderFilter'], $filterCFG[$k]);
			$html .= '</select>';
			
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
	
}//CLASS mwRegmgr