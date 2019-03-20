<?php
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

	public $statusList		= [];

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

	public	$statusMajor		= RM_STATUS_NEW;		// |- Major and minor application statuses.
	public	$statusMinor		= '';				// |

	public	$amount			= 0;				// Amount for payment. Should be calculated by extensions on save.
	public	$transaction		= '';				// Transaction SN. Obtained during payment.

	public	$extensions		= [];				// Selected products list with submitted info.

	function __construct ($type = '') {

		$this

			->setField('id')				->Validations('isID')
			->setField('sn')				->Validations('trim|isSN(A)')

			->setField('type')				->Validations('isAlnum')

			->setField('modified')				->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, true, true)
			->setField('created')				->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, 'CURRENT_TIMESTAMP', false)

			->setField('userId', 'user_id')			->Validations('isID')

			->setField('statusMajor', 'status_major')	->Validations('isAlnum')
			->setField('statusMinor', 'status_minor')	->Validations('isAlnum')

			->setField('amount')				->Validations('isNumeric', 'Amount')->dbType('decimal', '11,2', '0')
			->setField('transaction')

			->setField('extensions')->Validations('')->SZ()->dbType('text', false)

		; //OBJECT $this

		// Storing type in self
		$this->type	= $type;

		// Generating new SN
		$this->sn	= newSN('A');
		
		//get major statuses
		$this->statusList = rmCfg()->getStatuses('', RM_STATUS_MAJOR);
		
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
	
	// ---- Filter ----
		
		$where = '';
		
		//add filter by type
		if ( !empty($options['type']) ) {
			
			if ( empty($where) )
				$where = ' WHERE ';
			else
				$where .= ' AND ';
			
			$where .= 'type = \'' . $options['type'] . '\'';
				
		} //IF type is set
		
		$orderby = '';
		
		//add sorting feature
		if ( !empty($options['sorting']) ) {
			
			$orderby .= ' ORDER BY ' . $options['sorting'];
				
		} //IF type is set
		
	// ---- Applications ----	
		
		// Loading items from DB
		$sql	= "SELECT * FROM {$this->Table}" . $where . $orderby;
		$res	= mwDB()->query($sql)->asArray('id'); 

		if ( !$res )
			return $res;

		// Post processing
		$users	= [];
		foreach ( $res as $id => &$row ) {
			
			// Unpacking extensions info
			$row['extensions'] = safeUnserialize($row['extensions']);
			
			// Collecting user IDs, to load users
			if ( $row['user_id'] )
				$users[]	= $row['user_id'];
			
		} //FOR each row

	// ---- Users ----	
		
		// Additionally checking users to make sure that there is what to search
		if ( $users ) {
			
			// Loading users data
			$uTable	= User::get()->Table;
			$sql	= "
				SELECT * FROM `{$uTable}`
				WHERE `id` in (".sqlValues($users).")
			";

			$users	= mwDB()->query($sql)->asArray('id');

			// Storing user data in each application row
			foreach ( $res as $id => &$row ) {
				
				// Skipping problematic ones
				if ( empty($row['user_id']) or empty($users[$row['user_id']]) )
					continue;
					
				// Storing user as subarray
				$row['user']	= $users[$row['user_id']];
				
			} //FOR each row
			
		} //IF found users
		
		return $res;

	} //FUNC getList

	function setStatus ($major = false, $minor = false, $options = []) {
		
	// ---- Options ----
	
		$options = array_merge( [
			'save'		=> true,		// Set FALSE to skip auto update in DB
			'trigger'	=> true,		// Set FALSE to do not trigger event
		], $options );
	
	// ---- Values ----

		// Checking states against current values
		// Doing in loop for code simplification
		// Collecting updated data for single call update
		$data = [];
		foreach ( ['status_major' => $major, 'status_minor' => $minor] as $cName => $status ) {
			
			// Nothing to do if status is not set
			if ( !$status )
				continue;
	
			// Gettining field name from alias
			$name	= $this->getField($cName)->Field;
			
			// For new applications - just setting and triggering
			// For existing applications - updating only if status changed
			if ( 
				!$this->id
				or ($this->id and $this->$name != $status)
			) {

				// Setting status in self, storing as data for update and triggering event				
				$this->$name	= $status;
				$data[$cName]	= $status;
				
				// Triggering if allowed
				(new mwEvent('regmgr.status.'.$status))
					->trigger($this);
				
			} //IF status updated
			
		} //FOR each scope

		// If there is somethinig to update - saving in DB
		// Updating manually to speedup and not mess up with other stuff
		if ( $data and $this->id and $options['save'] )
			mwDB()->update($this->Table, $data, ['id' => $this->id], 1);
		
		return $this;
		
	} //FUNC setStatus
	
	function getDescValues($field) {
		
		$sql	= '
			SELECT DISTINCT ' . $field . ' FROM ' . $this->Table . '
		';

		return mwDB()->query($sql)->asArray();
		
	} //FUNC getDescValues
	
} //CLASS rmApplication

