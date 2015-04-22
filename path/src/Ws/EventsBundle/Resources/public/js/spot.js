$(document).ready(function() {

/*===========================================================
	// Autocomplete Spot Selection
============================================================*/
	//create the data loader with the URL
	var spots_loader = new Bloodhound({
	    datumTokenizer: function (datum) {
	        return Bloodhound.tokenizers.whitespace(datum.token);
	    },
	    queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: $("input.autocomplete-spot:first").attr('data-autocomplete-url')+'/FR/%QUERY',
	        
	    }
	});

	// Initialize data loader (Bloodhound suggestion engine)
	spots_loader.initialize();

	//find all input who need the autocompletion's feature
	$('input.autocomplete-spot').each(function(index){

		var input = $(this);
		var template_empty = input.attr('data-template-empty');
		var template_header = input.attr('data-template-header');
		var template_footer = input.attr('data-template-footer');
		var template_suggestion = Handlebars.compile( '<p class="tt-name"><strong>{{city}}</strong> <small>{{name}}</small></p><p class="tt-sub">{{address}}</p><p class="tt-id">{{id}}</p>');
		var trigger_length = input.attr('data-trigger-length');
		
		input.typeahead(
			{		
			minLength: trigger_length
			},
			{
			name: 'spot'+index,
			displayKey: function(datum) { return datum.slug },
			source: spots_loader.ttAdapter(),
			templates: {
				empty : template_empty,
				footer : template_footer,
				header : template_header,
				suggestion: template_suggestion
				},
			}
		)
		.on('typeahead:selected',function(evt,suggestion){
			$('.autocompleted-spot_id').get(index).value = suggestion.id;
		})
		;

	});
	
});
