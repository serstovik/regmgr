<?php
//todo: link  by id
//todo: save as separate strings w\o serialization
//todo: add file name
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

		$this->load->model('rmAttach');

		$Attacher	= new rmAttach();
		$files 		= $Attacher->getFiles($this->application->sn);
		if (!empty($files['files']))
			$files	= unserialize($files['files']);

		$cnt	= 1;
		$htmlFiles	= '<ul>';
		foreach	($files as $name => $file){
			$htmlFiles	.= '<li><a href="/files/regmgr/'.$file.'" target="_blank">'.ucfirst($name).'</a></li>';
			$cnt++;
		} //FOREACH

		$htmlFiles	.= '</ul>';
	?>
		<dl class="mwDialog">

			<dt>User Attachments</dt>
			<dd><?=$htmlFiles;?></dd>
		</dl>
	<?php

	} //FUNC editor_attach

} //CLASS mwRMEditorEx_attach