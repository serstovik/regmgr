var RM_BACKEND_AJAX = '/site/ajax/regmgr';

/** //** ----= CLASS rmApplicationAdmin	=------------------------------------------------------------------------------\**//** \
 *
 * 	Base regMgr js model for backend
 *
 * 	@package	MorwebCMS
 * 	@subpackage	regmgr
 * 	@category	model
 *
 \**//** -----------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
var rmApplicationAdmin		= function ($el) {

	return vEventObject(['onInit', 'onLoad', 'onSave'], {

	dom		: {

		container	: false,
		rows		: false,		// Applications rows
		actions		: false,

	},

	init		: function () {

		var $this	= this;

	// ---- Data ----

		// Making sure mwData is initialized
		if ( isEmpty(mwData.rmApps) )
			mwData.rmApps = {};

	// ---- DOM ----

		$el = _jq($el);

		$this.dom.container		= $el;
		$this.dom.rows			= $this.dom.container.find('tr');

	// ---- Events ----

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
		
		//init sorting select
		jQuery('#regmgr_sorting').off().change(function(e){
			
			var el = jQuery(this);
			
			mwAjax(RM_BACKEND_AJAX + '/index', {'sorting' :  el.val()} )
				.go()
				.success( function($data) {} )
				.error( function() {} )
			; // mwAjax
			
		});
		
		//init filter select
		jQuery('.regmgr_filter').off().change(function(e){
			
			var el = jQuery(this);
			
			/*
			var toSend = {};
			toSend.filter = [];
			toSend.filter.id = el.attr('id');
			toSend.filter.value = el.val();
			___(toSend);
			*/
			toSend = {};
			toSend.filterKey = el.attr('id');
			toSend.filterValue = el.val();
			
			mwAjax(RM_BACKEND_AJAX + '/index', toSend )
				.go()
				.success( function($data) {} )
				.error( function() {} )
			; // mwAjax
			
		});
		
		$this.onInit($this);

	}, //FUNC init

	/* ==== Actions ============================================================================================= */

	delete		: function ($appId) {

		// Sending form using AJAX
		mwAjax(RM_BACKEND_AJAX+'/deleteApplication/'+$appId, {}, true)

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

	},

}).init();};