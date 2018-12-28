/** //** ----= OBJECT applicationEd	=------------------------------------------------------------------------------\**//** \
*
* 	Application editor
*
* 	@package	Morweb
* 	@subpackage	regmgr
* 	@category	editor
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
var applicationEd = (function () {

	var $parent	= mwEditor();
	return jQuery.extend({}, $parent, {

	dom		: {			// Set of jQuery shortcuts to usefull elements

		body		: false,		// Editor body

	}, // dom

	type		: '',			// Current item type

	/** //** ----= init	=--------------------------------------------------------------------------------------\**//** \
	*
	*	General one time init. Build shortcuts to window, usefull elements and sets events.
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	init		: function ($force) {

		var $this = this;

		$parent.init.call(this, $force);

		// Creating jQuery shortcuts to usefull elements
		$this.dom.body		= $this.Body;

		return $this;

	}, //FUNC init

	/** //** ----= resetDialog	=------------------------------------------------------------------------------\**//** \
	*
	*	Resets dialog changes and internal values.
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	resetDialog	: function () {

		var $this	= this;

	// ---- Tabs ----

		// Clearing target in case no tabs happen
		$this.dom.tabs.html('');
		
		// Moving tabs to window head
		var $tabs	= $this.dom.formContents.find('.rmTabs-backend').appendTo( $this.dom.tabs );

		// Cleaning ghost mwDialog
		$tabs.find('.mwDialog').removeClass('mwDialog');

		return this; 

	}, //FUNC resetDialog

	/** //** ----= dialog	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Initiates dialog before display.
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	dialog	: function ($id) {

		var $this	= this;

	// ---- Dialog ----

		// Setting up self to always use selected type
		$this.ControllerGet = '?type='+$this.type;

		// Calling parent
		$parent.dialog.call($this, $id);

		return $this;

	}, //FUNC dialog
	
	del		: function ($id, $text) {
		
		var $this	= this;
		
		$text = jQuery('.regmgr-del-' + $id).attr('rel');
		
		// Calling parent
		$parent.del.call($this, $id, $text);
		
	}, //FUNC del
	
	}); //CLASS
	
})(); //OBJECT applicationEd
