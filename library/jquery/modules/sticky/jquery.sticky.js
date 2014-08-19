/**
 * Sets any element to fixed after scrolling a certain length on page.
 * <div class='sticky' sticky='150'></div>
 * This should set the position to fixed after 150px scrolling down, then reset it back to relative afterwards. 
 */
var stuck = 0;
jQuery(document).ready(function(){
	window.onscroll = function() {
	    $(".sticky").each(function(){
	       var $height2stick = $(this).attr("sticky");
	       if($height2stick != null) {
	           if(window.pageYOffset >= $height2stick) {
	               $(this).css({position: 'fixed', top: 0});
	               $(this).addClass("stuck");
				   if(typeof afterWindowScroll == 'function') {
				   		afterWindowScroll(1);
				   }
	           }
	           else {
	               $(this).css({position: 'relative'});
	               $(this).removeClass("stuck");
	               if(typeof afterWindowScroll == 'function') {
	               	   afterWindowScroll(0);
	               }
	           }
	       }
	    });
	};
});