/** //** ----= CLASS rmBalloonApp	=------------------------------------------------------------------------------\**//** \
 *
 * 	Base Balloon application script files.
 *
 * 	@package	MorwebCMS
 * 	@subpackage	regmgr
 * 	@category	js scripts
 *
 \**//** -----------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
 var rmBalloonApp	= function ($el) {
 	
	var $el = _jq($el);	
		
	return vEventObject([], {
		
		dom				: {},
		
		//aliases tabsName with related methods
		tabsForEvent			: {
			
			pilot_info			: { //tab2 (pilot experience)

				method				: 'toggleSection',	// calling method to toggle
				hideByLoad			: 'rm_hide_pilot_exp', 	//id class to hide by default
				toggleElements			: 'rm_hide_pilot_exp',	//html section id which we need to toggle
				toggleElName			: 'convicted',		//input[name] which manage toggle 
				
				//tricky actions
				hideOnYes			: true, 		//default action if Yes choosen
				hideOnNo			: false,		//default action if No choosen
				
				extraSections			: null
				
			}, //tab 2
			
			pilot_part			: { //tab 4 (pilot participation)
				
				method				: 'toggleSection',
				hideByLoad			: null,
				toggleElements			: 'ride_con_section', //id class that
				toggleElName			: 'ride_con',
				
				//tricky actions
				hideOnYes			: false,
				hideOnNo			: true,
				
				extraSections			: ['govt_rel', 'media_pilot', 'sponsor_pilot', 'albuq_aloft'],
				
				//extra toggle names for sub sections 
				govt_rel			: {
					
					method				: 'toggleSection',
					hideByLoad			: 'gov_rel_section',	//id
					toggleElements			: 'gov_rel_section', 	//id class that
					toggleElName			: 'govt_rel',
					
					//tricky actions
					hideOnYes			: false,
					hideOnNo			: true
					
				}, //govt_rel
				
				media_pilot			: {
					
					method				: 'toggleSection',
					hideByLoad			: 'gov_rel_section2',	//id
					toggleElements			: 'gov_rel_section2', 	//id class that
					toggleElName			: 'media_pilot',
					
					//tricky actions
					hideOnYes			: false,
					hideOnNo			: true
					
				}, //media_pilot
				
				sponsor_pilot			: {
					
					method				: 'toggleSection',
					hideByLoad			: 'sponsor_pilot_section',	//id
					toggleElements			: 'sponsor_pilot_section', 	//id class that
					toggleElName			: 'sponsor_pilot',
					
					//tricky actions
					hideOnYes			: false,
					hideOnNo			: true
					
				}, //media_pilot
				
				albuq_aloft			: {
					
					method				: 'toggleSection',
					hideByLoad			: 'aibf_section',	//id
					toggleElements			: 'aibf_section', 	//id class that
					toggleElName			: 'albuq_aloft',
					
					//tricky actions
					hideOnYes			: false,
					hideOnNo			: true
					
				} //albuq_aloft
				
			},// pilot_part
		
		}, //tabsForEvent
		
		init			: function () {

			var $this	= this;
			
			var tabName	= $el.data('for');

			//checking if we have it in our obj
			if ($this.tabsForEvent[tabName] !== undefined){
				//another check if it is a function and call it
				if (typeof $this.tabsForEvent[tabName] == "object"){
					
					$this[$this.tabsForEvent[tabName].method]($this.tabsForEvent[tabName]);

					if ($this.tabsForEvent[tabName].extraSections !== null){
						
						for (var k = 0; k < $this.tabsForEvent[tabName].extraSections.length; k++){
							
							//parent obj
							var pObj	= $this.tabsForEvent[tabName];
							//create internal Obj just to make alias more readable
							var cObj	= pObj[$this.tabsForEvent[tabName].extraSections[k]];

							$this[pObj.method](cObj);
							
						}//for
					}//if
//					todo: call internal objects 
				}//if
			}//if
			
			return false;
			
		}, //init
		
		//hide show section functionality
		toggleSection		: function($obj) {

			var $this	= this;
			//check if need to hide smth by default
			if($obj.hideByLoad  !== null){
				$this.toggleElements($obj.hideByLoad, false);
			}//if
			
			jQuery('input[name='+$obj.toggleElName+']').change(function(){
				
				var elValue	= jQuery(this).val();
				
				//checking by input type
				var inputType	= jQuery(this).attr('type');
				
				switch (inputType) {
					
					case 'radio':
						
						if(elValue == 'Yes'){
							$this.toggleElements($obj.toggleElements, $obj.hideOnYes);	
						}else{
							$this.toggleElements($obj.toggleElements, $obj.hideOnNo);
						}//if
						
						break;
					case 'checkbox':
						
						if (jQuery(this).is(':checked')){
							$this.toggleElements($obj.toggleElements, $obj.hideOnYes);
						} else {
							$this.toggleElements($obj.toggleElements, $obj.hideOnNo);
						}//if
						
						break;
					default:
						break;
						
				} //switch
				
			});//change
			
		}, //toggleSection
		
		/*
		hide elements
		@selector - jquery selector with class of id prefix (#el or .el)
		@toggleType - true - show, false = hide
		 */
		toggleElements		: function (selector, toggleType) {
			
			if (jQuery(selector) == undefined)
				return false;
			
			if (!toggleType){
				jQuery('#'+selector).hide();
			} else {
				jQuery('#'+selector).show();
			}
			
		}//hideElements
		
	}.init());//vEventObject
 	
 }//rmBalloonApp