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

	} //FUNC init

	function index ($type = '') {

		$types = array_keys( rmCfg()->getTypes() );

		if ( empty($type) or !in_arrayi($type, $types) )
			throw( new Exception('Invalid type specified.') );

		// Loading required resources
	//	mwLoad('system')->resource('mw.system', 'mw.forms');
		$this->load
			->js('regMgr.js')
			->css('regMgr.css');

		$tData = [];

		// Compiling template name from config
		$template = rmCfg()->getTypes($type, 'template');
		$template = compilePath($this->SectionName, $template);

		$tpl = $this->load->template($template, $tData, 'tplApplication');

		$tpl->main($tData);

		$sn = newSN('F');
	?>
		<form id="<?=$sn?>" action="" method="post" enctype="multipart/form-data" onsubmit="return false;">
			<input type="hidden" name="submit" value="0" />
			<input type="hidden" name="page" value="<?=$this->wgt->Page->ID?>" />
			<input type="hidden" name="widget" value="<?=$this->wgt->ID?>" />
			<input type="hidden" name="form" value="<?=$sn?>" />
			<input type="hidden" name="type" value="<?=$type?>" />
			<?=$tpl->html()?>
		</form>
		
		<script type="text/javascript">

			jQuery( function () {
				
				rmApplication('#<?=$this->wgt->jsId?>');

			}); //jQuery.onLoad

		</script>
	<?php

	/*/
		mwLoad('cart')->model();

		// Replacing bollean with model
		$cart =  mwCart()->init();

		__($cart);
	/**/

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

		// Creating and initializing main model, passing section loader into it
		$app = new rmApplication($_POST['type']);

	// ---- POST and FLES ----

		// Validating general application inputs
		if ( $v = $app->validate($_POST) )
			throw( new mwValidationEx('Wrong info provided.', $v, $_POST['form']) );

	// ---- DB ----

		// Making sure table is up to date
		$app->createTable()->updateTable();

		// And saving data into DB
		$app->fromArray($_POST)->toDB();

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