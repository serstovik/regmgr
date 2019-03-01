<?php

class mwRegMgrPayment extends mwController {

	public $htmlSn		= 'regmgrPayment';	// Id used in form html
	
	public $AutoIndex	= true;
	
	public $userId		= 0;		// User id from users table
	public $groupId		= 0;		// Group id

//	public $hofModel	= false;	// |
	public $user		= false;	// |- Instances of common models
	public $group		= false;	// |
	public $form		= false;	// |

	public $gate		= '';		// Paymenr gateway service name
	public $amount		= 0;		// Amount to pay
	public $currency	= 'USD';	// CURRENCY to pay
	
	public $rr_use		= 1;		// Recurring usage scenario: 0 - Disabled, 1 - Enabled, 2 - Forced 
	public $rr_ptype	= 'O';		// Recurring period type O - once, M - monthly, Y - yearly
	public $rr_pstep	= 1;		// Recurring period step
	public $rr_count	= 1;		// Recurring payments count
	
	//Collection of variables for content parsing
	//need to generate on render
	public $contentVars	= array();
	
	function __init() {
		
		parent::__init();

	}//init

	function _initModels ($initUser = false){
		
		$cfg = rmCfg()->getBranch('payment');
		
		__($cfg);
		
		return;
		
		$this->gate	= $cfg['gateway']['Service'];
		$this->amount	= $cfg['gateway']['Amount'];
		$this->currency	= strtoupper($cfg['gateway']['Currency']);
		
		//check is custom amount exists
		if( !empty($_POST['cost_amount']) && !$cfg['base']['FreeRegistration'] ) {
			
			//check is amount short version of amount lists
			if ( is_numeric($_POST['cost_amount']) ) {
				
				//check is selected amount exists in config
				if ( !empty($cfg['gateway']['amounts']) && !empty($cfg['gateway']['amounts'][$_POST['cost_amount']]) )
					$this->amount = $cfg['gateway']['amounts'][$_POST['cost_amount']];
				
			}
			else {
				
				//check is amount exists as plan
				if( !empty($cfg['gateway']['plans']) && !empty($cfg['gateway']['plans'][$_POST['cost_amount']]) ) {
					
					//load defaul plan first
					if ( !empty($cfg['gateway']['plans']['default']) ) {
						
						$def = $cfg['gateway']['plans']['default'];
						
						//amount
						if ( !empty($def['amount']) ) $this->amount = $def['amount'];
						
						//check is recurring set
						if ( !empty($def['recurring']) ) {
							
							$this->rr_use = 2;
							
							//init default recurring type							
							if ( $def['recurring'] == 'monthly' )
								$this->rr_ptype = 'M';
							else if ( $def['recurring'] == 'yearly' )
								$this->rr_ptype = 'Y';
							
							//init default step
							if ( !empty($def['step']) ) $this->rr_pstep = (int)$def['step'];
							//init default count
							if ( !empty($def['count']) ) $this->rr_count = (int)$def['count'];
							
						}
						
					}
					
					//load current plan
						
					$cur = $cfg['gateway']['plans'][$_POST['cost_amount']];
					
					//amount
					if ( !empty($cur['amount']) ) $this->amount = $cur['amount'];
					
					//check is recurring set
					if ( !empty($cur['recurring']) ) {
						
						//init default recurring type
						if ( $cur['recurring'] == 'monthly' )
							$this->rr_ptype = 'M';
						else if ( $cur['recurring'] == 'yearly' )
							$this->rr_ptype = 'Y';
						
						//init default step
						if ( !empty($cur['step']) ) $this->rr_pstep = (int)$cur['step'];
						//init default count
						if ( !empty($cur['count']) ) $this->rr_count = (int)$cur['count'];
						
					}
					
				}
			}
			
		}
		
		$uri = $this->wgt->Page->uri;
		
		//get user group ID from uri
		$uri_nice = strtolower($uri[3]);
		
		//if not 3rd param - use default group
		if ( empty($uri_nice) ) {
			
			$this->groupId	= $cfg['base']['DefaultUserGroupID'];
			
		} // if ( empty($uri_nice) )
		else {
			
			//get user group with nice url
			$groups_list = getUsersGroupsWithNiceURL();
			
			//check is group exists
			if ( !empty($groups_list[$uri_nice]) ) {
				
				$this->groupId	= $groups_list[$uri_nice]['id'];
				
			}// if ( !empty($groups_list[$uri_nice]) )
			else {
				
				throw( new Exception( '<div class="status Error">Wrong user group URI provided.</div>' ) );
				
			}
			
		}
		
		// Loading system models
		mwLoad('system')->model('users');
		mwLoad('system')->model('forms');

		// Loading user
		$this->user		= User::get(); // get user
		$this->userId		= $this->user->ID;

		// Loading user group (HOF related)
		$this->group		= mwUsersGroup::create()->loadByID($this->groupId);

		// Loading and initiating group form
		$this->form		= new mwForm();
		$this->form->Inputs	= $this->user;
		$this->form->loadByID($this->group->FormID)->init(false, false);

		// Finally initiating user with data
		if ( $initUser and $this->user->Data )
			$this->user->fromArray($this->user->Data);

	}//func	initModels

	//displays form for new to register or returned users to edit account
	function index (){
		
		// Initiating common models, with user init
		$this->_initModels(true);
		
		// Returning form as HTML for futher processing in mod
		// Using ob cache to make sure echo is captured too
		$this->_renderForm();
		
	} //FUNC index
	
	/*
	//alias for index
	function type() {
		
		$this->index();
		
	} //FUNC type
	*/
	
	//save users
	function save(){

		// Initiating common models, with user init
		$this->_initModels(true);
		
		$cfg = getListingsCFG();
		
		$isNew = $this->user->ID == 0;
		
	// ---- Custom processing ----
		
		// Custom processing goes before common validations, cuz processing can create/remove additional values.
		// ToDo: Implement Forms custom inputs processing and proper contact dataobject fields init

		$inputs = $this->form->InputsList;
		
		$msg	= array(); // Will accumilate exceptions here

		// Looping through each dataobject field and checking type
		/**/
		foreach ( $this->user->_fields as $name => $field ) {
			
			// If no saved input data - skipping
			if ( empty($field['input']) or empty($inputs[$field['input']['type']]) )
				continue;
			
			//Fix for checkboxes not present in $_POST on update
			if ( $field['input']['type'] == 'CheckBox' && !array_key_exists($name, $_POST) )
				$_POST[$name] = '0';
			
			// Just shortcut
			$input = $inputs[$field['input']['type']];
			
			// Checking if widget really need custom post processing
			if ( !$input->customPost ) continue;

			try {
				// Filling widget properties and processing
				$this->user->_fields[$name]['input']['results'] = $input->fromArray($field['input'])->post();

			} catch ( Exception $e ) {
				$msg[] = $e->getMessage();
 			} //TRY
			
		} //FOR each field
		/**/
		
		if ( $msg )
			throw( new Exception( '<div class="status Error">'.implode('<br />', $msg).'</div>' ) );


	// ---- Validation ----
		
		// Validating user info
		
		if ( $v = $this->user->validate($_POST) ) //validate post
			throw( new mwValidationEx('Wrong info provided.', $v, $this->htmlSn) );
		
	// ---- Payment ----

		// Payment is proceeded only for new users
		// Existing users already paid
		if ( $isNew ) {
			
			//if registration set as free - no need to pay
			if ( !$cfg['base']['FreeRegistration'] ) {
				
				// Loading gate and it's models
				// ToDo: read gateway settings from config
				mwLoad('services')->model();
				mwSVC::model('ecomm');
				$gate = mwService($this->gate);
	
				$gate->Form = $this->htmlSn;
				$gate->Email = $_POST['email'];
	
				// Temporary fixed amount
				// ToDo: read amount from config
				$_POST['amount'] = $this->amount;
				
				//transaction option
				$options = array(
					'currency' => $this->currency,
					'rr_use' => $this->rr_use,
					'rr_ptype' => $this->rr_ptype,
					'rr_pstep' => $this->rr_pstep,
					'rr_count' => $this->rr_count
				);
				
				// Creating transaction based on post
				//$tr = $gate->newTransaction()->fromPost($_POST);
				$tr = $gate->newTransaction( $options )->fromPost($_POST);
				
				// Processing payment directly, as this is dedicated client mod
				// No tricky iframe/returning gateways expexted.
				$gate->charge();
	
				$_POST['transaction'] = $tr->Order->REF;
				
				//set new user unactive by default
				//to let admin activate it in backend
				$this->user->reqActivation = true;
			
			}
		} //IF new user

	// ---- DB Update ----

		// Saving user in DB
		$this->user->fromArray($_POST)->save();
		
		// Adding in group if new user
		if ( $isNew ) {
			
			$this->user->addGroups($this->groupId);

			// And loggin in.
			//$this->user->login($this->user->getLogin(), $this->user->Password, true);

			// Creating user record for HOF (hof_users)
			// Include transaction ID in hof user table
			// ToDo: actually create it :)


			//send an email about registration
			//toDo: add email sending
			
	// ---- Redirect ----

			$_SESSION['ls_new_user'] = true;

			// Redirecting new user to video player
			//$this->addAjax('redirect', '/ss/player');
			
			$l = new mwListing;
			//__($user);
			//$file = compilePath(SITE_TEMPLATES_PATH, $this->SectionName . '/emails', 'approved.php');
			//$html = loadView($file, $user);
			$html = 'Congratulations! You have a new signup. Login to view details.<br />
			<a href="https://' . $_SERVER['HTTP_HOST'] . '/site/listings/members">Listings Members</a>';
			
			$l->email($cfg['email']['signup']['sendTo'], $cfg['email']['signup']['from'], $html, $cfg['email']['signup']['subject']);
			//$l->email('info@moradmedia.com', '"Elite YYC" <noreply@eliteyyc.morwebcms.com>', $html, 'Eliteyyc.ca - New Signup');
			//$l->email('spatel11@gmail.com', '"Elite YYC" <noreply@eliteyyc.morwebcms.com>', $html, 'Eliteyyc.ca - New Signup');
			//$l->email('jw@topcalgaryrealestate.com', '"Elite YYC" <noreply@eliteyyc.morwebcms.com>', $html, 'Eliteyyc.ca - New Signup');
			

		} //IF new user
		else {
			
			// Redirecting existing user to profile page after update account info
			//$this->addAjax('redirect', '/' . $this->SectionName . '/profile'); 
			
		}
		
		
	} //FUNC save

	//render form with all needed elements like js, wraps, etc..
	function _renderForm(){
		
		$cfg = getListingsCFG();
		
		mwLoad('system')
			->resource('mw.system', 'mw.forms')
			->java('block.auth.js')
		; //mwLoad
		
		if ( $this->wgt->LiveEd or !empty($_SESSION['ls_new_user']) ) {
			
			?><div class="lsCaption"><mwWidget rel="lsContent_welcome" info="Listings Welcome Message" widget="Content" >Listings Welcome Message</mwWidget></div><?php
			
			//reset new user
			$_SESSION['ls_new_user'] = false;
			
			//if no liveEd - show welcome message only
			if ( !$this->wgt->LiveEd ) return;
			
		} //IF returning user
		
		// Adding editable content on top of mod contents.
		// For account mod page, it's sensitive to auth
		if ( $this->wgt->LiveEd or !$this->userId ) {
			?><div class="lsCaption"><mwWidget rel="lsContent_register" info="Listings Registration Caption" widget="Content" >Listings Registration Caption</mwWidget></mwWidget></div><?php
		} //IF new user

		if ( $this->wgt->LiveEd or $this->userId ) {
			?><div class="lsCaption"><mwWidget rel="lsContent_account" info="Listings Account Caption" widget="Content" >Listings Account Caption</mwWidget></div><?php
		} //IF returning user
		
		//make array of file inputs to render images later
		$thumbs_list = array();
		//check is user authorused - dont need to render images for new user
		if ( $this->user->Data ) {
			
			//__($this->user->_fields);
			//loop user fields
			foreach($this->user->_fields as $k => $v) {
				
				//check is field has input - no input means its a base field (cant be file input)
				if ( empty( $v['input'] ) ) continue;
				
				//check is input type=file
				if ( $v['input']['type'] != 'File' ) continue;
				
				//check is input has some value
				if ( !empty($this->user->$k) )
					$thumbs_list[$k] = $this->user->$k;
				
			}
		}
		
	?>
		<form id="<?=$this->htmlSn?>" method="post" action="/ajax/listings/account/save" onsubmit="return mwAuth.submitForm(this, 1)">
			<input type="hidden" name="id" value="<?=$this->userId?>" />
			<input type="hidden" name="xtoken" value="<?=getToken()?>" />
			
			<!--generate hidden inputs for all file inputs to store old value-->
			<?foreach($thumbs_list as $k => $v):?>
			<!--input type="hidden" name="<?=$k?>" value="<?=$v?>" /-->
			<?endforeach;?>
			
			<div class="mwFormStatus"></div>
			
			<?= ( $this->userId || $cfg['base']['FreeRegistration'] ) ? $this->_renderForm_edit() : $this->_renderForm_new()?>
			
			<div class="mwFormLoader" style="display:none"></div>

		</form>

		<script type="text/javascript">

			jQuery( function () {

			// ---- Inputs ----

				// Removing technical inputs
				jQuery('[name="transaction"]').closest('tbody').remove();
				jQuery('[name="status"]').closest('tbody').remove();

			// ---- Submit ----

				// Making sure submit button in the end of form

				// Looking for button wrapper
				var $bWrap = jQuery('.mwFormSubmit').closest('tbody');

				// Creating table which will serve as button wrapper
				// And inserting it right before loader
				jQuery('<table class="Dialog Tall"></table>')
					.append($bWrap)
					.insertBefore('#<?=$this->htmlSn?> .mwFormLoader');

			// ---- Init ----

				setTimeout( function () {
					styleDialog('#<?=$this->htmlSn?> .Dialog');
				}, 1 );
				
			// ---- Thumbs ----
				
				//add delay to let styleDialog do job
				setTimeout(function(){
					
					//store thumbs data to JS
					var thumb_list = <?=json_encode($thumbs_list)?>
					//check is thumbs list has something to render
						
					//___(thumb_list);
					//loop thumbs list
					for(var i in thumb_list) {
						
						jQuery('.name-' + i).parent().append('<div class="account-thumb"><img src="/files/galleries/' + thumb_list[i] + '" /></' + 'div>');
						
					}
					
				}, 1)
				
				
			}); // jQuery.onLoad

		</script>
	<?php

	} //FUNC _renderForm

	//renders registration form
	function _renderForm_new(){
		
		mwLoad('services')->model();
		mwSVC::model('ecomm');
		
		// Preparing form html
		$html = $this->form->HTML();
		
		// Loading gateway billing form
		$tpl = compilePath(SITE_TEMPLATES_PATH, 'payments/gates', $this->gate.'.php');
		if ( is_file($tpl) )
			$html .= loadView($tpl);
		else
			throw( new Exception('Incomplete gateway setup: template') );
		
		return $html;
		
	} //FUNC _renderForm_new
	
	//renders account form
	function _renderForm_edit(){
		
		return $this->form->setup()->HTML();
		
	} //FUNC _renderForm_edit
	
}//class mwRegMgrPayment
?>