
		
$(document).ready(function() {
	

	/*===========================================================
	// Autocomplete cityName input
============================================================*/
	//create the data loader with the URL
	var sports_loader = new Bloodhound({
	    datumTokenizer: function (datum) {
	        return Bloodhound.tokenizers.whitespace(datum.token);
	    },
	    queryTokenizer: Bloodhound.tokenizers.whitespace,
	    remote: {
	        url: $(".sport-autocomplete:first").attr('data-autocomplete-url')+'/%QUERY',
	        
	    }
	});
	// Initialize data loader (Bloodhound suggestion engine)
	sports_loader.initialize();

	//find all input who need the autocompletion's feature
	$('.sport-autocomplete').each(function(index){

		var input = $(this);
		var template_empty = input.attr('data-template-empty');
		var template_header = input.attr('data-template-header');
		var template_footer = input.attr('data-template-footer');
		var template_suggestion = Handlebars.compile( '<p class="tt-name"><span class="tt-icon ws-icon ws-icon-{{ icon }}"></span> {{name}}<span class="tt-sub">{{category}}</span><p class="tt-id">{{id}} (à cacher)</p></p>');
		var trigger_length = input.attr('data-trigger-length');
		
		input.typeahead(
			{		
			minLength: trigger_length
			},
			{
			name: 'sport'+index,
			displayKey: 'name',
			source: sports_loader.ttAdapter(),
			templates: {
				empty : template_empty,
				footer : template_footer,
				header : template_header,
				suggestion: template_suggestion
				},
			}
		)
		.on('typeahead:selected',function(evt,suggestion){
			$('.sport-id-autocompleted').get(index).value = suggestion.id;
		})
		;
	})


	$("select.sportSelection").each(function(){

		$(this).select2({
			formatResult: addSportIcon,
			formatSelection: addSportIcon
		});

	});	   

});


function addSportIcon(sport){		

	if(sport.id != 'undefined'){
		var icon = $(sport.element[0].outerHTML).attr('data-icon');
		return '<span class="ws-icon ws-icon-'+icon+'"></span> <span class="sport-name">'+sport.text+'</span>';		
	}
	else 
		return sport.text;
}