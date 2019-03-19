<?php


class PageBlock_RegMgr extends mwPageBlock {

// ---- SETTINGS ----

	public	$Caption		= 'RegMgr';
	public	$Description		= 'Registration Manager';
	public	$IconClass		= 'Form';

	public	$gridHidden		= false; 			// Set TRUE to hide block from pallete grid.

// ---- FIELDS ----

// ---- Payment ----

	public	$service	= '';					// Payment service. "Native" Stripe Checkout by default.
	
	public	$sandbox	= 0;					// Sandbox mode
	public	$simRequest	= false;				// |- Simulation options
	public	$simResponse	= false;				// |  Service to use

	public	$currency	= 'USD';				// Defines transaction currency.

	public	$message	= '';					// After submit message
	public	$redirect	= '';					// After submit redirect url

// --- Privilegies ---

	function __construct () {

		parent::__construct();

		$this
			->setField('service')->DB(FALSE)

			->setField('sandbox')->DB(FALSE)->Validations('isInt')
			->setField('simRequest')->DB(FALSE)->Validations('isCheckbox')
			->setField('simResponse')->DB(FALSE)->Validations('isCheckbox')

			->setField('currency')->DB(FALSE)->Validations('isAlnum')

			->setField('message')->DB(FALSE)->Validations('')
			->setField('redirect')->DB(FALSE)->Validations('')

			->setField('Data')->SZ('service', 'sandbox', 'simRequest', 'simResponse','currency', 'message', 'redirect')
			
		; //OBJECT $this
		
	} //CONSTRUCTOR

	function render () {

		// Setting up uri for loader
		$this->load->uri = $this->Page->uri;

		// Using controllers for uri processing
		list( $obj, $method, $params ) = $this->load->controllerURI(CMS_FRONTEND, 2, false);

		// Initialising object
		$obj->page	= $this->Page;
		$obj->wgt	= $this;

		// Calling specified method.
		$html = call_user_func_array( [$obj, '_ob_'.$method], $params );

		// Parsing embedded contents
		$html = $this->parseCommon($html);
/*
		// Parsing custom vars in content
		// Using vTpl to support nested vars
		// Always calling, to make sure unknown/unused vars are cleared
		$tpl = new vTpl2($html);
		$tpl
			->varWrap('[]')
			->parse()
			->vars($obj->contentVars)
			// Can't clear vars, as it will remove some intended stuff
			// ToDo: implement contents targeted parsing
			//	->cleanVars()
		; //$tpl

		$html = $tpl->html();
*/
		return $html;

	}// FUNC render

	function editor() {
	?>
		<?=$this->edPayment()?>
		<?=$this->edMessage()?>
	<?php
	} //FUNC editor

	/** //** ----= edPayment	=------------------------------------------------------------------------------\**//** \
	*
	* 	Payment options.
	* 
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function edPayment () {
		
		$svc = mwSVC::getList('payments', false); 
	?>
		<dl class="mwDialog <?=mw3()? 'tall' : 'wide'?>">
		
			<dd class="formGroup"><div>Gateway</div></dd>
			
			<dt>Payment Service:</dt>
			<dd>
				<?=mwSVC::getSelector('payments');?>
			</dd>

			<dt>Sandbox:</dt>
			<dd>
				<input class="cell-even" type="radio" name="sandbox" value="0" cap="Live" hint="In live mode system will proceed real charges." />
				<input class="cell-even" type="radio" name="sandbox" value="1" cap="Sandbox" hint="In sandbox mode system will make test calls to server without real charges." onchange="mwFormToggle(this, '.sandboxOn', this.checked)" />
			</dd>

			<dt class="sandboxOn">Simulation:</dt>
			<dd class="sandboxOn">
				<input class="cell-even" type="checkbox" name="simrequest" cap="Simulate Request" hint="In this simulation mode system substitutes transaction data with valid testing values." />
				<input class="cell-even" type="checkbox" name="simresponse" cap="Simulate Response" hint="In this simulation mode server request will be sumulated instead of processing actual request." />
			</dd>

			<dd class="sandboxOn hint Hint"><span class="warning">Warning:</span> Sandbox mode enabled. <br /> Transactions will not process real funds. </dd>

			<dd class="formGroup"><div>Additional Payment Options</div></dd>

			<dt>Currency:</dt>
			<dd>
				<select name="currency">
					<option value="USD">USD</option>
					<option value="CAD">CAD</option>
				</select>
			</dd>
			
		</dl>
	<?php
	} //FUNC edPayment

	/** //** ----= edMessage	=------------------------------------------------------------------------------\**//** \
	*
	* 	After submit settings.
	* 
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function edMessage () {
	?>
		<dl class="mwDialog <?=mw3()? 'tall' : 'wide'?>">
		
			<dd class="formGroup"><div>After Submission</div></dd>
			
			<dt>Message:</dt>
			<dd><textarea name="message"></textarea></dd>
			
			<dt>Redirect:</dt>
			<dd><input type="text" name="redirect" /></dd>

		</dl>
	<?php 
	} //FUNC edMessage

}//CLASS mwPageBlock_ReqMgr