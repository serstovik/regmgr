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

		// Also selecting selected editor tab
		$this.dom.form.find('.mwWinTabs td.selected').click();
		
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

	/** //** ----= apply	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Dialog save/reload. Adds tabs control.
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	apply		: function ($callback) {
			
		var $this	= this;
		
		// Saving current tabs positions
		// ToDo: stupport for mw3 winTabs.
		var $tabs	= {};
		
		// Searching tabs
		$this.Body.find('.mwWinTabs').each( function () {
			
			// Creating shortcuts
			var $el		= jQuery(this);
			var $id		= $el.attr('id');
			
			// Skipping ones without ID - can't store those
			if ( !$id )
				return;

			// Looking for selected tab, if none is selected - no need to store it
			// Using both mw3 and mw2 syntax
			var $sel	= $el.find('.Selected, .selected').first();
			
			// Saving current states and IDs, if tab have one
			var $rel	= $sel.attr('rel');
			
			if ( !$rel )
				return;
				
			$tabs[$id] = $rel;
			
		}); //FUNC each tab
		
		// Calling normal apply, adding custom callback
		$parent.apply.call($this, function ($data) {

			// Temporarily hiding form to speed up animations
			$this.Form.hide();	
			
			// Restoring saved tabs
			for ( var $id in $tabs ) {
				
				// Clicking instead of moving selection - this allows proper switching events
				$this.Body.find('#'+$id+' [rel='+$tabs[$id]+']')
					.click();
				
			} //FOR each tab

			// Displaying form again
			$this.Form.show();	
			
			// reIssuing callback
			if ( isFunction($callback) )
				$callback($data);
			
		}); //FUNC apply
		
	}, //FUNC apply
	
	del		: function ($id, $text) {
		
		var $this	= this;
		
		$text = jQuery('.regmgr-del-' + $id).attr('rel');
		
		// Calling parent
		$parent.del.call($this, $id, $text);
		
	}, //FUNC del
	
	}); //CLASS
	
})(); //OBJECT applicationEd
