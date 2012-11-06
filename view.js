
var LNJF={
	
	format:function(){
		
		$(".tweet").each(function(){
			
			var tweetID=$(this).attr('tweetid');
			
			var content="";
			var tweet=jsonTweets[tweetID];
			var text=tweet['text'];
			
			if(!$("#formatting input[name=includehash]").attr('checked')){
				text=text.replace(new RegExp('#'+hashtag, 'gim'), '');
			}
			
			if(!$("#formatting input[name=includejimmy]").attr('checked')){
				text=text.replace(new RegExp('@jimmyfallon', 'gim'), '').replace(new RegExp('@latenightjimmy', 'gim'), '');
			}
			
			if($("#formatting input[name=includeusername]").attr('checked')){
				text='<a href="http://www.twitter.com/'+tweet['user']+'" class="username">'+tweet['user']+'</a> '+text;
			}
			
			if($("#formatting input[name=dofont]").attr('checked')){
				$("#tweets").addClass('dofont');
			} else {
				$("#tweets").removeClass('dofont');
			}
			
			text=text.replace(/([\s]{1,})/g, ' ');
			text=$.trim(text);
			
			$(".text", this).html(text);
			
		});
		
	},
	
	init:function(){
		
		$("#viewControls input").live("click", function(){
			var rating=$(this).attr('rating');
			$("#tweets, #viewControls").attr('rating', rating);
		});
		
		$("#formatting input, #formatting select").live("change", function(){
			LNJF.format();
		});
		
		LNJF.format();
		
	}
	
}

LNJF.init();