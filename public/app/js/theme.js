		$('#style-toggle').click(function (){
			$(this).toggleClass('null-style');
		   if ($(this).hasClass('null-style')){
		      $('.theme-styles').attr('href','null');
		   }
		   else{
		   	$('.theme-styles').attr('href','/app/css/theme.css');
		   }
		});

		$(document).ready(function(){
			$('.widget-header').wrap('<div class="carbon"></div');
		});