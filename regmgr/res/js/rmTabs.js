/** //** ----= CLASS rmTab	=--------------------------------------------------------------------------------------\**//** \
*
* 	Tabs control helper.
*
* 	@package	MorwebCMS
* 	@subpackage	regmgr
* 	@category	model
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
jQuery.fn.rmTabs = function ($options) {

	if ( !this.length )
		return;

	return this.data('rmTabs').set($options);

} //FUNC rmTabs

var rmTabs		= function ($el, $options) {

	return vEventObject(['onInit', 'onTabRender', 'onTabEnter', 'onTabLeave'], {

	dom			: {				// Set of interesting dom elements

		wrap			: false,			// Tabs wrapper
		
		container		: false,			// Tabs contents container
		head			: false,			// Tabs buttons container

		controls		: false,			// Tab control inputs		
		buttons			: false,			// Tab captions buttons
		contents		: false,			// Tab bodies

	}, //$dom

	current			: {				// Information about current tab

		name			: '',				// Tab name

		control			: false,			// Tab control element
		button			: false,			// Tab button element
		content			: false,			// Tab content element

	}, //current

	height			: 300,				// Startup height
	dynaHeight		: false,			// Set TRUE to chage height of tabs dynamically on swap

/* ==== SETUP =============================================================================================================== */

	/** //** ----= set	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Updates self properties with given values.
	*
	*	@param	MIXED	$option		- Option to set. Can be data object to setup several properties.
	*	@param	MIXED	[$value]	- Value to set. Not used if object passes as first parameter.
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	set		: function ($option, $value) {

		var $this = this;

		// Values can come as object, or single value
		// Applying using object, for code unification below
		// Any variable are accepted, to allow custom data storage
		var $o = {};

		if ( arguments.length === 1 )
			$o = $option;
		else
			$o[$option] = $value;

	// ==== Events ====

		// Processing events, as those should be cleared before extending
		for ( var $i in $o ) {

			// Skipping non events and non funcitons
			if ( !this.__events[$i] || !isFunction($o[$i]) )
				continue;

			// Setting up event, and removing it from opitons
			this[$i]($o[$i]);
			delete($o[$i]);

		} //FOR each opiton

	// ==== Self ====

		jQuery.extend(this, $o);

		return this;

	}, //FUNC set

	/** //** ----= init	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Initiates dom and events.
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	init			: function () {

		var $this	= this;

		$el = _jq($el);

		$this.set($options);

	// ---- DOM ----

		// Storing self in container for later reuse
		$el.data('rmTabs', $this);

		$this.dom.wrap		= $el;
		$this.dom.container	= $el.find('.rmTabs-container');
		$this.dom.head		= false;				// There is no tabs head in this logic
		
		$this.dom.controls	= $el.find('.rmTabs-control');
		$this.dom.buttons	= $el.find('.rmTabs-button');
		$this.dom.contents	= $el.find('.rmTabs-content');

	// ---- Events ----

		// Adding switch stuff to buttons clicks
		$this.dom.buttons
			.on('click.rmTabs', function ($e) {

				// Switching to clicked tab
				return $this.switchTab( jQuery(this) );

			}); //FUNC onClick

		// Initializing next/prev buttons
		$this.dom.wrap.find('.rmTabs-action.prevTab')
			.on('click.rmTabs', function ($e) {

				// Looking for prev button and clicking it
				// This will make sure all necessary events will be triggered, including custom ones
				$this.current.button.prevAll('.rmTabs-button:visible:first')
					.trigger('click');

			}); //FUNC onClick

		$this.dom.wrap.find('.rmTabs-action.nextTab')
			.on('click.rmTabs', function ($e) {

				// Looking for prev button and clicking it
				$this.current.button.nextAll('.rmTabs-button:visible:first')
					.trigger('click');

			}); //FUNC onClick

		// Updating tabs heights on resize
		jQuery( window )
			.on('resize.rmTabs', function ($e) {

				$this.updateHeights();

			}); //FUNC onResize

	// ---- Current ----

		// Switching tab to current after small timeout
		// This make sure all initial stuff is rendered
		setTimeout( function () {
			
			$this.switchTab( $this.dom.controls.filter(':checked').next() );

			// Triggering init event
			$this.onInit($this);

		}, 10 );

		return $this;

	}, //FUNC init

/* ==== TABS ================================================================================================================ */

	/** //** ----= setCurrent	=------------------------------------------------------------------------------\**//** \
	*
	*	Initializes current tab.
	*
	*	@param	jQuery	$control	- Control element for tab.
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	setCurrent	: function ($control) {
		
		var $this = this;
		
		// Saving control, and getting name from it's id
		$this.current.control	= $control;
		$this.current.name	= $control.attr('id');

		// Button is adjacent to label
		$this.current.button	= $control.next();
		
		// Content follows next
		$this.current.content	= $this.current.button.next(); 
	
		return this;
		
	}, //FUNC setCurrent

	/** //** ----= switchTab	=------------------------------------------------------------------------------\**//** \
	*
	*	Selects specified tab.
	*
	*	@param	jQuery	$el	- Buttton being clicked.
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	switchTab	: function ($el, $click) {
		
		var $this = this;

		// Not doing anything if same button clicked
		if ( $this.current.button && $el.is($this.current.button) )
			return;

		// Triggering leave event first, passing current tab stuff
		// Skipping when there is no current though
		if ( $this.current.control )
			if ( $this.onTabLeave($this.current) === false )
				return false;

		// Removing selcted class form old current
		if ( $this.current.button )
			$this.current.button.removeClass('selected');

		// Setting clicked tab as current
		// Control will be imideately preceeding button
		$this.setCurrent( $el.prev() );
		
		// Running enter callback, it's allowed to cancel process
		if ( $this.onTabEnter($this.current) === false )
			return false;

		// Adding selected class
		$this.current.button.addClass('selected');

		// Executing render callback now
		// Passing contents element
		$this.onTabRender($this.current.contents);

		// Correcting heights
		$this.updateHeights();		

	}, //FUNC switchTab

/* ==== HELPERS ============================================================================================================= */

	/** //** ----= updateHeights	=------------------------------------------------------------------------------\**//** \
	*
	*	Updates heights, based on settings and currently selected tab.	
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	updateHeights	: function () {

		var $this = this;

		if ( $this.dynaHeight )
			
			// Updating to height of current element if dynaheight enabled
			var $height	= $this.current.content.outerHeight(true);
			
		else 

			// Always recalculating heights of elements, in case something custom was rendered
			var $height	= $this.dom.contents.maxHeight(true, true);				

		$this.dom.container.css('margin-bottom', $height );			

		return this;

	}, //FUNC updateHeights

}).init();}; //CLASS rmTabs
