jQuery(
    function( $ ) {
	
	var w = 770;
	w = $(window).width();
	
	if (w < 1024) {
	
		$(".iframe").colorbox({
			iframe: true,
			width: "100%",
			height: "100%"
		});
	
		$(".inline").colorbox({
			inline: true,
			width: "100%",
			height: "100%"
		});
	
	} else {
	
		$(".iframe").colorbox({
			iframe: true,
			width: "80%",
			height: "80%"
		});
	
		$(".inline").colorbox({
			inline: true,
			width: "80%",
			height: "80%"
		});
	}
	
});