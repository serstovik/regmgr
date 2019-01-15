<?php
/** //** ----= CLASS mwRMEditorEx_eShop	=------------------------------------------------------------------------------\**//** \
*
* 	eShop products editor for application editor.
*
* 	@package	morweb
* 	@subpackage	regmgr
* 	@category	widget
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwRMEditorEx_eShop extends mwRMEditorEx {

	function editor_products () {

	// ---- Cart ----
		
		// Nothing to do if no cart stored
		if ( empty($this->data['cart']) )
			return;

		// Getting cart data
		$cData	= json_decode($this->data['cart'], true);

		// Loading cart model and creating object
		mwLoad('cart')->model();
		$cart	= (new mwCart())->setItems( $cData );

	// ---- HTML ----

		// Loading css for list
	//	$this->load->css('regmgr_eshop.css');
	?>
		<link type="text/css" href="/res/regmgr_eshop/css/regmgr_eshop.css" rel="stylesheet" />
		
		<div id="eShop-modifyButton" class="mwDialog">
			<div class="cell-100"><input type="button" value="Modify Products" class="full" /></div>
		</div>

		<div id="eShop-applyButton" class="mwDialog" style="display: none;">
			<div class="cell-70"><input type="button" value="Apply Changes" class="Hi hi full" /></div>
			<div class="cell-30"><input type="button" value="Cancel" class="full" /></div>
		</div>
		
		<div class="eShop-productsList <?=mw3()? 'mw3' : 'mw2'?>">
		<?php	foreach ( $cart->items as $id => $row ) { ?>
		
				<?=$this->renderItem($row)?>
			
		<?php	} //FOR each cart item ?>
		</div>
	<?php
	} //FUNC editor_products

	function renderItem ($item) {
		
		$html = $this->_ob_itemTemplate();
		
		return arrayToTemplate($item, $html, true);
		
	} //FUNC renderItem

	function itemTemplate () {
	?>	
		<div class="eShop-item">
			<div class="preview"><img src="/getimage/eShop/{preview}?r=0x100&c=100x100" /></div>
			<div class="details">
				<table>
					<tr>
						<th>Title:</th>
						<td>{title}</td>
					</tr>
					<tr>
						<th>Price:</th>
						<td>{price}</td>
					</tr>
				</table>
			</div>		
		</div>
		<hr />
	<?php		
	} //FUNC renderItem

} //CLASS mwRMEditorEx_eShop