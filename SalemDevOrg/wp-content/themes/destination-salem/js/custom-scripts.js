jQuery(function(){

	/* magic buttons on home */
	var interests = []
	jQuery('.itin').toggle(function(){
		jQuery(this).addClass('active').addClass('x-active');
		jQuery(this).find('.x-particle').addClass('x-active');
		interests.push(jQuery(this).attr('id'));
		var url = '/your-adventure/?interest=' + interests;
		jQuery('#begin-btn').attr('href',url);
	}, function(){
		jQuery(this).removeClass('active').removeClass('x-active');
		jQuery(this).find('.x-particle').removeClass('x-active');
		var i = jQuery(this).attr('id'); 

		if (interests.indexOf(i)!=-1) {
            interests.splice(interests.indexOf(i),1);
        } else {
            interests.push(i);
        }
        var url = '/your-adventure/?interest=' + interests;
		jQuery('#begin-btn').attr('href','');
	});

	/* inject itinerary into hidden form field on itin page */

	jQuery('#itinerary_email').load('/itinerary-email/',function(){
		var html = jQuery('#itinerary_email').html();
		jQuery('#field_email-content').empty().html('<html><body>'+html+'</body></html>');
	});
	jQuery('#wpfavoritepostswidget-2').on("DOMSubtreeModified",function(){
		jQuery('#itinerary_email').html('').load('/itinerary-email',function(){
			var html = jQuery('#itinerary_email').html();
			jQuery('#field_email-content').empty().html('<html><body>'+html+'</body></html>');
		});

	});
	var currentdate = new Date(); 
    jQuery('#field_date').val(currentdate);
    function resizeIframe(obj) {
		obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	}
    /*
    jQuery('.fa').hover(function(){
	        var title = jQuery(this).attr('title');
	        jQuery(this).data('tipText', title).removeAttr('title');
	        jQuery('<p class="tooltip"></p>').text(title).appendTo('body').fadeIn('slow');
	}, function() {
	        // Hover out code
	        jQuery(this).attr('title', jQuery(this).data('tipText'));
	        jQuery('.tooltip').remove();
	}).mousemove(function(e) {
	        var mousex = e.pageX + 20; //Get X coordinates
	        var mousey = e.pageY + 10; //Get Y coordinates
	        jQuery('.tooltip').css({ top: mousey, left: mousex })
	});
	*/
})

