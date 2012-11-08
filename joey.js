
$("a#joeyLink").live("click", function(){
	var matte=$('<div id="joey"><div></div></div>').appendTo('body').on("mousedown", function(){
		
		var whichSound=Math.round(Math.random()*22);
		$("#audio").append("<embed src='/audio/joey/"+whichSound+".mp3' autostart='true' loop='false'/>");
		$(this).remove();
		
	});
	
	var whichSound=Math.round(Math.random()*22);
	$("#audio").append("<embed src='/audio/joey/"+whichSound+".mp3' autostart='true' loop='false'/>");
	
	$("div", matte).on("mousedown", function(){
		var whichSound=Math.round(Math.random()*22);
		$("#audio").append("<embed src='/audio/joey/"+whichSound+".mp3' autostart='true' loop='false'/>");
		return false;
	});
	
});