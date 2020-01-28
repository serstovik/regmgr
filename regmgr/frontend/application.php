<?php
/**//** ----= CLASS mwApplication		=----------------------------------------------------------------------\**//** \
 *
 *
 *
 *\**//** --------------------------------------------------------------= by SerStoVik @ Morad Media Inc.	=------/** //**/
class mwApplication extends mwController {


	public	$AutoIndex	= true;		// Allow or not redirect unknown methods to index.

	public	$page		= false;	// |- Stores source page and widget objects  
	public	$wgt		= false;	// |
	
	public	$config		= false;

	function __init	() {

		$this->load->model('rmCfg');
		mwLoad('regmgr_attach')->model('rmAttach');

	} //FUNC init

	function index ($type = '', $id = '') {

	// ---- Vaidating ----
	
		$types = array_keys( rmCfg()->getTypes() );

		if ( empty($type) or !in_arrayi($type, $types) )
			throw( new Exception('Invalid type specified.') );

		if ( !empty($id) and !isID($id) )
			throw( new Exception('Invalid application specified.') );

	// ---- Models and resources ----
	
		// Loading App model
		$this->load->model('rmApplication');

		// Loading required resources
	//	mwLoad('system')->resource('mw.system', 'mw.forms');
		$this->load
			->js('regMgr.js')
			->css('regMgr.css');

	// ---- Application ----

		// Creating and initializing application model
		// Using clear application on template for extensions interractions
		// Or loading it with existing application if ID provided
		$app = new rmApplication($type);

		if ( $id ) {
			
			$app->loadByID($id);
			
			if ( !$app->id )
				throw( new Exception('Failed to laod specified application.') );
			
		} //IF existing application loading

		$tData = ['application' => $app];

	// ---- Payment ----
	
		// Loading payment config and checking amount
		// Input always present, but is ignored in some cases
		$pCfg	= rmCfg()->getTypes($type, 'payment');
		$amount	= 0;
		
		if ( !empty($pCfg['enabled']) and !empty($pCfg['amount']) )
			$amount	= $pCfg['amount']; 

	// ---- Template ----

		// Compiling template name from config
		$template = rmCfg()->getTypes($type, 'template');
		$template = compilePath($this->SectionName, $template);

		$tpl = $this->load->template($template, $tData, 'tplApplication');

		$tpl	
			->application($app)
			->main($tData);

		$html	= $tpl->html();
		
		//__($tpl->message('thank_you'));
		$thank_you = addslashes('<div style="width: 100%; text-align: center; font-size: 24px; ">Application saved. Thank you.</\' + \'div>');
		
	// ---- Form ----

		// Prefilling form from current app
		// Doing only for existing applications, as input defaults are set on template anyway
		if ( $app->id ) {
		
			$form	= new mwForm();
			$form->init($html)->Inputs($app)->setup();
		
			$html	= $form->HTML();
			
		} //IF existing application
	
		//showing files if they were already uploaded 
		//arrays for the files
		$thumbs_list 	= [];
		
		//loop user fields
		foreach($app->_fields as $k => $v) {
			
			//check is field has input - no input means its a base field (cant be file input)
			if ( empty( $v['input'] ) ) continue;
			
			//check is input type=file
			if ( $v['input']['type'] != 'File' ) continue;
			
			$fileName	= $v['input']['name'];
			//check is listing has some value for this input
			if ( !empty($app->$fileName) )
				$thumbs_list[] = array(
					'name' => $v['input']['name'], 
					'value' => $app->$fileName, 
					'title' => $v['input']['title']
				);
			
		}//foreach
		
	// ---- Render ----

		$sn = newSN('F');
	?>

		

		<script type="text/javascript">

			rmFilesData	= <?=json_encode($thumbs_list)?>

		</script>

		<form id="<?=$sn?>" action="" method="post" enctype="multipart/form-data" onsubmit="return false;">
			<input type="hidden" name="id" value="<?=$app->id?>" />
			<input type="hidden" name="type" value="<?=$type?>" />
			<input type="hidden" name="page" value="<?=$this->wgt->Page->ID?>" />
			<input type="hidden" name="widget" value="<?=$this->wgt->ID?>" />
			<input type="hidden" name="form" value="<?=$sn?>" />
			<input type="hidden" name="amount" value="<?=$amount?>" />
			<input type="hidden" name="files" value="" />
			<input type="hidden" name="submit" value="0" />
			<?=$html?>
		</form>
		
		<script type="text/javascript">

			jQuery( function () {
				
				rmApplication_inst = rmApplication({
					sn		: '<?=$sn?>',
					el		: '#<?=$this->wgt->jsId?>',
					thank_you	: '<?=$thank_you?>',
				});
				
			}); //jQuery.onLoad

		</script>
	<?php
	} //FUNC index

	function save () {

	// ---- Validating ----

		$types = array_keys( rmCfg()->getTypes() );

		if (
			empty($_POST['type'])
			or !isAlnum($_POST['type'])
			or !in_arrayi($_POST['type'], $types)
		)
			throw( new Exception('Invalid type specified.') );

		// Initiating widget
		$this->_initWidget();

		if ( empty($_POST['form']) or !isSN($_POST['form'], 'F') )
			throw( new Exception('Invalid source form.') );

		// Using flag to trigger immediate payment if enabled
		$isPayment = false;

	// ---- Model ----

		// Loading involved models
		$this->load->model('rmApplication');

		// Creating and initializing main model
		$app = new rmApplication($_POST['type']);

		// If application have ID - loading it
		// This allows partial updates and status checks
		if ( !empty($_POST['id']) or !isID($_POST['id']) ) {
			
			$app->loadByID($_POST['id']);
			
		} //IF ID provided

	// ---- POST and FILES ----

		// Validating general application inputs
		if ( $v = $app->validate($_POST) )
			throw( new mwValidationEx('Wrong info provided.', $v, $_POST['form']) );

		//todo: add files logic
		$Attacher	= new rmAttach();
		if (isset($app->sn))
			$_POST['sn']	= $app->sn;

		$Attacher->uploadAndSaveDocument();

	// ---- User ----
	
		// Setting up current user
		$app->userId	= User::ID();
		
		// Validating, as currently applications can't be without users
		if ( !$app->userId )
			throw( new Exception('Please login or register first.') );

	// ---- Payment ----
	
		// Validating amount input, just resetting in case of shit
		if ( empty($_POST['amount']) or !isNumeric($_POST['amount']) )
			$_POST['amount'] = 0;
	
		// Loading payment config and checking amount
		$pCfg	= rmCfg()->getTypes($_POST['type'], 'payment');

		// Making sure amount is present in condig
		if ( empty($pCfg['amount']) )
			 $pCfg['amount'] = 0;
	
		// Checking payment settings
		// If no payment - forcefully zeroing amount 
		if ( 
			empty($pCfg) 
			or empty($pCfg['enabled']) 
		)
			$_POST['amount'] = 0;

		// If custom amount is not allowed - forcing value
		elseif ( empty($pCfg['customAmount']) )
			$_POST['amount'] = $pCfg['amount'];
			
		// If custom amount is enabled - leaving as is

	// ---- DB ----

		// Making sure table is up to date
		$app->createTable()->updateTable();
		
		// Filling app with data and saving to get ID
		$app->fromArray($_POST);

		// Finally saving application into DB
		$app->toDB();
	
	// ---- States ----		

		// At this point - ID is defined... or something went horrobly wrong, which raised exceptions above
		
		// Chekcing if application is submitted
		// Processing each state separately, for better controll
		if ( !empty($_POST['submit']) && $_POST['submit'] == '1' ) {
			
			// Submit is set only for new or opened applications
			if ( empty($app->statusMajor) or in_array($app->statusMajor, [RM_STATUS_NEW, RM_STATUS_OPEN]) ) {

				// Setting submit status and triggering it
				$app->setStatus(RM_STATUS_SUBMIT);
				
			// ---- Payment -----	
				
				// Checking if payment is enabled
				$payCfg	= rmCfg()->getTypes($app->type, 'payment');
				
				// If payment is enabled - keeping app in submit status, 
				// and adding redirect if immediate payment is set
				// Otherwise setting to ready state right here
				// Skipping payment if zero amount though 
				if ( $app->amount and !empty($payCfg['enabled']) ) {

					// ToDo: should calculate amount here
					
					// If payment is on after submit - need to add redirect
					if ( $payCfg['enabled'] == 'onSubmit' ) {
						
						// Redirecting to payment page
						$this->addAjax('redirect', '/'.$this->page->Name.'/application/payment/'.$app->id);
						
					} //IF payment is enabled on submit
					
				} //IF payment is enabled
				else {

					// Without payemnt setting ready status right away
					$app->setStatus(RM_STATUS_READY);
					
				} //IF no payment enabled

			} //change status to submit
			
			// Just in case - leaving application open
			else {

				// Just setting open state 			
				$app->setStatus(RM_STATUS_OPEN);
				
			} //IF submitted from weird state

		} //IF submit button clicked
		
		else {

			// Just setting open state 			
			$app->setStatus(RM_STATUS_OPEN);
			
		} //IF simple save

	// ---- Result ----
		
	} //FUNC save

	function payment ($id = 0) {

		// Loading payment related stuff
		list($app, $cfg, $amount) = $this->_getPaymentCommons($id);

	// ---- Resources ----

		// Loading system resources
		// System for ajax, forms for form styles and auth.js for handy JS submit wrapper
		mwLoad('system')
			->resource('mw.system', 'mw.forms')
			->java('block.auth.js')
		; //mwLoad
		
	// ---- Template ----	

		// Using SN to mark the form
		$sn		= newSN('RM');

		// Loading template html, passing some interesting values		
		$vData	= [
			'app'		=> $app,
			'cfg'		=> $cfg,
			'wgt'		=> $this->wgt,
			'amount'	=> $amount,
		]; //$vData		

		$file	= compilePath($this->SectionName, 'payments', $cfg['template']);
		$html	= $this->load->template($file, $vData);

		if ( !$html )
			throw( new Exception('Payment template is not provided.') );

		// Using inline TPL parsing cuz in rush	
		$tpl	= new vTpl2($html);
		
		// Parsing billing form
		$tpl->parse()->section('billingForm', function ($node) {

			// Loading billing form and required resources
			mwLoad('ecomm')->model('essentials');
			mwLoad('ecomm')->model('funds');
			
			$file	= compilePath('payments/gates', $this->wgt->service.'.php');
			$billingForm	= $this->load->template( $file );
			
			if ( empty($billingForm) )
				throw( new Exception('Billing form is not provided.') );
			
			return $billingForm;
		}); //FUNC parse billingForm
		
		// Parsing application as vars 
		$vars	= [
			'application'	=> array_merge($app->asArray(), ['amount' => $amount]),
			'user'		=> User::Data(),
		]; //$vars

		$tpl->parse()->vars($vars);
		
		// Getting html back and rendering form
		$html	= $tpl->html();
	
	// ---- FORM ----
		
	?>
		<form id="<?=$sn?>" method="post" action="/ajax/regmgr/application/paymentSubmit" onsubmit="return mwAuth.submitForm(this, 0)">
		
			<input type="hidden" name="id" value="<?=$app->id?>" />
			<input type="hidden" name="xtoken" value="<?=getToken()?>" />
		
			<input type="hidden" name="page" value="<?=$this->wgt->Page->ID?>" />
			<input type="hidden" name="widget" value="<?=$this->wgt->ID?>" />
			<input type="hidden" name="form" value="<?=$sn?>" />
			
		<?php	if ( !empty($cfg['customAmount']) ) { ?>
		
				<input type="hidden" name="amount" value="<?=$amount?>" />
		
		<?php	} //IF custom amount is allowed ?>
			
			<?=$html?>
			
		</form>

		<script type="text/javascript">

			jQuery( function () {

			// ---- Init ----

				setTimeout( function () {
					styleDialog('#<?=$sn?> .Dialog');
				}, 1 );
				
			}); // jQuery.onLoad

		</script>

	<?php	
	} //FUNC payment
	
	function paymentSubmit () {

	// ---- Validating ----
	
		// Making sure application ID is set
		if ( empty($_POST['id']) or !isID($_POST['id']) )
			throw( new Exception('Invalid applicaiton specified.') );

		$id	= (int)$_POST['id'];

		// Validating form SN
		if ( empty($_POST['form']) or !isSN($_POST['form'], 'RM') )
			throw( new Exception('No form specified.') );
		
		// ToDo: validate token
		
		// Validating and Loading payment related stuff
		list($app, $cfg, $amount) = $this->_getPaymentCommons($id);

		// Validating and loading page widget for options
		$this->_initWidget();
		
		$wgt	= $this->wgt;

	// ---- User ----
	
		$user = User::get();
		
	// ---- Amount ----
	
		// Checking if custom amount is allowed
		if ( 
			!empty($cfg['customAmount'])
			and isset($_POST['amount']) 
			and isNumeric($_POST['amount']) 
		)
			 $amount	= $_POST['amount'];

		// Fixing amount in post for transaction
		$_POST['amount'] = $amount;

	//---- Gateway -----
		
		mwLoad('services')->model();
		mwSVC::model('ecomm');
		$gate = mwService($wgt->service);

		if ( !$gate )
			throw( new Exception('Failed to load payment service widget') );

		$gate->Form	= $_POST['form'];
		$gate->Email	= $user->Email;

	// ---- SandBox ----

		// Forcing sandbox mode (resetting whatever was set by service)
		if ( $wgt->sandbox == '1' )
			$gate->setCreds('sb');
		else
			$gate->setCreds('live');

		// Setting simulation modes if necessary
		// Defaulting to success untill more complicated simulation modes editor will be implemented 

		$gate->simRequest	= ( $wgt->simRequest ) ? [ECOMM_SIM_SUCCESS] : false;
		$gate->simResponse	= ( $wgt->simResponse ) ? ECOMM_SIM_SUCCESS : false;

//		$gate->simRequest	= array(ECOMM_SIM_SUCCESS, ECOMM_SIM_BAD_CC);
//		$gate->simResponse	= ECOMM_SIM_SUCCESS;
//		$gate->simResponse	= ECOMM_SIM_REC_SUCCESS;
//		$gate->simResponse	= ECOMM_SIM_BAD_CC;

	// ---- Payment ----

		//transaction options
		$options = array(
			'currency' => $wgt->currency,
		); //$options
		
		// Creating transaction based on post
		$tr = $gate->newTransaction($options)->fromPost($_POST);

		/// Processing poyment		
		if ( $gate->charge() === FALSE )
			return FALSE;

		$_POST['transaction'] = $tr->Order->REF;

	// ---- Application ----
	
		// Updating application
		// Preventing save on status, as will save everything later anyway
		$app
			->setStatus(RM_STATUS_READY, '', ['save' => false])
			->amount($amount)
			->transaction($tr->Order->REF)
			->toDB()
		; //$app 

	// ---- Return ----
	
		// Adding message and redirect if set
		$msg	= $wgt->message;
		$rUrl	= $wgt->redirect;
		
		// Validating url to be local
		if ( !isLocalURL($rUrl) )
			$rUrl	= '';

		// Message is always present if no redirect
		// Just replacing entire form with message
		if ( $msg or !$rUrl)
			$this->addContent($_POST['form'], $msg ? $msg : '&nbsp;'); 
	
		// Also adding redirect
		if ( $rUrl )
			$this->addAjax('redirect', $rUrl);
	
	} //FUNC paymentSubmit

/* ==== Helpers ============================================================================================================= */

	function _initWidget () {

	// ---- Validating ----

		if ( empty($_POST['page']) or !isID($_POST['page']) )
			throw( new Exception('Invalid page specified.') );

		if ( empty($_POST['widget']) or !isID($_POST['widget']) )
			throw( new Exception('Invalid page specified.') );

	// ---- Models ----

		// Using page to get main widget
		mwLoad('pages')->model();

		$this->page	= (new mwPage())
			->loadById($_POST['page']);

		// Saving widget in self
		$this->wgt = $this->page->getWidget($_POST['widget']);

		// Done.
		return $this;

	} //FUNC initWidget
	
	function _loadApp ($id) {
		
		if ( empty($id) or !isID($id) )
			throw( new Exception('Invalid application specified.') );

		// Loading App model
		$this->load->model('rmApplication');

		$app	= new rmApplication();

		// Loading manually, untill proper DB load implemented in model
		$sql	= "SELECT * FROM {$app->Table} WHERE `id` = {$id} LIMIT 1";
		$data	= mwDB()->query($sql)->asRow();

		// Completing model initialization if got results
		if ( empty($data['id']) )
			throw( new Exception('Invalid application specified.') );
			
		$app
			->type($data['type'])
			->init()
			->fromArray($data);
		
		return $app;
		
	} //FUNC _loadApp

	function _getPaymentCommons ($id) {
		
		// Validating and loading application model
		$app = $this->_loadApp($id);

		// Can pay only for submitted status
		// ToDo: review necessarity of this check
		// ToDo: extended status reacion
		if ( $app->statusMajor != RM_STATUS_SUBMIT )
			throw( new Exception('Application have incorrect state.') );

		// Checking if payment is enabled for this type and getting base amount
		$cfg		= rmCfg()->getTypes($app->type, 'payment');
		
		if ( empty($cfg) or empty($cfg['enabled']) )
			throw( new Exception('Application type does not support payments') );
		
	//	$amount		= empty($cfg['amount']) ? 0 : $cfg['amount']; 
		$amount		= $app->amount; 
		
		// Result is optimized for list
		return [$app, $cfg, $amount];
		
	} //FUNC _getPaymentCommons
	
	function sb () {
		
		$app = $this->_loadApp(30);
		
		__($app);
		
		$app->setStatus(RM_STATUS_SUBMIT, 'tested');
		
		__($app->id, $app->statusMajor);
		
	} //FUNC
	
}//CLASS mwApplication