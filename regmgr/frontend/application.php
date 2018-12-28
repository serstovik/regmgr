<?php
/**//** ----= CLASS mwApplication		=----------------------------------------------------------------------\**//** \
 *
 *
 *
 *\**//** --------------------------------------------------------------= by SerStoVik @ Morad Media Inc.	=------/** //**/
class mwApplication extends mwController {

	public	$wgt		= false;
	public	$AutoIndex	= true;		// Allow or not redirect unknown methods to index.

	public $config		= false;

	function __init	() {

		$this->load->model('rmCfg');
		mwLoad('regmgr_attach')->model('rmAttach');

	} //FUNC init

	function index ($type = '', $id = '') {

	// ---- Vaidating ----
	
		$types = array_keys( rmCfg()->getTypes() );

		if ( empty($type) or !in_arrayi($type, $types) )
			throw( new Exception('Invalid type specified.') );

		if ( !empty($id) and !isID($id) )
			throw( new Exception('Invalid application specified.') );

	// ---- Models and resources ----
	
		// Loading App model models
		$this->load->model('rmApplication');

		// Loading required resources
	//	mwLoad('system')->resource('mw.system', 'mw.forms');
		$this->load
			->js('regMgr.js')
			->css('regMgr.css');

	// ---- Application ----

		// Creating and initializing application model
		// Using clear application on template for extensions interractions
		// Or loading it with existing application if ID provided
		$app = new rmApplication($type);

		if ( $id ) {
			
			$app->loadByID($id);
			
			if ( !$app->id )
				throw( new Exception('Failed to laod specified application.') );
			
		} //IF existing application loading

		$tData = ['application' => $app];

	// ---- Template ----

		// Compiling template name from config
		$template = rmCfg()->getTypes($type, 'template');
		$template = compilePath($this->SectionName, $template);

		$tpl = $this->load->template($template, $tData, 'tplApplication');

		$tpl	
			->application($app)
			->main($tData);

		$html	= $tpl->html();
		
		//__($tpl->message('thank_you'));
		$thank_you = addslashes('<div style="width: 100%; text-align: center; font-size: 24px; ">Application saved. Thank you.</\' + \'div>');
		
	// ---- Form ----

		// Prefilling form from current app
		// Doing only for existing applications, as input defaults are set on template anyway
		if ( $app->id ) {
		
			$form	= new mwForm();
			$form->init($html)->Inputs($app)->setup();
		
			$html	= $form->HTML();
			
		} //IF existing application
	
	// ---- Render ----

		$sn = newSN('F');
	?>
		<form id="<?=$sn?>" action="" method="post" enctype="multipart/form-data" onsubmit="return false;">
			<input type="hidden" name="id" value="<?=$app->id?>" />
			<input type="hidden" name="type" value="<?=$type?>" />
			<input type="hidden" name="page" value="<?=$this->wgt->Page->ID?>" />
			<input type="hidden" name="widget" value="<?=$this->wgt->ID?>" />
			<input type="hidden" name="form" value="<?=$sn?>" />
			<input type="hidden" name="files" value="" />
			<input type="hidden" name="submit" value="0" />
			<?=$html?>
		</form>
		
		<script type="text/javascript">

			jQuery( function () {
				
				rmApplication_inst = rmApplication({
					sn		: '<?=$sn?>',
					el		: '#<?=$this->wgt->jsId?>',
					thank_you	: '<?=$thank_you?>',
				});
				
			}); //jQuery.onLoad

		</script>
	<?php
	} //FUNC index

	function save () {

	// ---- Validating ----

		$types = array_keys( rmCfg()->getTypes() );

		if (
			empty($_POST['type'])
			or !isAlnum($_POST['type'])
			or !in_arrayi($_POST['type'], $types)
		)
			throw( new Exception('Invalid type specified.') );

		// Initiating widget
		$this->_initWidget();

		if ( empty($_POST['form']) or !isSN($_POST['form'], 'F') )
			throw( new Exception('Invalid source form.') );

	// ---- Models ----

		// Loading involved models
		$this->load->model('rmApplication');

		// Creating and initializing main model
		$app = new rmApplication($_POST['type']);

	// ---- POST and FLES ----

		// Validating general application inputs
		if ( $v = $app->validate($_POST) )
			throw( new mwValidationEx('Wrong info provided.', $v, $_POST['form']) );

		//todo: add files logic
		$Attacher	= new rmAttach();
		if (isset($app->sn))
			$_POST['sn']	= $app->sn;

		$Attacher->uploadAndSaveDocument();

	// ---- DB ----

		// Making sure table is up to date
		$app->createTable()->updateTable();
		
		//check is submit button was clicked
		if ( !empty($_POST['submit']) && $_POST['submit'] == '1' ) {

			//change status to submit only for new or saved apps
			if ( in_array($this->statusMajor, [RM_STATUS_NEW, RM_STATUS_OPEN]) ) {
			
				$app->setStatus(RM_STATUS_SUBMIT);
			
			}//change status to submit

		}// submit button clicked
		//submit != 1 - save and return clicked
		else {
			
			
			
		}
		//__($_POST, $app);
		//$app->loadById();
		
		
		//trigger before save

		// And saving data into DB
		$app->fromArray($_POST)->toDB();
		
		//triger after save
		
		
	} //FUNC save

/* ==== Helpers ============================================================================================================= */

	function _initWidget () {

	// ---- Validating ----

		if ( empty($_POST['page']) or !isID($_POST['page']) )
			throw( new Exception('Invalid page specified.') );

		if ( empty($_POST['widget']) or !isID($_POST['widget']) )
			throw( new Exception('Invalid page specified.') );

	// ---- Models ----

		// Using page to get main widget
		mwLoad('pages')->model();

		$page	= (new mwPage())
			->loadById($_POST['page']);

		// Saving widget in self
		$this->wgt = $page->getWidget($_POST['widget']);

		// Done.
		return $this;

	} //FUNC initWidget

	function test () {

		$wgts	= $this->load->widgets('RMApplicationEx');
		//__($wgts);



		$wgt = $this->load->widget('RMApplicationEx', 'eShopForms');
		__($wgt);

	} //FUNC test


}//CLASS mwApplication