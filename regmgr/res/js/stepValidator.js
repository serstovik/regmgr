/** //** ----= CLASS rmStepValidator	=------------------------------------------------------------------------------\**//** \
*
* 	Step Validator.
*
* 	@package	MorwebCMS
* 	@subpackage	regmgr
* 	@category	model
*
\**//** ------------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
var rmStepValidator		= function ($el) {
	return vEventObject (['onError'], {
		
		tabsWrapId		: 'mainFormTabs',		
		reqClass		: 'required',
		formElements		: ['input', 'textarea', 'select'],
		
		errorMsg		: 'All marked fields are required',
		
		dom			: {
			//wrapper html element			
			stepWrap		: '',
			notValidTagNames	: [],
			notValidTitles		: [],
			
		},
		
		css			: {
			errorBorder		: '1px solid red',	
		},
		
		init			: function () {
			
			var $this		= this;
			$this.dom.stepWrap	= $el;
			
			//getting all element to validate
			for(var i = 0; i <= $this.formElements.length; i++){
				//if true then we check another 
				$this.validateElements($this.formElements[i]);
			}//for

			//if we have non-valid elements return false with error msg
			if ($this.dom.notValidTagNames.length > 0){
				//add errorBorders
				$this.addErrorBorder();
				//show error Message
				$this.showErrorMessage();
				return false;
			}
			
			//if all is fine return true
			return true;					
					
		}, //init()
		
		validateElements	: function ($tag) {
			
			var $this	= this;
			jQuery($this.dom.stepWrap).find($tag).each(function () {

				//check if element has this attr
				var attr = jQuery(this).attr('required');
				if (typeof attr !== typeof undefined && attr !== false) {
  					//checking if value is empty and is so adding not-valid elemnt to array
					var elValue	= jQuery(this).val();
					if(elValue == undefined || elValue == '' ){
						$this.dom.notValidTagNames.push(jQuery(this).attr('name'));
						$this.dom.notValidTitles.push(jQuery(this).attr('title'));
					}//if
				}//if
				
			});//each
			
			return true;	
			
		}, //validateElements
		
		addErrorBorder		: function () {
			var $this	= this;
			console.log(11);
			for(var i = 0; i < $this.dom.notValidTagNames.length; i ++){
				var elName = $this.dom.notValidTagNames[i];
				jQuery("input[name='"+elName+"']").parent().css($this.css.errorBorder);
			}	
		},//addErrorBorder
		
		showErrorMessage	: function () {
			
			var $this	= this;
			var msg		= '<p style="color: red">'+$this.errorMsg+'<br>';
			for(var i = 0; i < $this.dom.notValidTitles.length; i ++){
				msg += ' -' + $this.dom.notValidTitles[i] + '<br>';
			}//for
			msg += '</p>';
			
			//show error message
			jQuery('#'+$this.tabsWrapId).before(msg);
			
		}//showErrorMessage
		
	}.init()); //vEventObject
	
}//rmStepValidator

//example how to use at the html page
//rmStepValidator($jqueryEl);