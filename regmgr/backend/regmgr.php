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

	function index () {

		$app		= new rmApplication();
		
		// Ensuring applications DB meets minimum requirements
		$app->createTable()->updateTable();
		
		$rows		= $app->getList();

		$tData		= [];
		
		$columns_cfg = rmCfg()->getBranch('backend', 'index');
		
		$table_heads = [];
		foreach( $columns_cfg as $c_k => $c_v ) {
			
			//create table header
			$table_heads[] = [
				'label' => $c_v['label']
			];
			
			//init column widget
			//$column_widget = ...
			
			//trying to load special widget for column
			$column_widget = $this->load->widget('RMDesktopEx', $c_k);
			__($column_widget);
			if ( !$column_widget )
				$column_widget = $this->load->widget('RMDesktopEx', 'base');
			
			__($column_widget);
			
			//prepare column data
			foreach($rows as $r_k => $r_v) {
				
				//init columns array
				if ( !isset($rows[$r_k]) )
					$rows[$r_k]['RMColumns'] = [];
				
				
				//generate column html
				$rows[$r_k]['RMColumns'][] = $column_widget->render($c_k, $c_v['value'], $r_v);
				
			}
			
		}
		
		$desk_list = $this->load->widgets('RMDesktopEx');
		__($desk_list, $columns_cfg, $rows);
		
		$this->loadContent('table', 'index', array(
			'heads' => $table_heads,
			'rows' => $rows
		));
		
		if ( $this->isAjax )
			return;

		$this->load->editor('applicationEd')->loadJS();

		return $this->loadIndex('desktop');

	} //FUNC index

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