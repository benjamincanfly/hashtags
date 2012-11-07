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
		
		return true;
		
		$(".tweet").each(function(){
			
			var tweetID=$(this).attr('tweetid');
			
			var content="";
			var tweet=jsonTweets[tweetID];
			var text=tweet['text'];
			
			text=text.replace(/([\s]{1,})/g, ' ');
			text=$.trim(text);
			
			$(".text", this).html(text);
			
		});
		
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
								$(this).attr('color', 'blue');
							} else {
								$(this).attr('color', 'red');
							}
						} else if (divideBy==3){
							if(i>=(numOfThese*(2/3))){
								$(this).attr('color', 'green');
							} else if(i>=(numOfThese/3)) {
								$(this).attr('color', 'blue');
							} else {
								$(this).attr('color', 'red');
							}
							
						} else if (divideBy==4){
							if(i>=numOfThese*.75){
								$(this).attr('color', 'yellow');
							} else if(i>=numOfThese/2) {
								$(this).attr('color', 'green');
							} else if(i>=numOfThese/4) {
								$(this).attr('color', 'blue');
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
	wrap:function(){
		$(".tweet").each(function(){
			
			var tweetID=$(this).attr('tweetid');
			
			var content="";
			var tweet=jsonTweets[tweetID];
			var text=tweet['text'];
			
			text=text.replace(new RegExp('(#'+hashtag+')([\s]{0,})', 'gim'), "<span class='hash'>\$1 </span>");
			text=text.replace(new RegExp('(@jimmyfallon)([\s]{0,})', 'gim'), "<span class='atjimmy'>\$1 </span>").replace(new RegExp('(@latenightjimmy)([\s]{0,})', 'gim'), "<span class='atjimmy'>\$1 </span>");
			text=replaceURLWithHTMLLinks(text);
			text='<a href="http://www.twitter.com/'+tweet['user']+'" class="username">'+tweet['user']+' </a> '+text;
			
			//console.info(text);
			$(this).html(text);
		});
		
	},
	init:function(){
		
		LNJF.wrap();
		
		$("#viewControls input").live("click", function(){
			var rating=$(this).attr('rating');
			$("#tweets, #viewControls").attr('rating', rating);
		});
		
		$("#formatting input[name=includehash], #formatting input[name=includejimmy], #formatting input[name=includeusername], #formatting input[name=dofont]").live("change", LNJF.quickFormat);
		
		$("#formatting input[name=shownumbers], #formatting select").live("change", function(){
			LNJF.slowFormat();
		});
		
		LNJF.format();
		
	}
	
}

LNJF.init();