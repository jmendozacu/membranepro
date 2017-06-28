/*** printcard_bgb custom javascrips ***/

jQuery(document).ready(function() {
	
	// Login reset password toggle
	jQuery('#reset-password').click(function(){
		jQuery('.remember-form').addClass('open');
	});

	jQuery('#back-to-login').click(function(){
		jQuery('.remember-form').removeClass('open');
	});
	
	// styled select
	// jQuery('.input-box select').each(function(){
	    // jQuery(this).wrap('<div class="styled-select"></div>');
	// });

	// Modal cautare rapida
	jQuery('.cauta-rapid').click(function(){
		jQuery('.overlay').toggleClass('visible');
		jQuery('.cautare-rapida').toggleClass('visible');
	});

	//modal inchidere 
	jQuery('.overlay').click(function(){
		jQuery('.overlay').removeClass('visible');
		jQuery('.cautare-rapida').removeClass('visible');
	});
});