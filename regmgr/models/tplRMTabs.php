<?php

/** //** ----= CLASS tplRMTabs		 =-----------------------------------------------------------------------------\**//** \
*
* 	Tabs rendering model.
*
* 	@package	Morweb
* 	@subpackage	regmgr
* 	@category	model
*
\**//** ------------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
class tplRMTabs extends vTpl2 {

	public	$backend		= false;			// Backend editor rendering flag.
	public	$load			= false;			// Section loader.

	/** //** ----= main	=--------------------------------------------------------------------------------------\**//** \
	*
	* 	Main parser logic. Used as entry point for template parsing.
	*
	*	@return	SELF
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function main ($vars = []) {

	// ---- Section ----

		$this->SectionName	= rmCfg()->get('sectionName');
		$this->load		= mwLoad($this->SectionName);

	// ---- Main tags ----
	
		// Splitting big parsers to dedicated parse/render methods for code readability
		$this->parseTabs($this);

	// ---- Post Render ----

		// Parsing general actions
		$this->render()->actions();

		return $this;

	} //FUNC main	

/* ==== Tabs ================================================================================================================ */

	/** //** ----= parseTabs	=------------------------------------------------------------------------------\**//** \
	*
	* 	Tabs toolkit.
	* 
	* 	@see vTplNode parser for params.
	*
	*	@return	SELF
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function parseTabs ($node) {

		$node->parse()->section('tabs', function ($node) {

			// Loading CI's reflector helper
			CI()->load->helper('inflector');

			// Parsing all tab contents and collecting headers/bodies as array
			// After parse - rendering tabs using appropiate renderer
			$tabs		= [];
			
			// Marking selected tab
			$selected	= false;
					
			// Using counter to autoname tabs names
			$i	= 1;
			
			$node->parse()->section('tabContent', function ($node) use (&$tabs, &$i, &$selected) {

			// ---- Head ----
				
				// Reading head
				$name	= '';
				$cap	= '';
				
				// Checking available attributes and generating name for tab
				if ( $node->attr['name'] )
					
					// If name specified - just using it
					$name = $node->attr['name'];
					
				elseif ( $node->attr['caption'] )
					
					// If no name, but caption - creating name from name
					$name = underscore($node->attr['caption']);
				
				else
					
					// Finally - have to use counter
					$name = 'tab_'.$i;
				
				// Similar logic for caption, except this time we have name
				if ( $node->attr['caption'] )
				
					// If have caption - using it
					$cap = $node->attr['caption'];
				else
					
					// Otherwise - just using name
					$cap = humanize($name);
			
				// Saving it
				$tabs[$name] = [
					'name'		=> $name,
					'caption'	=> $cap,
					'selected'	=> false,
				]; //$tabs
			
				// Checking if tab is marked for selection
				
				if ( !empty( $node->attr['selected'] ) )
					$selected	= $name;
			
			// ---- Body ----
			
				// Saving node body in collection 
				$tabs[$name]['body']	= $node->html();
				
				// Increasing couner
				$i++;	
				
			}); //FUNC parse tabContent
			
			// Selecting some tab
			if ( !$selected )
				$selected = reset($tabs)['name'];
			
			// Adding selected marker
			$tabs[$selected]['selected'] = true;
				
			// Rendering tabs using selected renderer
			// ToDo: implement more rendering methods for frontend and give designers ability to choose
			if ( $this->backend )
				return $this->_ob_renderTabs_backend($node, $tabs);
			else
				return $this->_ob_renderTabs_radios($node, $tabs);
				
		}); //FUNC parse tabs
		
		return $this;
		
	} //FUNC parseTabs

	/** //** ----= renderTabs_radios	=----------------------------------------------------------------------\**//** \
	*
	* 	Rendering tabs using radios css trick.
	*
	* 	@see vTplNode parser for params.
	*
	*	@return	SELF
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function renderTabs_radios ($node, $tabs) {

		$this->load
			->js('rmTabs.js')
			->css('rmTabs.css')
		; // load

		// Defaulting attributes
		$node->attr = array_merge([
			'id'		=> newSN('T'),
			'options'	=> '{}',
			'height'	=> 300,
			'dynaheight'	=> 0,
		
		], $node->attr); // merge

		// Reading initial height
		// It will be updated with JS anyway, but looks better when it's locked initially
		$height		= $node->attr['height'];

		// Generating jsSn
		$jsSn		= $node->attr['id'];

		// Reading options data, and compiling additional options
		$jsOptions	= $node->attr['options'];
		$jsAttr		= [
			'height'	=> $height,
			'dynaHeight'	=> toBool($node->attr['dynaheight']),
		]; //$jsAttr

		$jsOptions = glueJson($jsOptions, json_encode($jsAttr) );

	?>
		<div class="rmTabs" id="<?=$jsSn?>">
		
			<div class="rmTabs-container" style="margin-bottom: <?=$height?>px;">

		<?php	foreach ( $tabs as $name => $row ) { ?>
		
				<input type="radio" name="<?=$jsSn?>_tab" id="<?=$name?>" class="rmTabs-control mw" <?=($row['selected']) ? ' checked="checked"' : ''?> />
				<label for="<?=$name?>" class="rmTabs-button"><?=$row['caption']?></label>
				<div class="rmTabs-content" data-for="<?=$name?>"><?=$row['body']?></div>

		<?php	} //FOR each tab ?>
			
			</div>

		</div>

		<script type="text/javascript">
		
			jQuery( function () {

				rmTabs('#<?=$jsSn?>', <?=$jsOptions?>);

			}); //jQuery.onLoad
		
		</script>
	<?php
	} //FUNC renderTabs_radios

	/** //** ----= renderTabs_backend	=----------------------------------------------------------------------\**//** \
	*
	* 	Rendering tabs for backend editor.
	*
	* 	@see vTplNode parser for params.
	*
	*	@return	SELF
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function renderTabs_backend ($node, $tabs) {
	?>
		<dl class="mwDialog tools rmTabs-backend">

			<dd>
				<table class="mwWinTabs">
					<tr>

					<?php	foreach ( $tabs as $name => $row ) { ?>

							<td rel="<?=$name?>" onclick="mwSwitchTab(this);"<?=($row['selected']) ? ' class="Selected"' : ''?>><?=$row['caption']?></td>

					<?php	} //FOR each tab ?>	

					</tr>
				</table>
			</dd>

		</dl>
	
	<?php	$i = 0; ?>
	
		<div class="winContainer">	

		<?php	foreach ( $tabs as $name => $row ) { ?>
		
				<div class="winContainer" id="<?=$name?>"<?=($row['selected']) ? '' : ' style="display:none"'?>><?=$row['body']?></div>
		
		<?php	} //FOR each tab ?>	

		</div>
	<?php
	} //FUNC renderTabs_backend

/* ==== Actions ============================================================================================================= */

	/** //** ----= nextTab, prevTab	=------------------------------------------------------------------------------\**//** \
	*
	*	Basic action. Generates buttons for tabs navigation.
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function renderAction_switchTab ($node, $prev = false) {

		if ( $this->backend )
			return false;

		// Adding prefix class to button
		// Which will be used by JS to initialize
		return $node->render()->basicAction([
			'prefix'	=> 'rmTabs-action',
		]); //basicAction

	} //FUNC renderAction_switchTab

	function renderAction_nextTab ($node) {

		return $this->renderAction_switchTab($node, false);

	} //FUNC renderAction_nextTab

	function renderAction_prevTab ($node) {

		return $this->renderAction_switchTab($node, true);

	} //FUNC renderAction_prevTab

/* ==== Helpers ============================================================================================================= */

} //CLASS tplRMTabs
?>