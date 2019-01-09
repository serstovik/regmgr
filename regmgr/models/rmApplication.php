<?php
define('RM_STATUS_NEW', 'new');
define('RM_STATUS_OPEN', 'open');
define('RM_STATUS_SUBMIT', 'submit');
define('RM_STATUS_READY', 'ready');
define('RM_STATUS_APPROVED', 'approved');
define('RM_STATUS_DECLINED', 'declined');
define('RM_STATUS_CLOSED', 'closed');

/** //** ----= CLASS rmApplication	=------------------------------------------------------------------------------\**//** \
 *
 * 	Base regMgr application DB model.
 *
 * 	@package	MorwebCMS
 * 	@subpackage	regmgr
 * 	@category	model
 *
\**//** ------------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
class rmApplication extends vDBObject {

	public $statusList		= [RM_STATUS_NEW, RM_STATUS_OPEN, RM_STATUS_SUBMIT, RM_STATUS_READY, RM_STATUS_APPROVED, RM_STATUS_DECLINED, RM_STATUS_CLOSED];

// ---- SETTINGS ----

	public	$Table			= 'regmgr_applications'; 	// DB table to use.

	public	$SectionName		= '';				// Section name.
	public	$load			= false;			// Section loader.

// ---- FIELDS ----

	public	$id			= 0;				// DB id.
	public	$sn			= '';				// Application unique SN.

	public	$type			= '';				// Application type.

	public	$modified		= '';				// |
	public	$created		= '';				// |- Creation and last modification dates.

	public	$userId			= '';				// Application owner.

	public	$statusMajor		= 'new';			// |- Major and minor application statuses.
	public	$statusMinor		= '';				// |


	public	$extensions		= [];				// Selected products list with submitted info.

	function __construct ($type = '') {

		$this

			->setField('id')->Validations('isID')
			->setField('sn')->Validations('trim|isSN(A)')

			->setField('type')->Validations('isAlnum')

			->setField('modified')->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, true, true)
			->setField('created')->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, 'CURRENT_TIMESTAMP', false)

			->setField('userId', 'user_id')->Validations('isID')

			->setField('statusMajor', 'status_major')->Validations('isAlnum')
			->setField('statusMinor', 'status_minor')->Validations('isAlnum')

			->setField('extensions')->Validations('')->SZ()->dbType('text', false)

		; //OBJECT $this

		// Storing type in self
		$this->type	= $type;

		// Generating new SN
		$this->sn	= newSN('A');

		// Initiating self from type template
		$this->init();

	} //CONSTRUCTOR

	function init () {

		if ( !$this->type )
			return $this;

	// ---- Section ----

		$this->SectionName	= rmCfg()->get('sectionName');
		$this->load		= mwLoad($this->SectionName);

	// ---- HTML ---

		// Compiling template name from config
		$template		= rmCfg()->getTypes($this->type, 'template');
		$template		= compilePath($this->SectionName, $template);

		// Loading template to init from it
		$html			= $this->load->template($template);

		// No template - no process
		if ( empty($html) )
			throw( new Exception('Failed to load application template for type: '.$this->type.'.') );

		// Initiating self from form loaded, using mwForm
		mwLoad('system')->model('forms');

		// Creating empty mwForm, and passing self as data set
		$form = new mwForm();
		$form->Inputs = $this;

		// Initiating from from template loaded above
		// This will also initiate self accoring inputs on form.
		$form->init($html, false);

		return $this;

	} //FUNC init
	
	function getList ($options = []) {

		$sql	= "SELECT * FROM {$this->Table}";

		return mwDB()->query($sql)->asArray('id');

	} //FUNC getList

} //CLASS rmApplication

