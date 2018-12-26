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

		$this->loadContent('applications', 'index', array('applications' => $rows, 'headers' => $headers));

		if ( $this->isAjax )
			return;

		$this->load->editor('applicationEd')->loadJS();

		return $this->loadIndex('desktop');

	}//index()

	function deleteApplication ($appId){

		$app = (new rmApplication())
			->id($appId)
			->delete();

		return $this->index();

	} //FUNC deleteApplication

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