<?php
/** //** ----= CLASS mwRMApplicationEx_eShop	=----------------------------------------------------------------------\**//** \
*
* 	eShop integration widget.
*
* 	@package	morweb
* 	@subpackage	regmgr
* 	@category	widget
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwRMApplicationEx_eShop extends mwRMApplicationEx {

	public	$extName	= 'eShop';				// Extension name. If omited - widget name will be used.

	public	$esLoad		= false;				// eShop loader

	public	$gModel		= false;				// |- Group and item models.
	public	$iModel		= false;				// |  
	public	$cart		= false;				// If cart is used - it will be loaded in here.

	function __init () {

		// Loading eshop loader
		$this->esLoad	= mwLoad('eshop')->model('loader', 'esLoad');		

	} //CONSTRUCTOR

	function render ($node) {
		
		// Loading cart model
		// Designed to use with cart only
		// ToDo: add support for non-cart eShop modes
		mwLoad('cart')->model();
		
		$ext	= $this->data;
		
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

	// ---- Data ----

		// Nothing to do if no items in cart
		if ( empty($this->cart->items) )
			return;

		// Grabbing cart data, and adding some items data
		// Storing selected items data with form for fast display on backend
		$data = $this->cart->data;

		foreach ( $data as $id => &$row ) { 
			
			$item	= $this->cart->items[$row['id']];
			$tmp	= [];
			
			// Just copying selected fields
			foreach ( ['id', 'sn', 'parent_id', 'title', 'preview', 'price'] as $f ) {

				$tmp[$f] = $item[$f];
				
			} //FOR each field
			
			$row['item'] = $tmp;
			
		} //FOR each item in cart 

		// Encoding cart data into input
	?>
		<textarea name="cart" style="display: none;"><?=json_encode($data)?></textarea>
	<?php
	} //FUNC render

} //CLASS mwRMApplicationEx_eShop