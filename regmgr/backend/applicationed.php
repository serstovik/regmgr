<?php
/** //** ----= CLASS mwApplicationEd		 =---------------------------------------------------------------------\**//** \
*
* 	RegMgr application editor.
*
* 	@package	Morweb
* 	@subpackage	regmgr
* 	@category	editor
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwApplicationEd extends mwEditor {

	public	$Table			= 'regmgr_applications';
	public	$EditorName		= 'applicationEd';
	public	$Desktop		= 'regmgr';

	public	$ItemKind		= 'Application';
	public	$DataName		= 'rmApps';

	public	$useDataCache		= false;	
	public	$useWidnowCache		= false;	

	public	$Privileges		= array('manage_applications');

	public	$type			= '';

	public	$extensions		= [];					// Extensions instances.

	public	$dialogWidth		= 1200;
	public	$panelWidth		= 40;
	public	$minPanelWidth		= 300;

	function __init	() {
		
		$this->load->model('rmCfg');
		$this->load->model('rmApplication');

	// ---- Type ----

		// If type is specified using it
		if ( !empty($_REQUEST['type']) and isAlnum($_REQUEST['type']) ) {

			// Validating type agains registered types
			$types = array_keys( rmCfg()->getTypes() );

			if ( !in_arrayi($_REQUEST['type'], $types) )
				throw( new Exception('Invalid type specified.') );

			// Saving type in self
			$this->type = $_REQUEST['type'];

		} //IF type is provided
		
		parent::__init();

	} //CONSTRUCTOR

	function getDBObject () {

		// Checking if already loaded
		if ( $this->Item )
			return $this->Item;

		// ToDo: Implement static cache to allow multiple calls in a row
		// Consider using clones and partial init for safety

		$obj = new rmApplication($this->type);

		// Making sure table is initiated with random changed designers do on templates
		$obj->createTable()->updateTable();

		return $obj;

	} //FUNC getDBObject

// ---- CONTRIOLLERS -----------------------------------------------------------------------------------------------------------	
	
	/** //** ----= validate	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Validates given item data using DBObject and throws exception in case of error.
	*
	*	@param	array	$data	- Item data to validate.
	* 
	* 	@return	array		- Validated (updated) item data.
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function onValidate ($data) {

		// Loading extensions
		$ext	= $this->loadExtensions();

		// Preparing validations array
		$v = [];
	
		// Looping through extensions and validating with each, collecting all validations together
		foreach ( $ext as $name => $obj ) {

			// Skipping ones who don't have post coming
			if ( empty($data['extensions'][$obj->extName]) )
				continue;
			
			// Issuing and collecting validations
			try {
				
				// Validating extension data in POST and saving it back
				$data['extensions'][$obj->extName] = $obj->validate($data['extensions'][$obj->extName]);
				
			} catch ( mwValidationEx $e ) {
				
				// Converting input names and adding into collection
				$res = $e->Results;
				foreach ( $res as $eName => $messages )
					$v["extensions[{$obj->extName}][{$eName}]"] = $messages;
				
			} //CATCH
			
		} //FOR each extension

		// If there where validation issues - need to throw this as exception
		if ( $v )
			throw( new mwValidationEx('Wrong info provided.', $v, $this->EditorName) );
		
		//load old data from DB
		$this->Item->loadByID($data['id']);
		
		//add flag to send email on major status change
		if ( $data['status_major'] != $this->Item->statusMajor )
			$this->emailMajor = true;
		
		
		//add flag to send email on minor status change
		if ( $data['status_minor'] != $this->Item->statusMinor )
			$this->emailMinor = true;
		
		return $data;
		
	} //FUNC onValidate
	
	/** //** ----= onBeforeSave	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Occurs after object was initiated with POST data, but before it was saved in DB.
	* 
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function onBeforeSave ($item) {
		
		// Loading extensions
		$ext	= $this->loadExtensions();
		
		// Looping through and saving if post have something
		foreach ( $ext as $name => $obj ) {

			// Skipping ones who don't have post coming
			if ( empty($this->Item->extensions[$obj->extName]) )
				continue;
			
			// Issuing save, passing extension data and saving it back
			$this->Item->extensions[$obj->extName] = $obj->save($this->Item->extensions[$obj->extName]);
				
		} //FOR each extension
		
		//trigger major status event
		if ( $this->emailMajor )
			(new mwEvent('regmgr.status.' . $this->Item->statusMajor))->trigger($this->Item);
		
		//trigger major status event
		if ( $this->emailMinor )
			(new mwEvent('regmgr.status.' . $this->Item->statusMinor))->trigger($this->Item);
		
	//	$this->loadWindow();
		
	} //FUNC onBeforeSave

// ---- VIEWS ------------------------------------------------------------------------------------------------------------------	
			
	/** //** ----= window	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Defines editor window body. Outputs ready to use HTML.
	* 
	* 	@param	array	[$vars]	- View variables.ʄ
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function window ( $vars = array() ) {

		// Listing extension tabs for later rendering
		$tabs = $this->listTabs();
		
		// Loading template for current type
		// Rendering with extensions
		// Using this way because window cache is disabled, and editor is rendered for each item individually
		// Compiling template name from config
		$template = rmCfg()->getTypes($this->type, 'template');
		$template = compilePath($this->SectionName, $template);

		$tpl = $this->load->template($template, [], 'tplApplication');

		$tpl	
			->backend(true)
			->application($this->Item)
			->main([]);

		$html	= $tpl->html();
		
	?>
		<div class="winHeader">Edit Application:</div>

		<input data-settings="{ maximized:true }" />

		<form action="" method="post" id="<?=$this->EditorName?>_form" onsubmit="return <?=$this->EditorName?>.save();">
	
			<div class="winRow tools">
	
				<div class="winContent right" style="width: calc(<?=$this->panelWidth?>% - 20px)">
	
					<dl class="mwDialog tools">
	
						<dd><?=$this->winTabsHeads($tabs)?></dd>
	
					</dl>
	
				</div>
	
				<div class="winContent auto" id="<?=$this->EditorName?>_tabs"></div>
	
			</div>
	
			<div class="winHDivider"></div>
	
			<div class="winRow flex" style="width: <?=mw3() ? 'auto' : $this->dialogWidth?>px">
	
				<div id="<?=$this->EditorName?>_panel" class="winContainer right hidden" style="width:<?=$this->panelWidth?>%">
	
					<?=$this->winTabsContents($tabs);?>
	
				</div>
	
				<div class="winVDivider right" style="display: <?=mw3()? 'none' : ''?>;"></div>
	
				<div class="winContainer auto">
	
					<input type="hidden" name="id" value="" />
					<input type="hidden" name="sn" value="" />
					<input type="hidden" name="type" value="" />
					<input type="hidden" name="user_id" value="" />
					<input type="hidden" name="status_major" value="" />
					<input type="hidden" name="status_minor" value="" />

					<div class="winContent flex full" id="<?=$this->EditorName?>_formContents"><?=$html?></div>
	
				</div>
	
			</div>

		</form>
	
		<div class="winFooter">
			<a class="apply" rel="<?=$this->EditorName?>_form"><?=mw3()? '' : 'Save'?></a>
			<a class="close winCloseClick"><?=mw3()? '' : 'Cancel'?></a>
		</div>
	<?php
	} //FUNC window

/* ==== Tabs ================================================================================================================ */

	function listTabs ( ) {

		// ToDo: Merge ->listTabs() with ->loadExtensions();

		$wgts = $this->loadExtensions();
		
		$cfg = rmCfg()->getBranch('backend', 'editor');
		
		$tabs = [];

		$i = 0;

		foreach ( $wgts as $name => $w ) {
			
			$ext = explode('.', $cfg[$name]['extension']);
			
			//check is method provided in cofing
			if ( sizeof($ext) > 1 )
				$method		= 'editor_' . $ext[1];
			else //use default method if not
				$method		= 'editor';
			
			// config data
			$w->cfg = $cfg[$name];
			
			$tabs['tab_' . $name] = [

				'name'		=> $name,			// Tab short name
				'widget'	=> $w,				// Extension widget
				'method'	=> $method,			// Expected renderer method
				'caption'	=> $cfg[$name]['caption'],	// Tab visible caption
				'selected'	=> $i == 0,			// Default tab marker

			]; //$tabs

			$i++;
			
			/*
			foreach ( $w->tabs as $tName => $cap ) {

				$tabs[$name.'_'.$tName] = [

					'name'		=> $tName,			// Tab short name
					'widget'	=> $w,				// Extension widget
					'method'	=> 'editor_'.$tName,		// Expected renderer method
					'caption'	=> $cap,			// Tab visible caption
					'selected'	=> $i == 0,			// Default tab marker

				]; //$tabs

				$i++;

			} //FOR each tab in extension
			*/
			
		} //FOR each extension tab

		// ToDo: implement widgets sorting from config

		return $tabs;

	} //FUNC listTabs

	function winTabsHeads ($tabs) {
	?>
		<table class="mwWinTabs" id="<?=$this->EditorName?>_editorTabs">
			<tr>
			<?php	foreach ( $tabs as $name => $row ) { ?>

					<td rel="<?=$name?>" <?=( $row['selected'] ? 'class="Selected selected"' : '');?> onclick="jQuery('#<?=$this->EditorName?>_panel').removeClass('hidden'); mwSwitchTab(this);"><?=$row['caption']?></td>

			<?php	} //FOR each widget ?>

				<td rel="tab_off" onclick="jQuery('#<?=$this->EditorName?>_panel').toggleClass('hidden')">&gt;</td>
			</tr>
		</table>
	<?php
	} //FUNC winTabsHeads

	function winTabsContents ($tabs) {

		foreach ( $tabs as $name => $row ) {
		?>
			<div id="<?=$name?>" class="winContainer flex" style="min-width: <?=$this->minPanelWidth?>px;">

				<div class="winContent flex full">

					<?=$this->getExtensionEditor($row['widget'], $row['method'])?>

				</div>

			</div>
		<?php
		} //FOR each widget

	} //FUNC winTabsContents

/* ==== Helpers ============================================================================================================= */

	function getExtensionEditor ($obj, $method) {
		
		// Validating method
		if ( !method_exists($obj, $method) )
			return 'Impropertly configured extension, invalid method: ['.$method.']';

	// ---- HTML ----
	
		// Defining extension name
		if ( !$obj->extName )
			$obj->extName	= $obj->WidgetName;

		// Setting up extension
		$obj->application	= $this->Item;
		
		// Making sure extension data is set for feedback
		if ( empty($this->Item->extensions[$obj->extName]) )
			$this->Item->extensions[$obj->extName] = [];
		
		// Passing it into extension
		// Using pointer linking for feedback
		$obj->data 		= &$this->Item->extensions[$obj->extName];
		
		// Getting editor HTML
		$html	= call_user_func([$obj, '_ob_'.$method]); 
		
		// Updating ajax data with updated data
		// Have to do this directly in loader to object enforcemeent (for JS)
		// ToDo: Need to find better way of data merging for editors
		$this->load->sysCache['Data'][$this->DataName][$this->ID]->extensions = $this->Item->extensions;
	
	// ---- Inputs ----
		
		// Parsing editor inputs and converting them into array format
		// All extension inputs sould be added into extensions subarray, indexed by extension name		
		
		// Using vTpl for inputs parsing
		$tpl	= new vTpl2($html);
		
		$tpl->parse()->inputs( function ($node) use ($obj) {
			
			return $node->render()->prefixInput('extensions['.$obj->extName.']', true);
			
		}); //FUNC render.inputs
		
		// Getting updated html
		$html	= $tpl->html();			

		return $html;

	} //FUNC winTabsContents

	function loadExtensions ( $force = false ) {
		
		$cfg = rmCfg()->getBranch('backend', 'editor');
		
		// If forced - resetting extensions cache
		if ( $force )
			$this->extensions = [];

		// No need to reload if already loaded
		if ( !empty($this->extensions) )
			return $this->extensions;

		// Making sure DB object is set
		$this->setDBObject();

		// Loading exensions widgets

		//$this->extensions	= $this->load->widgets('RMEditorEx');
		
		$this->extensions = [];
		
		//loop index part of config
		foreach( $cfg as $cfgKey => $cfgVal ) {
			
			// Splitting extension into widget/method pair
			// If method omited - assuming default render
			list($widget, $methos)	= explode('.', $cfgVal['extension']);
			
			// Loading extension widget
			$obj			= $this->load->widget('RMEditorEx', $widget);
			
			// Skipping failed widgets
			if ( !$obj )
				continue;
			
			// Defining extension name
			if ( !$obj->extName )
				$obj->extName		= $obj->WidgetName;
			
			// Setting up object
			$obj->application	= $this->Item;
			
			// Providing link to extension data
			$obj->data		= &$this->Item->extensions[$obj->extName];
			
			// Supplying config and initiating properties with it
			$obj->cfg		= $cfgVal;
			$obj->_fromArray($cfgVal);

			// Storing in cache			
			$this->extensions[$cfgKey] = $obj;
			 
		} //FOR each config
		
		// Done
		return $this->extensions;

	} //FUNC loadExtensions

	function loadJS () {
		
		parent::loadJS();
		
		// Loading widgets and calling loadJS for them
		$wgts = $this->loadExtensions();
		
		foreach ( $wgts as $name => $wgt ) {

			$wgt->initResources();
			
		} //FOR each widget
		
	} //FUNC loadJS

} //CLASS mwApplicationEd
?>