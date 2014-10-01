$(document).ready(function() {

	var invit_emails = new Bloodhound({
	    datumTokenizer: function (datum) {
	        return Bloodhound.tokenizers.whitespace(datum.value);
	    },
	    queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: $('.tagsinput').attr('data-url-autocomplete')+'?email_is_like=%QUERY',
	        filter: function (inviteds) {
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
            
        })        
        ;
       
});
