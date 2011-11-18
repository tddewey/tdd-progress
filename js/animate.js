jQuery(document).ready(function($){

	var i = -700;

	setInterval(function() {
	
			$('.tdd_pb_bar').animate( {'background-position-x': i}, 18000, 'linear' );
			i = i - 700;

	} , 1000);

});