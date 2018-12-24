<?php

/** //** ----= CLASS tplApplication		 =---------------------------------------------------------------------\**//** \
*
* 	Application form template model.
*
* 	@package	Morweb
* 	@subpackage	regmgr
* 	@category	model
*
\**//** ------------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
class tplApplication extends vTpl2 {

	public	$backend		= false;			// Backend editor rendering flag.
//	public	$cart			= false;			// If cart is used - it will be loaded in here.

	public	$SectionName		= '';				// Section name.
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
	//	$this->parseForms($this);
		$this->parseExtensions($this);
	//	$this->parseDetails($this);
		$this->parseTables($this);
		$this->parseScripts($this);

	// ---- Post Render ----

		// Parsing general actions and formats
		$this->render()->actions();
		$this->render()->formats();

		// Hiding unused messages
		$this->render()->messages(false);

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
			$tabs	= [];
					
			// Using counter to autoname tabs names
			$i	= 1;
			
			$node->parse()->section('tabContent', function ($node) use (&$tabs, &$i) {

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
				]; //$tabs
			
			// ---- Body ----
			
				// Saving node body in collection 
				$tabs[$name]['body']	= $node->html();
				
				// Increasing couner
				$i++;	
				
			}); //FUNC parse tabContent
			
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

		$this->load->js('rmTabs.js');

		// Generating jsSn
		$jsSn		= ( !empty($node->attr['id']) and isVar($node->attr['id']) )? $node->attr['id'] : newSN('T');

		// Reading options data
		$jsOptions	= ( !empty($node->attr['options']) )? $node->attr['options'] : '{}';

		// Reading initial height
		// It will be updated with JS anyway, but looks better when it's locked initially
		$height		= ( $node->attr['height'] ) ? $node->attr['height'] : 300;
		
		// Using counter to autoselect first tab
		$i		= 0;
	?>
		<div class="rmTabs" id="<?=$jsSn?>" style="margin-bottom: <?=$height?>px;">

		<?php	foreach ( $tabs as $name => $row ) { ?>
		
				<input type="radio" name="<?=$jsSn?>_tab" id="<?=$name?>" class="mw" <?=($i++ == 0) ? ' checked="checked"' : ''?> />
				<label for="<?=$name?>" class="rmTabs-head"><?=$row['caption']?></label>
				<div class="rmTabs-content" data-for="<?=$name?>"><?=$row['body']?></div>

		<?php	} //FOR each tab ?>	

		</div>

		<hr />

		<script type="text/javascript">
		
			jQuery( function () {

				rmTabs('#<?=$jsSn?>', <?=$jsOptions?>);

				// Giving it small timeout to allow styleDialog to apply
				setTimeout( function () {

					// Getting tabs wrapper to work with
					var $wrap	= jQuery('#<?=$jsSn?>');
					
				<?php	if ( empty($node->attr['data-dynaheight']) ) { ?>					

						// Fast detecting max height on tabs, and applying margin to wrapper
						// Looping through contents and calculating heights
						var $height	= 0;
						$wrap.children('.rmTabs-content').each( function () {
							
							var $el	= jQuery(this);
							
							// Calculating height and coparing with biggest found
							var $h	= $el.outerHeight(true);
							if ( $h > $height )	
								$height = $h
							
						}); //FOR each children
						
						// Now we have biggest height, can force margin
						$wrap.css('margin-bottom', $height);

				<?php	} else { ?>					
					
						// Updating wrapper height on tab clicks
						$wrap.children('.rmTabs-head').on('click', function () {
							
							var $el		= jQuery(this);
	
							// Calculating height						
							var $height	= $el.next().outerHeight(true);
							$wrap.css('margin-bottom', $height);
							
						}); //FUNC onClick
						
						// Setting currently checked element height
						var $height = $wrap
							.children('input[type=radio]:checked').next().next()
							.outerHeight(true);
	
						$wrap.css('margin-bottom', $height);
	
				<?php	} //IF dynamic height?>					
				}, 10);
					
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

		// Using counter to autoselect first tab
		$i = 0;

	?>
		<dl class="mwDialog tools rmTabs-backend">

			<dd>
				<table class="mwWinTabs">
					<tr>

					<?php	foreach ( $tabs as $name => $row ) { ?>

							<td rel="<?=$name?>" onclick="mwSwitchTab(this);"<?=($i++ == 0) ? ' class="Selected"' : ''?>><?=$row['caption']?></td>

					<?php	} //FOR each tab ?>	

					</tr>
				</table>
			</dd>

		</dl>
	
	<?php	$i = 0; ?>
	
		<div class="winContainer">	

		<?php	foreach ( $tabs as $name => $row ) { ?>
		
				<div class="winContainer" id="<?=$name?>"<?=($i++ == 0) ? '' : ' style="display:none"'?>><?=$row['body']?></div>
		
		<?php	} //FOR each tab ?>	

		</div>
	<?php
	} //FUNC renderTabs_backend

/* ==== Backend ============================================================================================================= */

	/** //** ----= parseTables	=------------------------------------------------------------------------------\**//** \
	*
	* 	Parses frontend tables, preparing them to display in backend dialog.
	*
	* 	@see vTplNode parser for params.
	*
	*	@return	SELF
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function parseTables ($node) {

		if ( !$this->backend )
			return;

		$node->parse()->section('table', function ($node) {

			$node->attr['class'] = 'mwDialog '.$node->attr['class'];

		}); //FUNC parse table

		return $this;

	} //FUNC parseTables

	/** //** ----= parseScripts	=------------------------------------------------------------------------------\**//** \
	*
	* 	Parses on-template script, preparing template for displaying in backend editor.
	*
	* 	@see vTplNode parser for params.
	*
	*	@return	SELF
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function parseScripts ($node) {

		if ( !$this->backend )
			return;

		$node->parse()->section('script', function ($node) {

			return false;

		}); //FUNC parse script

		return $this;

	} //FUNC parseScripts

/* ==== Extensions ========================================================================================================== */

	/** //** ----= parseExtensions	=------------------------------------------------------------------------------\**//** \
	 *
	 * 	Applies form extensions.
	 *
	 * 	@see vTplNode parser for params.
	 *
	 *	@return	SELF
	 *
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function parseExtensions ($node) {

		$node->parse()->section('extension', function ($node) {

		// ---- Validating node ----

			// Checking setup
			if ( empty($node->attr['rel']) )
				return 'No extension specified.';

			$rel = $node->attr['rel'];

			if ( !isVar($rel) )
				return 'Invalid extension name: '.$rel;

			// Trying to load extension
			$wgt = $this->load->widget('RMApplicationEx', $rel);

			if ( !$wgt )
				return 'Failed to load extension: '.$rel;

		// ---- Render ----

			// Cleaning common attributes and applying remaining on widget
			$wgt->backend = $this->backend;
			$wgt->_fromArray($node->attr);

			// Rendering whatever extension needs to render
			return $wgt->_ob_render($node);

		}); //FUNC parese.extension

		return $this;

	} //FUNC parseExtensions

/* ==== Actions ============================================================================================================= */

	/** //** ----= save, submit	=------------------------------------------------------------------------------\**//** \
	*
	*	Basic action. Generates save and submit buttons.
	*
	\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
	function renderAction_save ($node) {

		if ( $this->backend )
			return false;

		// Adding prefix class to button
		// Which will be used by JS to initialize
		return $node->render()->basicAction([
			'prefix'	=> 'rmForm-action',
		]); //basicAction
		
	} //FUNC renderAction_save

	function renderAction_submit ($node) {

		if ( $this->backend )
			return false;

		// Save and submit are exactly same, differing only by submit flag
		// Which is decided by JS
		return $this->renderAction_save($node);

	} //FUNC renderAction_submit

/* ==== Helpers ============================================================================================================= */
	
} //CLASS tplApplication
?>