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

	public	$SectionName		= '';				// Section name.
	public	$load			= false;			// Section loader.

	public	$application		= false;			// Current application used for template loading

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
		$this->parseExtensions($this);
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
		
		// Using dedicaated tabs model for tabs parsing
		$tabsTpl		= $this->load->model('tplRMTabs', true);
		
		// Setting params
		$tabsTpl
			->html( $node->html() )		// Giving it self html		
			->backend( $this->backend )	// Specifying if rendering for backned
			->load( $this->load )		// Providing loader
			->main()			// Parsing
		; //$tabsTpl

		// Geting result back
		$node->html( $tabsTpl->html() );
		
	} //FUNC parseTabs

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

			// Defining extension name
			if ( !$wgt->extName )
				$wgt->extName = $rel;

			// Setting up widget
			$wgt->tpl		= $this;
			$wgt->application	= $this->application;
			$wgt->data		= ( !empty($this->application->extensions[$wgt->extName]) ) ? $this->application->extensions[$wgt->extName] : [];

			// Rendering whatever extension needs to render
			$html = $wgt->_ob_render($node);

			// Renaming inputs to make sure they are stored in DB correctly
			// All extension inputs sould be added into extensions subarray, indexed by extension name		
			
			// Using vTpl for inputs parsing
			$tpl	= new vTpl2($html);
			
			$tpl->parse()->inputs( function ($node) use ($rel, $wgt) {
				
				return $node->render()->prefixInput('extensions['.$wgt->extName.']', true);
				
			}); //FUNC render.inputs

			$html = $tpl->html();

			// Done
			return $html;

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