<?php

/** //** ----= CLASS tplRMFormRows	=------------------------------------------------------------------------------\**//** \
*
* 	Form rows template parser.
*
* 	@package	morweb
* 	@subpackage	regmgr
* 	@category	model
*
\**//** ---------------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
class tplRMFormRows extends vTpl2 {

	public	$backend		= false;			// Backend editor rendering flag.
	public	$load			= false;			// Section loader.
	
	public	$name			= 'rows';			// Data name to which group inputs on row.
	public	$data			= [];				// Rows data to prefill with.
	
	public	$mask			= '__k__';			// Inputs key mask.

	/** //** ----= main	=--------------------------------------------------------------------------------------\**//** \
	*
	* 	Main parser logic. Used as entry point for template parsing.
	*
	*	@return	SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function main ($vars = []) {

	// ---- Main tags ----
	
		// Splitting big parsers to dedicated parse/render methods for code readability
		$this->parseRows($this);

	// ---- Post Render ----

		// Parsing general actions
		$this->render()->actions();

		return $this;

	} //FUNC main	

/* ==== Tabs ================================================================================================================ */

	/** //** ----= parseRows	=------------------------------------------------------------------------------\**//** \
	*
	* 	Rows parser.
	* 
	* 	@see vTplNode parser for params.
	*
	*	@return	SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function parseRows ($node) {

		$node->parse()->section('formRow', function ($node) {

		// ---- Data ----

			// Clearing trash if happen
			unset($this->data[$this->mask]);
			
			// If no data - need to have 1 empty row
			// By default using 1 as starting key, but don't really care about keys below
			if ( !$this->data or !is_array($this->data) )
				$this->data	= [1 => []];

		// ---- Rows ----

			// Rendering each row and collecting html
			$html		= '';

			foreach ( $this->data as $key => $row ) {

				// Using node clone to generate each row
				$rNode		= $node->_clone();
				$html		.= $this->_ob_renderRow($rNode, $key, $row);

			} //FOR each data row

		// ---- Source ----

			// Finally rendering source
			// Using source node, as don't need it anymore
			$source		= $this->_ob_renderRow($node, true);

		// ---- HTML ----

			// Wrapping and gluing all together
			$source		= '<div class="rmFormRows-source" style="display:none;">'.$source.'</div>';
			$html		= '<div class="rmFormRows-container">'.$html.'</div>';

			// Done
			return $source.$html;

		}); //FUNC parse formRow
		
		return $this;
		
	} //FUNC parseRows

	/** //** ----= renderRow	=------------------------------------------------------------------------------\**//** \
	*
	* 	Renders single row.
	* 
	* 	@see vTplNode parser for params.
	*
	*	@return	SELF
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function renderRow ($node, $key, $row = []) {

		// Checking if source row given
		$isSource	= $key === true;

		// ToDo: prefill form inputs from data

		// Preparsing inputs to have correct array format
		// Have to do it for each individual row, for correct prefill support
		$node->parse()->inputs( function ($node) {
			
			return $node->render()->prefixInput($this->name.'[{k}]', true);
			
		}); //FUNC render.inputs

		// Parsing input #
		// Using mask for source row, it will be used by JS to generate new rows
		$node->parse()->vars(['k' => $isSource ? $this->mask : $key]);

		// Getting html and outputting
		$html = $node->html();
		
	?>
		<div class="rmFormRows-row"><?=$html?></div>
	<?php

	} //FUNC renderRow

/* ==== Actions ============================================================================================================= */

	/** //** ----= addRow, removeRow	=----------------------------------------------------------------------\**//** \
	*
	*	Basic action. Generates buttons rows adding and removal.
	*
	\**//** -------------------------------------------------------------------= by Mr.V!T @ Morad Media Inc. =----/** //**/
	function renderAction_main ($node) {

		// Adding prefix class to button
		// Which will be used by JS to initialize
		return $node->render()->basicAction([
			'prefix'	=> 'rmFormRows-action',
		]); //basicAction

	} //FUNC renderAction_switchTab

	function renderAction_addRow ($node) {

		return $this->renderAction_main($node);

	} //FUNC renderAction_nextTab

	function renderAction_removeRow ($node) {

		return $this->renderAction_main($node);

	} //FUNC renderAction_prevTab

/* ==== Helpers ============================================================================================================= */

} //CLASS tplRMFormRows
?>