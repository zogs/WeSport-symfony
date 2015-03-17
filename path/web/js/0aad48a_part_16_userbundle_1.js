$(document).ready(function() {

	$("#fos_user_registration_form_username").change(function(){

		var input = $(this);
		var control = input.parent().parent();
		var helper = input.next('.helper');
		var url = input.attr('data-url-checker');

		//check for forbidden characters
		if(chars = hasCharacters(input.val()," @,.;:\\/!?&$£*§~#|)(}{][")){
			control.toggleClass('control-error');
			helper.toggleClass('hide').empty().html("Les caractères suivants ne sont pas autorisés : "+chars);
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

	$("#fos_user_registration_form_email").change(function(){

		var input = $(this), 
		control = input.parent().parent(), 
		helper = input.next('.helper'), 
		url = input.attr('data-url-checker');

		var regex = new RegExp("[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,4})","g");
		var valid = input.val().match(regex);
		if(valid){
			control.removeClass('control-error');
			helper.addClass('hide').empty();

			// check if the email already exist in the database
			$.ajax({
				type:'GET',
				url: url,
				data: {email: input.val() },
				success: function(data){
					if(data.error){
						control.removeClass('control-success').addClass('control-error');
						helper.removeClass('hide').empty().html( data.error );
					}
					else {
						control.removeClass('control-error').addClass('control-success');
						helper.addClass('hide').empty();
					}
				},
				dataType: 'json'
			});
		}
		else {
			control.addClass('control-error');
			helper.removeClass('hide').empty().html("Hum... Ce n'est pas une adresse email valide !");
		}

		return;
	});

	$("#fos_user_registration_form_plainPassword_first").change(function(){

		$('#control-first').removeClass('control-error').addClass('control-sucess');
	});

	$("#fos_user_registration_form_plainPassword_second").change(function(){

		if($(this).val() == $('#fos_user_registration_form_plainPassword_first').val()){
			$('#control-first,#control-second').removeClass('control-error').addClass('control-success');
			$('#control-second .controls').find('.helper').addClass('hide').empty();
		}
		else {
			$('#control-first,#control-second').removeClass('control-success').addClass('control-error');
			$('#control-second .controls').find('.helper').removeClass('hide').empty().html("Fait attention, les mots de passe ne sont identiques !");
		}		
	});

});