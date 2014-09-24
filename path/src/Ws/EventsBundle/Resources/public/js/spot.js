$(document).ready(function() {

/*===========================================================
	// Autocomplete Spot Selection
============================================================*/
	
    $('input.autocomplete-spot').typeahead({
    	name:'spot',
    	valueKey:'slug',
		limit: 10,
		minLength: 3,
		allowDuplicates: true,	
		//local: array of datums,
		//prefetch: link to a json file with array of datums,
		remote: $('input.autocomplete-spot:first').attr('data-autocomplete-url')+'/FR/%QUERY',			
		template: [ '<p class="tt-name"><img class="flag flag-{{country}}" /> {{city}} <small>{{name}}</small></p>',
					'<p class="tt-sub">{{address}}</p>',
					'<p class="tt-id">{{id}}</p>',
					].join(''),
		engine: Hogan ,

		header: 'Sélectionner un endroit',
		//footer: 'footer',

	}).on('typeahead:selected',function( evt, datum ){
		$('input.autocomplete-spot:first').val(datum.slug);		
		$('input.autocomplete-spot_id:first').val( datum.id );
		$('input.autocomplete-spot:first').removeClass('empty');
		$('input.autocomplete-spot:first').val(datum.name);
	}).on('typeahead:opened',function(e){
		$('input.autocomplete-spot:first').addClass('open');		
	}).on('typeahead:closed',function(e){
		$('input.autocomplete-spot:first').removeClass('open');
		
	});



});




function countryFlag(state) {

	return "<img class='flag flag-"+state.id.toLowerCase()+"' /> "+state.text;
}
