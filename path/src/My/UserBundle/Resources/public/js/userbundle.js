$(document).ready(function() {

	$("#fos_user_registration_form_username").change(function(){

		var input = $(this);
		var control = input.parent().parent();
		var helper = input.next('.helper');
		var url = input.attr('data-url-checker');

		//check for forbidden characters
		if(chars = hasCharacters(input.val()," @,.;:\\/!?&$£*§~#|)(}{][")){
			control.toggleClass('control-error');
			helper.toggleClass('hide').empty().html("Le caractère suivant n'est pas autorisé : "+chars);
			return;
		} else {
			control.toggleClass('control-error');
			helper.toggleClass('hide').empty();
		}


		$.ajax({
			type: 'GET',
			url: url,
			data: {username : input.val() },
			success: function(data){
				console.log(data);					
				if(data.error){						
					control.removeClass('control-success');					
					control.addClass('control-error');
					helper.removeClass('hide').empty().html( data.error );
				}
				else {
					control.removeClass('control-error');
					control.addClass('control-success');	
					helper.addClass('hide').empty();					
				}
			},
			dataType: 'json'
		});

		return;
	});

});