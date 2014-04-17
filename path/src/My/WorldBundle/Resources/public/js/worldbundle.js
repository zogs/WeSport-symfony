$(document).ready(function() {

/*===========================================================
	// Autocomplete cityName input
============================================================*/
 	$('input#city_name').click(function(e){ 		
		$(this).val('');
		$('input#city_id').val('');		
	});
	
    $('input#city_name').typeahead({
    	name:'city',
    	valueKey:'name',
		limit: 6,
		minLength: 3,
		allowDuplicates: true,	
		//local: array of datums,
		//prefetch: link to a json file with array of datums,
		remote: $("#city_id").attr('data-autocomplete-url')+'/FR/%QUERY',			
		template: [ '<p class="tt-name">{{name}}</p>',
					'<p class="tt-sub">{{state}}</p>',
					'<p class="tt-id">{{id}} (à cacher)</p>',
					].join(''),
		engine: Hogan ,

		header: 'Sélectionner une ville',
		//footer: 'footer',

	}).on('typeahead:selected',function( evt, datum ){
		$(this).val(datum.name);		
		$('#city_id').val( datum.id );
		$('#city_name').removeClass('empty');
		$('#city_name').val(datum.name);
	}).on('typeahead:opened',function(e){
		$("#city_name").addClass('open');		
	}).on('typeahead:closed',function(e){
		$("#city_name").removeClass('open');
		
	});

	if($('.geo-select').length != 0){

		$('.geo-select-country').select2({ formatResult: countryFlag, formatSelection: countryFlag});				
		$('.geo-select:not(.geo-select-country,.hide)').select2();
	}


	/*===========================================================
	// Location FORM
	============================================================*/	
	$('.geo-select-ajax').change(function(){

		var parent = $(this);
		var url = parent.attr('data-ajax-url');
		var level = parent.attr('data-geo-level');
		var value = parent.val();
		parent.addClass('geo-loading');

		$.ajax({
			type: 'GET',
			url: url,
			data: { level: level, value: value },
			dataType: 'json',
			success: function(data){

				parent.removeClass('geo-loading');
				$('#'+data.level+'_select_field').empty().append(data.options).select2().show();
							
			}
		})
	});
});




function countryFlag(state) {

	return "<img class='flag flag-"+state.id.toLowerCase()+"' /> "+state.text;
}
