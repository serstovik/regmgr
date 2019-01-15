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

		// Getting name
		if ( $node->attr['name'] and isAlphaDash($node->attr['name']) )
			$name	= $node->attr['name'];
		else
			$name	= 'rows';
		
		// And rows data
		$data	= empty($this->data[$name]) ? [] : $this->data[$name]; 
		
		// Using dedicated model to parse rows
		$tpl = $this->load->model('tplRMFormRows', true);
		
		// Setting up and executing
		$tpl
			->load( $this->load )			// Providing loader 
			->html( $node->html() )			// Setting up html
			->backend( $this->backend )		// Adding backend flag
			->name( $name )				// Supplying name
			->data( $data )			// and data
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
	
				if ( !window._<?=$sn?> )
					window._<?=$sn?> = rmFormRows( jQuery('#<?=$sn?>'), {});
				
			}); //jQuery.onLoad
		
		</script>
	<?php		
	/**/

	} //FUNC render

} //CLASS mwRMApplicationEx_formRows