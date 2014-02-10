$(document).ready(function() {
	
	if($("#event_sport_name").length != 0){

	    	$("#event_sport_name").select2({ formatResult: addSportIcon, formatSelection: addSportIcon});	    	
	    	
	}


});


function addSportIcon(sport){	
	if(trim(sport.id)!='')
		return '<span class="ws-icon ws-icon-small ws-icon-'+sport.text.toLowerCase()+'"></span> '+sport.text;		
	else 
		return sport.text;
}