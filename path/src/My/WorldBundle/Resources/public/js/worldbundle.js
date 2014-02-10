$(document).ready(function() {

/*===========================================================
	// Autocomplete cityName input
============================================================*/	
 	$('input#event_location_city_name').click(function(e){ 		
			if($(this).hasClass('notempty')) { 
				$(this).val('');
				$('input#event_location_city_id').val('');
		}
	});
	
    $('input#event_location_city_name').typeahead({
    	name:'city',
    	valueKey:'name',
		limit: 6,
		minLength: 3,
		allowDuplicates: true,	
		//local: array of datums,
		//prefetch: link to a json file with array of datums,
		remote: $("#event_location_city_id").attr('data-autocomplete-url')+'/FR/%QUERY',			
		template: [ '<p class="tt-name">{{name}}</p>',
					'<p class="tt-sub">{{state}}</p>',
					'<p class="tt-id">{{id}} (à cacher)</p>',
					].join(''),
		engine: Hogan ,

		//header: 'header',
		//footer: 'footer',

	}).on('typeahead:selected',function( evt, datum ){
		$(this).val(datum.name);		
		$('#event_location_city_id').val( datum.id );
		$('#event_location_city_name').removeClass('empty');
		$('#event_location_city_name').val(datum.name);
	}).on('typeahead:opened',function(e){
		$("#event_location_city_name").addClass('open');		
	}).on('typeahead:closed',function(e){
		$("#event_location_city_name").removeClass('open');
		
	});
});

