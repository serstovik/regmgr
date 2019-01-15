/** //** ----= CLASS rmFormRows	=--------------------------------------------------------------------------------------\**//** \
*
* 	Form rows control helper.
*
* 	@package	MorwebCMS
* 	@subpackage	regmgr
* 	@category	model
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
jQuery.fn.rmFormRows = function ($options) {

	if ( !this.length )
		return;

	return this.data('rmFormRows').set($options);

} //FUNC rmFormRows

var rmFormRows		= function ($el, $options) {

	return vEventObject(['onInit', 'onAdd', 'onRemove'], {

	dom			: {				// Set of interesting dom elements

		wrap			: false,			// Extension wrapper
		
		source			: false,			// Source row used for duplicates
		container		: false,			// Duplicated rows container

	}, //$dom

	mask			: '__k__',			// Inputs key mask
	count			: 0,				// Current rows count

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
		$el.data('rmFormRows', $this);

		$this.dom.wrap		= $el;
		$this.dom.container	= $el.find('.rmFormRows-container');
		$this.dom.source	= $el.find('.rmFormRows-source');

		// Counting regular rows for iterator
		$this.count		= $this.dom.container.children().length;

		// Unstyling source, to simplify stuff
		// Using unstyled inputs allows to restyle them later, which will properly recreate attached events
		setTimeout( function () {
			unstyleDialog($this.dom.source);
		}, 10 );
		
	// ---- Events ----

		$this.dom.wrap.find('.rmFormRows-action.addRow')
			.on('click', function () {
				$this.addRow();				
			}); //FUNC onClick

		$this.dom.wrap.find('.rmFormRows-action.removeRow')
			.on('click', function () {
				$this.removeRow();				
			}); //FUNC onClick

		// Triggering init event
		$this.onInit($this);

		return $this;

	}, //FUNC init

/* ==== ROWS ================================================================================================================ */

	/** //** ----= addRow	=--------------------------------------------------------------------------------------\**//** \
	*
	*	Updates heights, based on settings and currently selected tab.	
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	addRow	: function () {

		var $this		= this;

		// Getting source as html to parse
		var $html		= $this.dom.source.html();

		// Increasing counter
		$this.count++;
		
		// Parsing key (iterator) on template
		$html			= $html.replace( new RegExp($this.mask, 'g'), $this.count);

		// Creating new element and appending to container
		var $el			= jQuery( $html ).appendTo($this.dom.container);

		// Styling added chunk
		styleDialog($el);
		
		// Triggering resize, to let everyone know that something on page was changed
		jQuery( window ).trigger('resize');

	}, //FUNC addRow

	/** //** ----= removeRow	=------------------------------------------------------------------------------\**//** \
	*
	*	Updates heights, based on settings and currently selected tab.	
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	removeRow	: function () {

		var $this = this;
		
		// Simply removing last row, untill 1 remain
		if ( $this.count <= 1 )
			return;

		$this.dom.container.children().last().remove();

		// Decreasing counter
		$this.count--;			

		// Triggering resize, to let everyone know that something on page was changed
		jQuery( window ).trigger('resize');

	}, //FUNC removeRow

/* ==== HELPERS ============================================================================================================= */

	/** //** ----= updateHeights	=------------------------------------------------------------------------------\**//** \
	*
	*	Updates heights, based on settings and currently selected tab.	
	*
	*	@return SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	updateHeights	: function () {


	}, //FUNC updateHeights

}).init();}; //CLASS rmFormRows
