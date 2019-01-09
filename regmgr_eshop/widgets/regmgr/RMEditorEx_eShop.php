<?php
/**//** ----= CLASS mwRMEditorEx_eShop	=------------------------------------------------------------------------------\**//** \
 *
 *
 *
 *\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc.	=------/** //**/
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
	?>
		<table class="<?=mw3()? 'contactsIndex' : 'IndexTable'?>">
			<thead><tr>
				<td class="Title">Title</td>				
				<td class="Div"></td>
				<td class="Num">Price</td>				
			</tr></thead>
			
		<?php	foreach ( $cart->items as $id => $row ) { ?>	
				<tbody >
					<td class="Title"><?=$row['title']?></td>				
					<td class="Div"></td>
					<td class="Num"><?=$row['price']?></td>				
				</tbody>
		<?php	} //FOR each cart item ?>
		</table>
	<?php
	} //FUNC editor

	function editor_forms () {
	?>
		<dl class="mwDialog">

			<dd class="Group"><div>Sample Editor</div></dd>

			<dt>Sample Input</dt>
			<dd><input name="sample_input" type="text" /></dd>

			<dt>Sample Textarea</dt>
			<dd><textarea></textarea></dd>

			<dt>Sample Textarea</dt>
			<dd><textarea style="height: 200px;"></textarea></dd>
		</dl>
	<?php
	} //FUNC editor


} //CLASS mwRMEditorEx_eShop