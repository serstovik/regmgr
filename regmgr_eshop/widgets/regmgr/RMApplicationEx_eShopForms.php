<?php
/**//** ----= CLASS mwRMApplicationEx_eShopForms	=--------------------------------------------------------------\**//** \
 *
 *
 *
 *\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc.	=------/** //**/
class mwRMApplicationEx_eShopForms extends mwRMApplicationEx {

	public	$esLoad		= false;				// eShop loader

	public	$gModel		= false;				// |- Group and item models.
	public	$iModel		= false;				// |  
	public	$cart		= false;				// If cart is used - it will be loaded in here.

	function __init () {

		// Loading eshop loader
		$this->esLoad	= mwLoad('eShop')->model('loader', 'esLoad');		

	} //CONSTRUCTOR

	function render ($node) {
		
		// Loading cart model
		// Designed to use with cart only
		// ToDo: add support for non-cart eShop modes
		mwLoad('cart')->model();
		
		$ext	= @$this->application->extensions[$this->WidgetName];
		if ( !$ext )
			$ext = [];
		
		// Using new cart for new application, or initiating cart from application
		if ( empty($this->application->id) ) {

			$this->cart	= mwCart()->init();

		} //IF new application
		else {

			// Nothing to do if no cart stored
			if ( empty($ext['cart']) )
				return;

			$cData		= $ext['cart'];

			$cData		= json_decode($cData, true);
			$this->cart	= (new mwCart())->setItems( $cData );

		} //IF existing application

	// ---- Group/Item ----

		// Using first cart item for group definition
		// Currently eShop handles only one group at time
		$item	= reset($this->cart->items); 		

		// Nothing to do if no items in cart
		if ( empty($item) )
			return;

		// Having item - can define mode and load models for item and group
		$mode	= $item['mode'];
		
		$this->gModel	= $this->esLoad->model('group', $mode);
		$this->iModel	= $this->esLoad->model('item', $mode);

		// Loading group
		$this->gModel->loadByID($item['parent_id']);

		$group	= $this->gModel->asArray();
		
	// ---- Templates ----

		// Loading submit template and providing it with required data
		$tpl = mwLoad('eShop')->model('tplSubmit', true);
		
		// Setting up tpl
		$tpl
			->mode($mode)
			->groupId($group['id'])
			->listId($group['list'])
			->cart($this->cart)
			->inputName('items')
		; //$tpl
		
		// Loading associated form
		$tpl->loadForm();
		
		// Rendering form for each item in cart
		$html		= '';
		
		// Using iterator for subcontacts
		$i = 1;
		foreach ( $this->cart->items as $item ) { 
			
			for ( $j = 1; $j <= $item['quantity']; $j++, $i++ ) { 
				
				// Using default form, no subcontact prefill
				$html .= $tpl->getContactForm('', @$ext['items'][$i], $i, ['item' => $item, 'group' => $group]);
			
			} //FOR each single item 
		} //FOR each item in cart  

		// Done, outputting collected html
		echo($html);

		// Encoding cart data into input
	?>
		<textarea name="cart" style="display: none;"><?=json_encode($this->cart->data)?></textarea>
	<?php
	} //FUNC render

} //CLASS mwRMApplicationEx_eShopForms