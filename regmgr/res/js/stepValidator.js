/** //** ----= CLASS rmStepValidator	=------------------------------------------------------------------------------\**//** \
*
* 	Step Validator.
*
* 	@package	MorwebCMS
* 	@subpackage	regmgr
* 	@category	model
*
\**//** ------------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
var rmStepValidator		= function ($el, $options) {
		
	return vEventObject ([], {
		
		tabsWrapId		: 'mainFormTabs',		
		reqClass		: 'required',
		formElements		: ['input', 'textarea', 'select'],
		
		//error messages
		// todo: add option to enebale it on Init, right now it's disable
		addErrorMsg		: false,			// disabled by default, if true then input titles will show	
		errorMsg		: 'All marked fields are required',
		notValidPrevStepMsg	: 'You should fill our all previous steps before move here',
		
		validateStatus		: null,
		rmSteps			: [],
		validatedStepClass	: 'rm-step-valid',
		
		dom			: {
			
			//wrapper html element			
			stepWrap		: '',
			rmStepClass		: 'rmTabs-content',		//css class should be added to each section
			notValidTagNames	: [],
			notValidTitles		: [],
			
			errorMsgWrap		: '.rmForm-status'
			
		},
		
		css			: {
			errorBorder		: '1px solid red !important'	
		},
		
		init			: function () {
			
			var $this		= this;
			
			$this.dom.stepWrap	= $el;
			
			$this.isPrevStepsValid();
			
			//getting all element to validate
			for(var i = 0; i < $this.formElements.length; i++){
				//if true then we check another 
				$this.validateElements($this.formElements[i]);
			}//for
			
			//if we have non-valid elements return false with error msg
			if ($this.dom.notValidTagNames.length > 0){
				
				//add errorBorders
				$this.addErrorBorder();
				/*
				//show error Message
				if ($this.addErrorMsg)
					$this.showErrorMessage();
				*/
				$this.validateStatus	= false;
				//add not valid status
				$this.updateStepValidationStatus($this.dom.stepWrap, false);
			} 
			else {
				
				$this.validateStatus	= true;
				//add validate status
				$this.updateStepValidationStatus($this.dom.stepWrap, true);
			}//if
			
			
			return $this.validateStatus;
					
		}, //init()
		
		validateElements	: function ($tag) {

			var $this	= this;
			
			var tabName	= jQuery($this.dom.stepWrap).attr('name');
			
			jQuery($this.dom.stepWrap).find($tag).each(function () {
				// checking if element is vidible
				if (jQuery(this).is(':visible')) {
					
					//check if element has this attr
					var attr 	= jQuery(this).attr('required');
					var elType	= jQuery(this).attr('type');
					var elName	= jQuery(this).attr('name');
					
					if (typeof attr !== typeof undefined && attr !== false) {
						//checking if value is empty and is so adding not-valid element to array
						var elValue = jQuery(this).val();

						if (elValue == undefined || elValue == '' || elValue == 'Please Select') {
							$this.dom.notValidTagNames.push(jQuery(this).attr('name'));
							$this.dom.notValidTitles.push(jQuery(this).attr('title'));
						} else {
							if ($tag == 'select'){
								jQuery(this).parent('div.mwInput').css('border-color', '#ccc');
							}
							else if(elType == 'file'){
								jQuery('span.validate_'+elName).css('color', 'black');
							}
							else{
								jQuery(this).parent('div.mwInput').removeClass('error Error');
							}//if 
						}
						
						if (elType == 'radio' || elType == 'checkbox'){
							//we need to check it just once not for each element
							var elName	= jQuery(this).attr('name');
							var isChecked	= jQuery('input[name='+elName+']').is(':checked');

							//if not checked then add them to the notValidList
							if(!isChecked){
								$this.dom.notValidTagNames.push(jQuery(this).attr('name'));
								$this.dom.notValidTitles.push(jQuery(this).attr('title'));	
							}//if isChecked
							else {
								jQuery(this).css('border', 'none');
								jQuery('span.validate_'+elName).css('color', 'black');
							}
						}//if radio
					}//if attr
				}// if isVisible
			});//each
			
			
		}, //validateElements
		
		addErrorBorder		: function () {
			
			var $this	= this;
			
			for(var i = 0; i < $this.dom.notValidTagNames.length; i ++){
				
				var elName = $this.dom.notValidTagNames[i];
				var curInputEl	= jQuery("input[name='"+elName+"']");
				var curSelectEl	= jQuery("select[name='"+elName+"']");
				
				if (curInputEl.size() > 0){
					//error classes dont work for input type=file and select boxes
					if (curInputEl.attr('type') == 'file'){
						jQuery("span.validate_"+elName).css("color", "red");
					}
					else if(curInputEl.attr('type') == 'radio' || curInputEl.attr('type') == 'checkbox') {
						curInputEl.closest('.mwInput').css('border', 'none');
						jQuery('span.validate_'+elName).css('color', 'red');
					}
					else {
						curInputEl.closest('.mwInput').addClass('error Error');
					}	
				
				} else if(curSelectEl.size() > 0) {
					curSelectEl.closest('.mwInput').css('border', '1px solid red');
					// jQuery("select[name='"+elName+"']").closest('.mwInput').addClass('error Error');
				}
				
			}//for	
			
		},//addErrorBorder
		
		showErrorMessage	: function () {
			
			var $this	= this;
			var msg		= $this.errorMsg+'<br>';
			for(var i = 0; i < $this.dom.notValidTitles.length; i ++){
				msg += ' -' + $this.dom.notValidTitles[i] + '<br>';
			}//for
			
			//show error message
			jQuery($this.dom.errorMsgWrap).html(msg);
			
		},//showErrorMessage
		
		showError		: function(msg) {
			
			var $this	= this;
			var errorMsg;
			
			if (msg != undefined && msg.length > 0){
				$this.errorMsg = msg;	
			}
			
			//show error message
			jQuery($this.dom.errorMsgWrap).html(msg);
			
		},
		
		clearErrors		: function () {
			
			var $this	= this;
			
			jQuery($this.dom.errorMsgWrap).html('');
			jQuery('.error').removeClass('error Error');
			
			$this.dom.notValidTagNames	= [];
			$this.dom.notValidTitles	= [];
			
		},
		
		isPrevStepsValid		: function () {

			var $this	= this;
			
			//get current name
			var cName;
			if ($options !== undefined && $options.name !== undefined){
				cName	= $options.name;				
			}
			else {
				cName	= $el.data('for');
			}
			
			//get tab order of current step from 0
			var stepOrder	= jQuery('div[data-for='+cName+']').index('.'+$this.dom.rmStepClass);
			//if we are on the first step then don't update
			if (stepOrder == 0)
				return true;
			
			//check if all prev steps are valid
			var arePreviousStepsValid	= false;
			for (var i = 0; i < stepOrder; i++){
				//by default status is false
				//check if prev steps have been validate
				if ( jQuery('.'+$this.dom.rmStepClass).eq(i).hasClass($this.validatedStepClass) ){
					arePreviousStepsValid	= true;
				}
				else {
					if( jQuery('.'+$this.dom.rmStepClass).eq(i).is(':visible') ){
						var isSelected;

						//checking if label is selected then return just true
						isSelected = jQuery('label.rmTabs-button[data-for='+cName+']').hasClass('selected');
						
						if (isSelected)
							return true;
						
						// todo: check if it's not a current step that just started
						$this.showError($this.notValidPrevStepMsg);
						arePreviousStepsValid = false;
						
					}//if isvisible only
					
				}//if

			}//for
			
			return arePreviousStepsValid;
			
		}, //isPrevStepsValid
		
		//toggle validation step class for current tab
		updateStepValidationStatus	: function($el, $isValid){
			
			var $this	= this;
			
			if ($isValid){
				$el.addClass($this.validatedStepClass);
			} else {
				$el.removeClass($this.validatedStepClass);
			}
			
		}//updateStepValidationStatus
		
	}); //vEventObject
		
}//rmStepValidator

// todo: user should't go throw the step for example from #1 directly to #3 w\o validate step #2
// todo: if step was validated mark it with some class, 
// todo: make order of tabs - need to know their numbers
// todo: onSubmit form check if all tabs have class that they were validated
