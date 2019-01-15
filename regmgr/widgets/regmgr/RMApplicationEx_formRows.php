<?php
/** //** ----= CLASS mwRMApplicationEx_formRows	=----------------------------------------------------------------------\**//** \
*
* 	Form rows widget.
*
* 	@package	morweb
* 	@subpackage	regmgr
* 	@category	widget
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class mwRMApplicationEx_formRows extends mwRMApplicationEx {

	function __init () {

	} //CONSTRUCTOR

	function render ($node) {
		
		// Using unique SN for JS init
		$sn	= newSN('R');
		
		// Using dedicated model to parse rows
		$tpl = $this->load->model('tplRMFormRows', true);
		
		// Setting up and executing
		$tpl
			->load( $this->load )			// Providing loader 
			->html( $node->html() )			// Setting up html
			->backend( $this->backend )		// Adding backend flag
			->data( $this->data )			// Supplying with data
			->main()
		; //$tpl
		
		// Getting parsed html, wrapping and outputting
		$html = $tpl->html();
	?>
		<div id="<?=$sn?>" class="rmFormRows">
			<?=$html?>
		</div>
		
	<?php		
	/*/
		$this->load->js('rmFormRows.js');
	/*/
		// Using hardload to trick AJAX js loading
		// Have to do this way until better resourcer is implemented
	?>	
		<script type="text/javascript" data-extension="<?=$this->WidgetName?>" src="/res/regmgr/js/rmFormRows.js"></script>	
		<script type="text/javascript" data-extension="<?=$this->WidgetName?>">

			jQuery( function () {
	
				if ( !window._RMFR )
					window._RMFR = rmFormRows( jQuery('#<?=$sn?>'), {});
				
			}); //jQuery.onLoad
		
		</script>
	<?php		
	/**/

	} //FUNC render

} //CLASS mwRMApplicationEx_formRows