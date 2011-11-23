jQuery(document).ready(function($){

	/* http://keith-wood.name/backgroundPos.html
	   Background position animation for jQuery v1.0.1.
	   Written by Keith Wood (kbwood{at}iinet.com.au) November 2010.
	   Dual licensed under the GPL (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt) and 
	   MIT (http://dev.jquery.com/browser/trunk/jquery/MIT-LICENSE.txt) licenses. 
	   Please attribute the author if you use it. */
	(function($){var g='bgPos';$.fx.step['backgroundPosition']=$.fx.step['background-position']=function(a){if(!a.set){var b=$(a.elem);var c=b.data(g);b.css('backgroundPosition',c);a.start=parseBackgroundPosition(c);a.end=parseBackgroundPosition($.fn.jquery>='1.6'?a.end:a.options.curAnim['backgroundPosition']||a.options.curAnim['background-position']);for(var i=0;i<a.end.length;i++){if(a.end[i][0]){a.end[i][1]=a.start[i][1]+(a.end[i][0]=='-='?-1:+1)*a.end[i][1]}}a.set=true}$(a.elem).css('background-position',((a.pos*(a.end[0][1]-a.start[0][1])+a.start[0][1])+a.end[0][2])+' '+((a.pos*(a.end[1][1]-a.start[1][1])+a.start[1][1])+a.end[1][2]))};function parseBackgroundPosition(c){var d={center:'50%',left:'0%',right:'100%',top:'0%',bottom:'100%'};var e=c.split(/ /);var f=function(a){var b=(d[e[a]]||e[a]||'50%').match(/^([+-]=)?([+-]?\d+(\.\d*)?)(.*)$/);e[a]=[b[1],parseFloat(b[2]),b[4]||'px']};if(e.length==1&&$.inArray(e[0],['top','bottom'])>-1){e[1]=e[0];e[0]='50%'}f(0);f(1);return e}$.fn.animate=function(e){return function(a,b,c,d){if(a['backgroundPosition']||a['background-position']){this.data(g,this.css('backgroundPosition')||'center')}return e.apply(this,[a,b,c,d])}}($.fn.animate)})(jQuery);


	//Width of graphic in pixels. Just to keep things logical.
	//Also the amount that it will move every x seconds...
	var i = -400;

	//for each .tdd_pb_bar, set a function that does this thing...
	$('.tdd_pb_bar').each(function(index,value){

		//Get the background position (x and y), split into an array
		var currbackgroundposn = $(value).css('background-position').split(' ', 2 );
		var x = Number(currbackgroundposn[0].split('px',1));
		var y = currbackgroundposn[1];

		//Run it once to start us off.		
		$(value).animate( {'background-position': i + 'px ' + y }, 15000, 'linear' );

		
		setInterval( function(){

			var currbackgroundposn = $(value).css('background-position').split(' ', 2 );
			var x = Number(currbackgroundposn[0].split('px',1));

			var posn = x+i + 'px ' + y;
			
			$(value).animate( {'background-position': posn }, 15000, 'linear' );
				
		}, 15000 );
		
	}); // .each
		

}); //doc.ready