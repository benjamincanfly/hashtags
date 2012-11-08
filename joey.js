
$("a#joeyLink").live("click", function(){
	var matte=$('<div id="joey"><div></div></div>').appendTo('body').on("mousedown", function(){
		
		$("#audio").append("<embed src='/audio/joey/22.mp3' autostart='true' loop='false'/>");
		$(this).remove();
		
	});
	
	var whichSound=1+Math.round(Math.random()*21);
	$("#audio").append("<embed src='/audio/joey/"+whichSound+".mp3' autostart='true' loop='false'/>");
	
	$("div", matte).on("mousedown", function(){
		var whichSound=1+Math.round(Math.random()*21);
		$("#audio").append("<embed src='/audio/joey/"+whichSound+".mp3' autostart='true' loop='false'/>");
		return false;
	});
	
});