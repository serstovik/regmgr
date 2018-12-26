<?php
/**//** ----= CLASS mwRMEditorEx_eShop	=------------------------------------------------------------------------------\**//** \
 *
 *
 *
 *\**//** ----------------------------------------------------------------= by SerStoVik @ Morad Media Inc.	=------/** //**/
class mwRMEditorEx_eShop extends mwRMEditorEx
{

	public	$tabs	= [
		'products'	=> 'Products',
		'forms'		=> 'Forms',
	]; //$tabs

	function editor_products () {

		echo('Selected products list will be here');

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