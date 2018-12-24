/** //** ----= CLASS rmTabs	=--------------------------------------------------------------------------------------\**//** \
 *
 * 	Tabs control helper.
 *
 * 	@package	MorwebCMS
 * 	@subpackage	regmgr
 * 	@category	model
 *
 \**//** -----------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
var rmTabs		= function ($el, $options) {

	return vEventObject(['onInit', 'onTabRender', 'onTabEnter', 'onTabLeave'], {

	dom			: {

		container		: false,		// Tabs wrapper
		tabHeads		: false,		// Tab captions list
		tabContents		: false,		// Tab bodies list

	}, //$dom

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
	 \**//** ----------------------------------------------------------------------------------= by SerStoVik =----/** //**/
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

	init			: function () {

		var $this	= this;

		$el = _jq($el);

		$this.set($options);

		$this.dom.container		= $el;
	//	$this.dom.rows			= $this.dom.container.find('tr');

		// Storing self in container for later reuse
		$this.dom.container.data('rmTabs', $this);

	// ---- Events ----

		$this.dom.container.find( '.rmTabs-head' ).click( function ($e) {
			$this.onTabLeave(jQuery(this));
		} );

/*/

		// Setting up row actions
		$this.dom.rows.each( function () {

			var $row	= jQuery(this);
			var $aId	= $row.attr('data-id');
			var $type	= $row.attr('data-type');

			$row.find('.edit')
				.off('click.rmIndexAction')
				.on('click.rmIndexAction', function () {

					applicationEd.type	= $type;
					applicationEd.dialog($aId);

				});

			$row.find('.Delete')
				.off('click.rmIndexAction')
				.on('click.rmIndexAction', function () {

					applicationEd.type	= $type;
					applicationEd.del($aId, 'Are you sure to delete this application ({first_name})?');

				});

		}); //FUNC each.row
/**/
		$this.onInit($this);

		return $this;

	}, //FUNC init

}).init();}; //CLASS rmTabs

jQuery.fn.rmTabs = function ($options) {

	return this.data('rmTabs').set($options);

} //FUNC rmTabs