<?php
/** //** ----= CLASS mwRMEditorEx_attach	=----------------------------------------------------------------------\**//** \
 *
 * 	@package        Morweb
 *	@subpackage	regMgr
 *	@category	widget extension
 *
 * \**//** ---------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
 class mwRMEditorEx_attach extends mwRMEditorEx
{

	public	$tabs	= [
		'attach'	=> 'Attachments'
	]; //$tabs

	function editor_attach () {

	?>
		<dl class="mwDialog">

			<dt>User Attachments</dt>
			<dd>User Attachment example...</dd>
		</dl>
	<?php

	} //FUNC editor_attach

} //CLASS mwRMEditorEx_attach