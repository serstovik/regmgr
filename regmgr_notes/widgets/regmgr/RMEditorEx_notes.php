<?php
/** //** ----= CLASS mwRMEditorEx_notes		=----------------------------------------------------------------------\**//** \
 *
 * 	@package        Morweb
 *	@subpackage	regMgr
 *	@category	widget extension
 *
 * \**//** ---------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
 class mwRMEditorEx_notes extends mwRMEditorEx
{

	public	$tabs	= [
		'notes'	=> 'Notes'
	]; //$tabs

	function editor_notes () {

	?>
		<dl class="mwDialog">

			<dt>Admin Notes</dt>
			<dd><textarea name="admin_notes" style="height: 200px;"></textarea></dd>
		</dl>
	<?php

	} //FUNC editor_notes

} //CLASS mwRMEditorEx_notes