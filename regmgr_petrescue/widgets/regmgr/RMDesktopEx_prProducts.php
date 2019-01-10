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
		if ( empty($row['extensions']['eShop']['items']) )
			return;

		$items = $row['extensions']['eShop']['items'];

		// Loading css for list
		$this->load->css('regmgr_petrescue.css');
			
		// For each item outputting separate <P>
		foreach ( $items as $i => $iRow ) {
	?>
			<p class="prProduct n<?=$i?>"><?=$iRow['item_title']?></p>		
	<?php	
		} //FOR each item

	} //FUNC column_products
	
} //CLASS mwRMDesktopEx_prProducts