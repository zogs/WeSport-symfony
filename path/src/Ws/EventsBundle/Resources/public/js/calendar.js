$(document).ready(function(){

	//calendar form
	$('#calendar_search_type,#calendar_search_level').dropdownchecklist({
		emptyText: "indifférent",
		width: 130
	});

	$('#calendar_search_area,#calendar_search_price').select2({	
		width: 130	
	});

	$('#calendar-form-options-toggle').click(function(){
		$('#calendar-form-options-container').toggleClass('open');
	});

	


	if($('#calendar-content').length){


		$('#calendar-search').submit(function(e){
			callThisWeek();
			e.preventDefault();
			return false;

		});

		//init var 
		var _body = $('body');
		var _cal = $('#calendar-content');
		var _zone = $('#calendar');
		var _aPrev = $('#pullPrev');
		var _aNext = $('#pullNext');	
		var _loader = $('#calendar-loader');
		var _screenWidth = $(window).width();
		var _drag = false;
		var _cWeek = true;
		var _anim;
		var _nbDays = 0, _displayDays, _dayDisplayed = 0;
		var _newPage=1, _loadingEvents= false, _moreEvents = true;
		var _newWeeks, _newWeekFirst, _oldWeek, _newDays, _newHidden, _hidden;
		var _wDrag = 150;
		var _xO;
		var _yO;
		var _cX,_cY;
		var _mxO;
		var _myO;
		var _x;
		var _y;
		var _lock;


		//Appel la semaine courante
		callThisWeek();

		window.cancelRequestAnimFrame = ( function() {
		    return window.cancelAnimationFrame          ||
		        window.webkitCancelRequestAnimationFrame    ||
		        window.mozCancelRequestAnimationFrame       ||
		        window.oCancelRequestAnimationFrame     ||
		        window.msCancelRequestAnimationFrame        ||
		        clearTimeout
		} )();

		window.requestAnimFrame = (function(){
		    return  window.requestAnimationFrame       || 
		        window.webkitRequestAnimationFrame || 
		        window.mozRequestAnimationFrame    || 
		        window.oRequestAnimationFrame      || 
		        window.msRequestAnimationFrame     || 
		        function(/* function */ callback, /* DOMElement */ element){
		            return window.setTimeout(callback, 1000 / 60);
		        };
		})();


		//set drag listeners
		setInitialListener();
		function setInitialListener(){
			_zone.on('mousedown touchstart',startDrag);
			$(window).on('mouseup touchend',stopDrag);
			setCalendarOrigin();
		}
		function setCalendarOrigin(){
			pos = _cal.position();
			_xO = pos.left;
		}
		function setMouseOrigin(e){
			_mxO = getClientX(e) +_xO;
		}
		function setMouseCoord(e){
			_cX = getClientX(e);
		}
		function getClientX(e){
			if(e.clientX) return e.clientX;
			if(e.originalEvent.changedTouches[0].clientX) return e.originalEvent.changedTouches[0].clientX;
		}
		function startDrag(e){			
			
			if(e.which == 2 ||e.which == 3) return; //if middle click or right click , cancel drag

			setMouseOrigin(e);
			setMouseCoord(e);

			_drag = true;
			
			$(window).on('mousemove touchmove',setMouseCoord);

			dragCalendar();
		}

		function stopDrag(e){		

			if(_lock=='left'){ 
				callPreviousWeek(); 
				lockLoad();
			}
			else if(_lock=='right'){ 
				callNextWeek();  
				lockLoad();
			}
			else {
				revert();
				_drag = false;
			}

			if (navigator.vibrate) { navigator.vibrate(0); }
			$(window).off('mousemove touchmove');
			cancelRequestAnimFrame(_anim);


		}

		function dragCalendar(e){

			//distance between mouse coord and initial coord
			var x = _xO + _cX - _mxO;

			//console.log('_xO='+_xO+' _cX='+_cX+' _mxO='+_mxO+' == '+x);
			//if it is the first week and drag to previous return false
			if(isCurrentWeek()==true && x>0 ) {
				_anim = requestAnimFrame(dragCalendar,_cal);
				return;
			}
			//if the drag distance if inferior to 10 px , return false
			if(Math.sqrt(Math.pow(x,2))<10) {
				_anim = requestAnimFrame(dragCalendar,_cal);
				return;
			}
			//if the drag distance is superior to the trigger width

			//prevent android bug where touchmove fire only once
			//if( navigator.userAgent.match(/Android/i) ) {
			//    e.preventDefault();
			//}


			if(x>=_wDrag) {
				_cal.css('left',_wDrag);
				lockPrev(); //set lock to previous
				if (navigator.vibrate) { navigator.vibrate(2000); }
				_anim = requestAnimFrame(dragCalendar,_cal);
				return;
			}
			if(x<=-_wDrag) {
				_cal.css('left',-_wDrag);
				lockNext(); //set lock to next week
				if (navigator.vibrate) { navigator.vibrate(2000); }
				_anim = requestAnimFrame(dragCalendar,_cal);
				return;
			}
			//set no lock
			nolock();

			x += 'px';

			_cal.css('left',x);
			_cal.css('z-index',0);

			_anim = requestAnimFrame(dragCalendar,_cal);
		}

		function revert(){
			_cal.animate({left:0}, _wDrag, 'swing');
		}

		function nolock(){
			_lock = '';		
			_cal.find('#pullPrev,#pullNext').removeClass('locked').removeClass('loading');
		}
		function lockPrev(){
			_lock = 'left';
			_cal.find('#pullPrev').addClass('locked');
		}
		function lockNext(){
			_lock = 'right';
			_cal.find('#pullNext').addClass('locked');		
		}
		function lockLoad(){
			_lock='';
			_cal.find('#pullPrev,#pullNext').removeClass('locked').addClass('loading');
		}

		function slideCalendar(direction){

			var width = _screenWidth;
			var slideDuration = 700;
			var delayDisplay = parseInt(slideDuration/_nbDays);

			if(direction == 'right') {
				contentPosition = width;
				contentSliding = '-='+width;
			}
			if(direction == 'left') {
				contentPosition = -width;
				contentSliding = '+='+width;
			}
			if(typeof direction == 'undefined'){
				contentPosition = -width;
				contentSliding = '+='+width;
				slideDuration = 0;
			}

			var weeks = _oldWeek.add(_newWeeks);

			_cal.css('left',0);
			
			weeks.addClass('sliding');

			_newWeeks.css({'left':contentPosition+'px'});

			if(direction=='left') _newDays = _newDays.get().reverse(); //reverse order the colomn are displayed
			_displayDays = setInterval(function(){ displayColomns(direction)},delayDisplay);

			_oldWeek.find('.events-day').empty();

			_newWeeks.animate({
				left:contentSliding,
				},slideDuration,'easeOutCirc',function(){ 
						
						_oldWeek.remove();
						_newWeeks.removeClass('sliding');
						setHeightCalendar();						
						return;				
			});


		}

		function clickEvent(e){
			
			if(_drag==true) {
				e.preventDefault();
				return false;
			}
			else {
				var link = e.currentTarget;			
				$(link).parent().addClass('clicked');				
			}
		}


		function displayColomns(direction){

			var colomn = $(_newDays[_dayDisplayed])
			colomn.addClass('displayed');

			colomnBindEvent(colomn);

			_dayDisplayed++;
			if(_dayDisplayed>=_nbDays) {
				clearInterval(_displayDays);
				_dayDisplayed = 0;
			}
		}

		function colomnBindEvent(colomn){
			colomn.on('click','.events-link',clickEvent);
			colomn.find('.tooltipbottom').tooltip({ placement : 'bottom', delay: { show: 200, hide: 100 }});
		}





		function setHeightCalendar(){

			var minHeight = parseInt(_newWeeks.css('minHeight'));
			var heightCalendar = _newWeeks.css('height','auto').height();	

			if(heightCalendar > minHeight){
				_cal.css('height',heightCalendar);
				_newWeeks.css('height',heightCalendar);
			} else {
				_cal.css('height',minHeight);
				_newWeeks.css('height',minHeight);
			}
			
		}


		function isCurrentWeek(){
			
			if(_cWeek==true) return true;
			return false;
		}

		function setCurrentWeek(){
			
			if(_newWeeks.hasClass('current-week')) _cWeek = true;
			else _cWeek = false;
			return _cWeek;
		}

		function callWeek(url,direction){		

			_loader.show();		
			
			var form = $('#calendar-search').serialize();
			form += '&nbdays='+findNumberDayPerWeek();

			$.ajax({
				type:'GET',
				url: url,
				data : form,
				success: function( newhtml ){						

					var oldhtml = _cal.html();
					document.getElementById('calendar-content').innerHTML = oldhtml+newhtml;

					_oldWeek = _cal.find(".events-weeks:first").attr('id','old-week');
					_newWeeks = _cal.find(".events-weeks:last").attr('id','new-week');	
					_newWeekFirst = _newWeeks.find('.events-week:first');				
					_newDays = _newWeeks.find('td.events-day');
					_newHidden = _newDays.find('.hidden');
					_search_url = _newWeeks.attr('data-search-url');

					_hidden = [];
					for(var i=0; i<_newHidden.length; i++){
						var obj = $(_newHidden[i]);									
						_hidden[obj.attr('id')] = obj.offset().top;
						
					}				

					slideCalendar(direction);	  				
					
					setCurrentWeek();

					$(window).scroll(); //refresh scroll function to display new event

					_newPage = 1;
					_moreEvents = true;
					_drag = false;

					if(isCurrentWeek()){					
						$('a.calendar-nav-prev').hide();
					}
					else{
						$('a.calendar-nav-prev').show();
					}

					updateUrl(_search_url);
					
									
					_loader.hide();


				},
				dataType:'html'
			});		

			return false;
		}

		function updateUrl(search_url)
		{	
			var state = {
			  "slider_calendar": true
			};

			var url = document.URL;
			var reg = new RegExp("/(calendar\/?.*)", "g");
			url = url.replace(reg, '/calendar/'+search_url);
			history.replaceState(state, search_url, url);
		}

		function addEventBefore(obj,evt){

			$(obj).before(evt);

			setHeightCalendar();
		}

		function addEventsToColomn(colomn,evts){

			var obj = $(colomn).find('.addEvent');
			var delay = 50;

			for(var i in evts){
				setTimeout(function(){
					addEventBefore(obj,evts[i]);
				},delay*i);			
			}		
			$(colomn).on('click','.events-link',clickEvent);
		}
		function loadBottomEvents(){

			console.log('load bottom');

			_loader.show();
			var url = _cal.attr('data-url-calendar-bottom');
			var form = $('#calendar_search').serialize();
			form += '&maxdays='+findNumberDayPerWeek();
			form += '&page='+_newPage;


			$.ajax({
				type:'GET',
				url: url,
				data : form,
				success: function( data ){				
					
					if(data.results!='empty'){

						var results = data.results;
						
						$(_newDays).each(function(){						

							var date = $(this).attr('data-date');

							if(results[date]){

								//addEventsToColomn(this,results[date]);
								$('#addPoint'+date).before(results[date]);

								var new_events = $('#colomn-'+date).find('.hidden');
								new_events.each(function(){								
									_hidden[$(this).attr('id')] = $(this).offset().top;
								});

								$(window).scroll(); //refresh scroll function to display new event
							
								colomnBindEvent($(this));

								//reset height of the calendar after all the hidden events have been displayed
								setTimeout(function(){ setHeightCalendar();},1000);

							}
						});	
						
					}
					else{	
						console.log('empty');				
						_moreEvents = false;
						_newPage = 1;
					
					}

					_loader.hide();
					_loadingEvents = false;


				},
				dataType:'json'
			});		

		}


		//infiniteEvents();
	    function infiniteEvents() {

	        $(window).scroll(function(){
	            
	            //position du scrolling
	            var scrollPos = parseInt($(window).scrollTop()+$(window).height());


	            //vérifie si chaque evenement caché est revélé par le scroll
	            for(var id in _hidden){
	            	var y = _hidden[id];            	
	            	if( y <= scrollPos){
						//si oui afficher l'evenement avec un petit delai            		          		            		
	            		$('#'+id).css('visibility','visible').hide().delay(300).fadeIn(200);
	            		//enlever l'evenement concerné du tableau
	            		delete _hidden[id];
	            		  
	            	}
	            }



	            //
	            if($(_newWeekFirst).length!=0){
	            	//position du bas de la premiere ligne
	            	var y = parseInt($(_newWeekFirst).offset().top+$(_newWeekFirst).height()); 
	            
		            //si le bas est atteint && calendar is not dragged && no events is loading && more events are possibbly to call
		            //console.log(y+' <= '+scrollPos+' && '+_drag+' && '+_loadingEvents+' && '+_moreEvents);
		            if( (y <= scrollPos ) && _drag===false && _loadingEvents === false && _moreEvents === true ) 
		            {               		            	
		                _loadingEvents = true;
		                _newPage        = _newPage+1;                                
		                loadBottomEvents();		                                   
		            }
	            }
	            

	            
	        });
	    };



		function findNumberDayPerWeek(){

			if(_nbDays!=0) return _nbDays;
			//Nombre de jour à afficher en fonction de la largeur de l'écran
			var dayPerWeek = {320:1,480:2,768:3,1024:4,1280:5,1440:6,10000:7};

			for(var maxwidth in dayPerWeek){	
				if(_screenWidth<=maxwidth) {
					_nbDays = dayPerWeek[maxwidth];	
					return _nbDays;
				}
			}
			return _nbDays;
		}

		function callNextWeek(){
			var url = _cal.attr('data-url-calendar-next');
			var direction = 'right';
			callWeek(url,direction);
		}

		function callPreviousWeek(){
			var url = _cal.attr('data-url-calendar-prev');
			var direction = 'left';
			callWeek(url,direction);
		}

		function callThisWeek(direction){
			var url = _cal.attr('data-url-calendar-now');
			callWeek(url,direction);
		}

		function callCurrentWeek(direction){
			var url = _cal.attr('data-url-calendar-date');
			var date = $('.events-weeks').attr('data-first-day');
			url = url+'/'+date;
			callWeek(url,direction);
		}

		$('a.calendar-nav-prev').on('click',function(e){
				callPreviousWeek();
				e.preventDefault();
				e.stopPropagation();
				//ga('send','event','Calendrier','SemainePrecedante');
				//ga('send', 'pageview', '/calendar/prev');
				return false;
		});
		$('a.calendar-nav-next').on('click',function(e){
				callNextWeek();
				e.preventDefault();
				e.stopPropagation();
				//ga('send','event','Calendrier','SemaineSuivante');
				//ga('send', 'pageview', '/calendar/next');
				return false;		
		});
		$('a.calendar-nav-now').on('click',function(e){
				e.preventDefault();
				e.stopPropagation();
				callThisWeek('prev');
				return false;
		});
	}
});