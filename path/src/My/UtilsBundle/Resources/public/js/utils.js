$(document).ready(function() {

	var invit_emails = new Bloodhound({
	    datumTokenizer: function (datum) {
	        return Bloodhound.tokenizers.whitespace(datum.value);
	    },
	    queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: $('.tagsinput').attr('data-url-autocomplete')+'?email_is_like=%QUERY',
	        filter: function (inviteds) {
	        	console.log(inviteds);
	            // Map the remote source JSON array to a JavaScript array
	            return $.map(inviteds, function (invited) {
	                return {
	                	id: invited.id,
	                    email: invited.email
	                };
	            });
	        }
	    }
	});

	// Initialize the Bloodhound suggestion engine
	invit_emails.initialize();


	/*==================================
		TagsInput
	==================================*/
	$('.tagsinput')
		.tagsinput({
			tagClass: 'label label-info ws-tag',
			confirmKeys: [13,44,32,16], //enter, virgule, espace, shift
			trimValue:true,
			typeaheadjs: {
			name:'invit_emails',
			displayKey: 'email',
			valueKey: 'email',
			source: invit_emails.ttAdapter()
		}            
	});
	//on focus out, the text is converted in a tag
	$('.bootstrap-tagsinput input').blur(function() {
		$('input.tagsinput').tagsinput('add', $(this).val());
		$(this).val('');
	});
       

    /*==================================
    	Datetime Picker
    ==================================*/
    $('input.with_date_picker').datetimepicker({
    	lang:'fr',    	
		 timepicker:false,
		 format:'d/m/Y',
		 minDate: 0,
		 mask:true,
		 lazyInit: true,
		 dayOfWeekStart: 1,
	});

	$('input.with_time_picker').datetimepicker({
		lang:'fr',
		datepicker: false,
		format: 'H:i',
		step:30,
		defaultTime:'13:00'
	});

	$('select.with_checkboxlist').dropdownchecklist({
		minWidth:250,
	});


	/*==================================
		Tooltip bootstrap
	==================================*/	
	$('.tooltiptop').tooltip( { delay: { show: 200, hide: 100 }} );
	$('.tooltipbottom').tooltip( { placement : 'bottom', delay: { show: 200, hide: 100 }} );

	/*==================================
		Submit button
	==================================*/	
	$('.btn-ws-submit').on('click',function(){
		$(this).addClass('btn-ws-submit-clicked');
	});


	/*==================================
	MOBILE MENU
	===================================*/
	if($("#menu-mobile").length!=0 && $("#menu-mobile").css('display')!='none'){

		$("#mmenu").mmenu({});	
	}

});