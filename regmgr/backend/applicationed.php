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

	public	$Privileges		= array('manage_applications');

	public	$type			= '';

	public	$extensions		= [];					// Extensions instances.

	public	$dialogWidth		= 1200;
	public	$panelWidth		= 30;
	public	$minPanelWidth		= 300;

	function __init	() {

		$this->load->model('rmCfg');
		$this->load->model('rmApplication');

	// ---- Type ----

		// If type is specified using it
		if ( !empty($_REQUEST['type']) and isAlnum($_REQUEST['type']) ) {

			// Validating type agains registered types
			$types = array_keys( rmCfg()->getTypes() );

			if (
				!isAlnum($_REQUEST['type'])
				or !in_arrayi($_REQUEST['type'], $types)
			)
				throw( new Exception('Invalid type specified.') );

			// Saving type in self
			$this->type = $_REQUEST['type'];

		} //IF type is provided

		parent::__init();

	} //CONSTRUCTOR

	function getDBObject () {

		// ToDo: Implement static cache to allow multiple calls in a row
		// Consider using clones and partial init for safety

		$obj = new rmApplication($this->type);

		// Making sure table is initiated with random changed designers do on templates
		$obj->createTable()->updateTable();

		return $obj;

	} //FUNC getDBObject

// ---- CONTRIOLLERS -----------------------------------------------------------------------------------------------------------	
	
	/** //** ----= save	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Custom save opeation.
	* 
	* 	@param	int	[$id] 	- Item ID to save.
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function save ($id = '') {

		return parent::save($id);

	} //FUNC save

// ---- VIEWS ------------------------------------------------------------------------------------------------------------------	
			
	/** //** ----= window	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Defines editor window body. Outputs ready to use HTML.
	* 
	* 	@param	array	[$vars]	- View variables.ʄ
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function window ( $vars = array() ) {

		$this->loadJS();
		
		$tabs = $this->listTabs();

	?>
		<div class="winHeader">Edit Application:</div>

		<input data-settings="{ maximized:true }" />

		<div class="winRow tools">

			<div class="winContent right" style="width: calc(<?=$this->panelWidth?>% - 20px)">

				<dl class="mwDialog tools">

					<dd>
						<?=$this->winTabsHeads($tabs)?>
					</dd>

				</dl>

			</div>

			<div class="winContent auto" id="<?=$this->EditorName?>_tabs">

			</div>

		</div>

		<div class="winHDivider"></div>

		<div class="winRow flex" style="width: <?=mw3() ? 'auto' : $this->dialogWidth?>px">

			<div id="<?=$this->EditorName?>_panel" class="winContainer right hidden" style="width:<?=$this->panelWidth?>%">

				<?=$this->winTabsContents($tabs);?>

			</div>

			<div class="winVDivider right" style="display: <?=mw3()? 'none' : ''?>;"></div>

			<div class="winContainer auto">


				<form action="" method="post" id="<?=$this->EditorName?>_form" onsubmit="return <?=$this->EditorName?>.save();">

					<input type="hidden" name="id" value="" />
					<input type="hidden" name="sn" value="" />
					<input type="hidden" name="type" value="" />

					<div class="winContent flex full" id="<?=$this->EditorName?>_formContents"></div>

				</form>

			</div>

		</div>

		<div class="winFooter">
			<a class="apply" rel="<?=$this->EditorName?>_form"><?=mw3()? '' : 'Save'?></a>
			<a class="close winCloseClick"><?=mw3()? '' : 'Cancel'?></a>
		</div>
	<?php
	} //FUNC window

/* ==== Tabs ================================================================================================================ */

	function listTabs ( ) {

		$wgts = $this->loadExtensions();

		$tabs = [];

		$i = 0;

		foreach ( $wgts as $name => $w ) {

			foreach ( $w->tabs as $tName => $cap ) {

				$tabs[$name.'_'.$tName] = [

					'name'		=> $tName,			// Tab short name
					'caption'	=> $cap,			// Tab visible caption
					'method'	=> [$w, '_ob_editor_'.$tName],	// Prepared renderer
					'selected'	=> $i == 0,			// Default tab marker

				]; //$tabs

				$i++;

			} //FOR each tab in extension

		} //FOR each extension tab

		// ToDo: implement widgets sorting from config

		return $tabs;

	} //FUNC listTabs

	function winTabsHeads ($tabs) {
	?>
		<table class="mwWinTabs">
			<tr>
			<?php	foreach ( $tabs as $name => $row ) { ?>

					<td rel="<?=$name?>" class="<?=$row['selected'] ? 'Selected' : ''?>" onclick="jQuery('#<?=$this->EditorName?>_panel').removeClass('hidden'); mwSwitchTab(this);"><?=$row['caption']?></td>

			<?php	} //FOR each widget ?>

				<td rel="tab_off" class="selected" onclick="jQuery('#<?=$this->EditorName?>_panel').toggleClass('hidden')">&gt;</td>
			</tr>
		</table>
	<?php
	} //FUNC winTabsHeads

	function winTabsContents ($tabs) {

		foreach ( $tabs as $name => $row ) {
		?>
			<div id="<?=$name?>" class="winContainer flex" style="min-width: <?=$this->minPanelWidth?>px;<?=!$row['selected'] ? ' display: none;' : ''?>">

				<div class="winContent flex full">

					<?=call_user_func($row['method'])?>

				</div>

			</div>
		<?php
		} //FOR each widget

	} //FUNC winTabsContents

/* ==== Helpers ============================================================================================================= */

	function loadExtensions ( $force = false ) {

		// If forced - resetting extensions cache
		if ( $force )
			$this->extensions = [];

		// No need to reload if already loaded
		if ( !empty($this->extensions) )
			return $this->extensions;

		// Loading exensions widgets
		$this->extensions	= $this->load->widgets('RMEditorEx');

		// Done
		return $this->extensions;

	} //FUNC loadExtensions

} //CLASS mwApplicationEd
?>