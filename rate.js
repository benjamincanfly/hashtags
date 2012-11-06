
var LNJF={
	
	rate: function(tweetID, tweetRating){
		$.ajax("/ajaxRate.php", {type:"post", data:"tweet="+tweetID+"&rating="+tweetRating, error:function(thing){alert("Error!");}});
		$("#audio").html("<embed src='/audio/"+(tweetRating==3?'good':(tweetRating==2?'okay':'bad'))+".wav' autostart='true' loop='false'/>");
		
		var button=$(".tweet[tweetid="+tweetID+"] button[value='"+tweetRating+"']");
		
		var newButton=$('<div class="fakeButton fake'+tweetRating+'"></div>').appendTo('body').css({width:$(button).outerWidth(),height:$(button).outerHeight()}).offset($(button).offset()).animate({marginTop:-50,marginLeft:-50,width:+150,height:+150,opacity:0},250,function(){$(this).remove();});
		
		if(tweetRating>1){
			var newButton2=$('<div class="fakeButton fake'+tweetRating+'"></div>').appendTo('body').css({width:$(button).outerWidth(),height:$(button).outerHeight()}).offset($(button).offset()).animate({marginTop:-100,marginLeft:-100,width:+250,height:+250,opacity:0},1000,function(){$(this).remove();});
		}
	
		if(tweetRating>2){
			var newButton3=$('<div class="fakeButton fake'+tweetRating+'"></div>').appendTo('body').css({width:$(button).outerWidth(),height:$(button).outerHeight()}).offset($(button).offset()).animate({marginTop:-200,marginLeft:-200,width:+450,height:+450,opacity:0},750,function(){$(this).remove();});
			
		}
		
	},
	rated: function(tweetID, tweetRating){
		$(".tweet[tweetid="+tweetID+"]").attr("rating", tweetRating);
		LNJF.progress();
	},
	progress:function(){
		
		var percentComplete=Math.floor(($(".tweet").size()-$(".tweet[rating=0]").size())/$(".tweet").size()*100);
		
		$("#progress .percent").text(percentComplete);
		
		$("#progress .Ts").text($(".tweet[rating=3]").size());
		$("#progress .STs").text($(".tweet[rating=2]").size());
		
	},
	format:function(){
		$(".tweet").each(function(){
			
			var tweetID=$(this).attr('tweetid');
			
			var content="";
			var tweet=jsonTweets[tweetID];
			var text=tweet['text'];
			
			if($("#rateFormatting input[name=removestuff]").attr('checked')){
				text=text.replace(new RegExp('#'+hashtag, 'gim'), '');
				text=text.replace(new RegExp('@jimmyfallon', 'gim'), '').replace(new RegExp('@latenightjimmy', 'gim'), '');
			}
			
			text=text.replace(/([\s]{1,})/g, ' ');
			text=$.trim(text);
			
			$(".text", this).html(text);
			
		});
	},
	init:function(){
		
		$("#tweets .tweet button[name=rating]").live("click", function(){
			var tweetID=$(this).attr('tweetid');
			
			LNJF.rate(tweetID, $(this).val());
			LNJF.rated(tweetID, $(this).val());
		});
		
		
		$("#rateFormatting input").live("change", LNJF.format);
					
		LNJF.format();
		
		LNJF.progress();
	}
	
}

LNJF.init();