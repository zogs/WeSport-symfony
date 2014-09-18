
		
$(document).ready(function() {
	

	/*===========================================================
	// Autocomplete cityName input
============================================================*/	
 	$('input#sport_name').click(function(e){ 		
		$(this).val('');
		$('input#sport_id').val('');		
	});
	
    $('input#sport_name').typeahead({
    	name:'sport',
    	valueKey:'name',
		limit: 6,
		minLength: 3,
		allowDuplicates: true,	
		//local: array of datums,
		//prefetch: link to a json file with array of datums,
		remote: $("#sport_name").attr('data-autocomplete-url')+'/%QUERY',			
		template: [ '<p class="tt-name">{{name}}</p>',
					'<p class="tt-sub">{{category}}</p>',
					'<p class="tt-id">{{id}} (à cacher)</p>',
					].join(''),
		engine: Hogan ,

		//header: 'header',
		//footer: 'footer',

	}).on('typeahead:selected',function( evt, datum ){
		$(this).val(datum.name);		
		$('#sport_id').val( datum.id );
		$('#sport_name').removeClass('empty');
		$('#sport_name').val(datum.name);
	}).on('typeahead:opened',function(e){
		$("#sport_name").addClass('open');		
	}).on('typeahead:closed',function(e){
		$("#sport_name").removeClass('open');
		
	});


	if($("select.iconSportSelect").length != 0){
	    	$("select.iconSportSelect").select2({ formatResult: addSportIcon, formatSelection: addSportIcon});	    		    	
	}

});


function addSportIcon(sport){	
	if(trim(sport.id)!='')
		return '<span class="ws-icon ws-icon-small ws-icon-'+sport.text.toLowerCase()+'"></span> '+sport.text;		
	else 
		return sport.text;
}