<?php
/** //** ----= CLASS mwRMDesktopEx_prProducts	=----------------------------------------------------------------------\**//** \
*
* 	Products column for Pet Rescue site.
*
* 	@package	morweb
* 	@subpackage	regmgr
* 	@category	widget
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwRMDesktopEx_prProducts extends mwRMDesktopEx {
	
	public function column_products ( $alias, $row, $cfg ) {

		// Nothing to do if no eShop/items recorded
		if ( empty($row['extensions']['eShop']['cart']) )
			return;

		// Decoding items data
		$cart	= json_decode($row['extensions']['eShop']['cart'], true);

		$items = $row['extensions']['eShop']['items'];

		// Loading css for list
		$this->load->css('regmgr_petrescue.css');
			
		// For each item outputting separate <P>
		// Using iterator for sequent classes
		$i = 0;
		foreach ( $cart as $id => $row ) {
			$i++;
	?>
			<p class="prProduct n<?=$i?>"><?=@$row['item']['title']?></p>		
	<?php	
		} //FOR each item

	} //FUNC column_products
	
} //CLASS mwRMDesktopEx_prProducts