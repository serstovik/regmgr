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

	public	$mode		= '';		// Mode used for integration
	public	$cart		= [];		// Current cart

	function editor_products () {

	// ---- Cart ----
		
		// Loading cart
		$this->loadCart();
		
		// Nothing to do if no cart loaded or no tems in cart
		if ( !$this->cart or !$this->cart->items )
			return;
		
		// Using first item in cart to determine current group
		$groupId	= reset($this->cart->items)['parent_id'];

		// Preparing set of selected items for items editor
		$selected	= [];
		foreach ( $this->cart->items as $id => $row )
			$selected[ $row['sn'] ] = true;		
		
	// ---- HTML ----

	?>
		<div id="eShop-modifyButton" class="mwDialog">
			<div class="cell-100"><input type="button" value="Edit" class="full" onclick="editProducts()" /></div>
		</div>

		<div id="eShop-applyButton" class="mwDialog" style="display: none;">
			<div class="cell-70"><input type="button" value="Apply Changes" class="Hi hi full" /></div>
			<div class="cell-30"><input type="button" value="Cancel" class="full" /></div>
		</div>
		
		<div class="eShop-productsList <?=mw3()? 'mw3' : 'mw2'?>">
		<?php	foreach ( $this->cart->items as $id => $row ) { ?>
		
				<?=$this->renderItem($row)?>
			
		<?php	} //FOR each cart item ?>
		</div>
		
		<script type="text/javascript">

			function editProducts () {

				// Enabling multiselection
				itemsEd.multiSelect	= true;
				itemsEd.selected	= <?=json_encode($selected)?>;

				// Hiding add/delete buttons to do not confuse users
				// Can simply hide it, as there is no concurrent callers
				// Additionally hiding divider above it
				var $tools		= itemsEd.Body.find('#itemsEd_itemTools'); 
				$tools.prev('.winHDivider ').remove();
				$tools.remove();

				// Setting up custom hint
				itemsEd.Editor.parent().attr('data-hint', 'Click '+itemsEd.ItemKinds+' to modify selection.');

				itemsEd
					.unbind('onSave')
					.onSave( function ($data) {
						
						// Converting selection to id's list
						// Forming new cart
						// ToDo: add checks and validations
						var $cart	= {};
						for ( var $rel in itemsEd.selected ) {
							
							var $item	= mwData.items[$rel];
							var $id		= $item.id;
							
							// Preparing item row
							// ToDo: implement common model for cart item definitions
							var $iRow	= {};
							var $fields	= ['id', 'sn', 'parent_id', 'title', 'preview', 'price'];
							for ( var $i in $fields ) {
								
								var $f		= $fields[$i];
								$iRow[$f]	= $item[$f];
								
							} //FOR each field
							
							// Forming cart row
							// ToDo: add support for quantities and attributes
							$cart[$id]	= {
								id		: $id,
								quantity	: 1,		// Currently supporting only 1 product as default
								attributes	: false,
								item		: $iRow,
							}; //$cart
							
						} //FOR each selected item
						
						// Storing updated cart data
						var $jCart	= JSON.stringify($cart); 
						var $input	= applicationEd.Form.find('#eShop-cart');
						
						$input.val($jCart);
						
						// Reloading dialog, to allow all editors and form to update.
						// This will also store updated cart
						applicationEd.apply();
						
						
					}) //FUNC onSave
					.dialog(<?=$groupId?>); 
				
				// Realigning window to apply changes
			//	itemsEd.Window.align();

				
				return false;

			} //FUNC editProducts
		
		</script>
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
				</table>
			</div>		
		</div>
		<hr />
	<?php		
	} //FUNC renderItem

	function save ($data) {
		
	//	__($data, $_POST);
		
		return $data;
	} //FUNC save

	function loadCart () {
	
		// Nothing to do if no cart stored
		if ( empty($this->data['cart']) )
			return;

		// Getting cart data
		$cData		= json_decode($this->data['cart'], true);

		// Loading cart model and creating object
		mwLoad('cart')->model();
		$this->cart	= (new mwCart())->setItems( $cData );
		
		return $this;
		
	} //FUNC loadCart

	function initResources () {

		// Loading css for list
		$this->load->css('regmgr_eshop.css');
		
		// Loading items editor
		// It requires mode to be present in get
		$_GET['mode'] = $this->mode;
		$ed = mwLoad('eshop')->Editor('itemsEd');		
		
	//	__($ed);
		
		// Preloading groups data, using models from editor
		$groups	= $ed->gModel->getList();
		$this->load->addData('groups', $groups);
		
	} //FUNC initResources

} //CLASS mwRMEditorEx_eShop