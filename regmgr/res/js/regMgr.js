var RM_FRONTEND_AJAX = '/ajax/regmgr/application';

/** //** ----= CLASS rmApplication	=------------------------------------------------------------------------------\**//** \
 *
 * 	Base regMgr application DB model.
 *
 * 	@package	MorwebCMS
 * 	@subpackage	regmgr
 * 	@category	model
 *
 \**//** -----------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
var rmApplication	= function ($el) {

	return vEventObject(['onInit', 'onLoad', 'onSave'], {

	dom		: {			// Set of shortcuts to useful elements

		container	: false,		// Application form container
		form		: false,		// Application form elemenet

		actions		: false,		// Action inputs

		loader		: false,		// Form loader
		status		: false,		// Form status

		tabs		: {			// Tabs elements
			wrap		: false,		// Tabs buttons wrapper
			buttons		: false,		// Tabs buttons
			contents	: false,		// Tabs contents
		}, //tabs

	}, // dom

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

	init		: function () {

		var $this = this;
		
	// ---- DOM ----

		$el = _jq($el);

		$this.dom.container	= $el;
		$this.dom.form		= $this.dom.container.find('form');

		$this.dom.actions	= $this.dom.form.find('.rmForm-action');
		$this.dom.loader	= $this.dom.form.find('.rmForm-loader');
		$this.dom.status	= $this.dom.form.find('.rmForm-status');

	// ---- Events ----

		$this.dom.actions.filter('.save')
			.on('click.rmForm', function () {

				$this.save();

			}) //FUNC save.onClick

		$this.dom.actions.filter('.submit')
			.on('click.rmForm', function () {

				$this.save(1);

			}) //FUNC submit.onClick

		styleDialog($this.dom.form);

		$this.onInit($this);

	}, //FUNC init

/* ==== Submit ============================================================================================================== */

	save		: function ($submit) {

		var $this = this;

		// Setting submit flag
		$this.dom.form.find('[name=submit]').val( $submit ? '1' : '0' );

		// Sending form using AJAX
		mwAjax(RM_FRONTEND_AJAX+'/save', $this.dom.form.get(0), false)

			.start( function ($data) {
			//	$this.state($action, true);
			}) //FUNC start

			.stop( function ($data) {
			//	$this.state($action, false);
			}) //FUNC srop

			.success( function ($data) {
			}) //FUNC success

			.error( function ($data) {
				//__($data);
			}) //FUNC error

			.go();

	}, //FUNC save

}).init(); } //CLASS rmApplication