function replaceURLWithHTMLLinks(text) {
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(exp,"<a href='$1' target=_NEW>$1</a>"); 
}

var LNJF={
	
	format:function(){
		
		LNJF.quickFormat();
		LNJF.slowFormat();
			
	},
	quickFormat:function(){
		$("#tweets").attr('showusername', $("#formatting input[name=includeusername]").attr('checked')?'yes':'no');
		$("#tweets").attr('showhash', $("#formatting input[name=includehash]").attr('checked')?'yes':'no');
		$("#tweets").attr('showatjimmy', $("#formatting input[name=includejimmy]").attr('checked')?'yes':'no');
		$("#tweets").attr('dofont', $("#formatting input[name=dofont]").attr('checked')?'yes':'no');
	},
	slowFormat:function(){
		
		if($("#formatting input[name=shownumbers]").attr('checked')){
				$("#tweets").addClass('shownumbers');
				
				var divideBy=$("#formatting select[name=divideby]").val();
				
				$("#tweets .tweets").each(function(){
					
					var numOfThese=$('.tweet', this).size();
					
					$(".tweet", this).each(function(i){
						
						if(divideBy==2){
							if(i>=numOfThese/2){
								$(this).attr('color', 'green');
							} else {
								$(this).attr('color', 'red');
							}
						} else if (divideBy==3){
							if(i>=(numOfThese*(2/3))){
								$(this).attr('color', 'blue');
							} else if(i>=(numOfThese/3)) {
								$(this).attr('color', 'green');
							} else {
								$(this).attr('color', 'red');
							}
							
						} else if (divideBy==4){
							if(i>=numOfThese*.75){
								$(this).attr('color', 'yellow');
							} else if(i>=numOfThese/2) {
								$(this).attr('color', 'blue');
							} else if(i>=numOfThese/4) {
								$(this).attr('color', 'green');
							} else {
								$(this).attr('color', 'red');
							}
						}
						
					});
					
				});
				
				
			} else {
				$("#tweets").removeClass('shownumbers');
				$("#tweets .tweet").attr('color','');
			}
			
	},
	init:function(){
		
		//LNJF.wrap();
		
		var tweetCounts=[0,0,0,0];
		
		for(tweet in tweets){
			tweetCounts[tweets[tweet]['rating_3']]++;
			var tweetCode=HT.fave.tweetCode(tweets[tweet]);
			//console.info('appending tweet rated '+tweets[tweet]['rating_3']);
			$('#tweets > .tweets[rating="'+tweets[tweet]['rating_3']+'"]').append(tweetCode);
			$('#tweets > .tweets[rating="all"]').append(tweetCode);
		}
		
		//// $("#viewControls");
		
		$("#viewControls input").live("click", function(){
			var rating=$(this).attr('rating');
			$("#tweets, #viewControls").attr('rating', rating);
		});
		
		$("#formatting input[name=includehash], #formatting input[name=includejimmy], #formatting input[name=includeusername], #formatting input[name=dofont]").live("change", LNJF.quickFormat);
		
		$("#formatting input[name=shownumbers], #formatting select").live("change", function(){
			LNJF.slowFormat();
		});
		
		//LNJF.format();
		
	}
	
}

$(document).ready(LNJF.init);