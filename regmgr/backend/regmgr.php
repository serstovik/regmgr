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

/*/

		if ( !empty($_GET['type']) and isAlnum($_GET['type']) )
			$type = $_GET['type'];

		$_GET['type'] = $type;
/**/
		$app		= new rmApplication();
		
		// Ensuring applications DB meets minimum requirements
		$app->createTable()->updateTable();
		
		$rows		= $app->getList();

	//	$this->addData('rmApps', $rows);

	//	$headers	= $this->getHeaders();
	//	$applications	= $this->getApplications();

		$types		= rmCfg()->getTypes();

		$tData		= [];

		// Loading form templates for all types available as subContents
		// Using them to edit applications
		foreach ( $types as $name => $params ) {

			// Loading form template using tpl model
			$template	= $params['template'];
			$template	= compilePath($this->SectionName, $template);

			$tpl		= $this->load->template($template, $tData, 'tplApplication');

			// Initializing template parser
			$tpl
					->backend(true)
					->main($tData);

			// Adding template as subcontent
			$this->load->addSubContent( 'appEd_'.$name, '<div>'.$tpl->html().'</div>' );

		} //FOR each type

		$this->loadContent('applications', 'index', array('applications' => $rows, 'headers' => $headers));

		if ( $this->isAjax )
			return;

		$this->load->editor('applicationEd');

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