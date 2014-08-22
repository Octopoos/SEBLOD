jQuery(document).ready(function($){
	$(".lock_img").live('click', function(){
		var $child = $(this).children();
		var $next = $(this).next();
		if ( $child.hasClass('linked') ){
			$child.removeClass("linked").addClass("unlinked");
			$next.css('display','inline');
		}else{
			$child.removeClass("unlinked").addClass("linked");
			$next.css('display','none');
		}
	});
});