
var LNJF={
	timers:{},
	rate: function(tweetID, tweetRating){
		
		clearTimeout(LNJF.timers['checkRatings']);
		
		console.info('ajax_rateTweet');
		$.ajax("/ajax_rateTweet.php", {type:"post", data:"level=3&id="+tweetID+"&rating="+tweetRating, error:function(thing){alert("Error!");},success:function(){
		console.info('ajax_rateTweet finished');
			LNJF.timers['checkRatings']=setTimeout(LNJF.checkTweetRatings, 1000);
		}});
		
		var button=$(".tweet[tweet-id="+tweetID+"] button[value='"+tweetRating+"']");
		
		if($("input[name=fx]").attr('checked')){
			
			var newButton=$('<div class="fakeButton fake'+tweetRating+'"></div>').appendTo('body').css({width:$(button).outerWidth(),height:$(button).outerHeight()}).offset($(button).offset()).animate({marginTop:-50,marginLeft:-50,width:+150,height:+150,opacity:0},250,function(){$(this).remove();});
		
			if(tweetRating>1){
				var newButton2=$('<div class="fakeButton fake'+tweetRating+'"></div>').appendTo('body').css({width:$(button).outerWidth(),height:$(button).outerHeight()}).offset($(button).offset()).animate({marginTop:-100,marginLeft:-100,width:+250,height:+250,opacity:0},1000,function(){$(this).remove();});
			}
	
			if(tweetRating>2){
				var newButton3=$('<div class="fakeButton fake'+tweetRating+'"></div>').appendTo('body').css({width:$(button).outerWidth(),height:$(button).outerHeight()}).offset($(button).offset()).animate({marginTop:-200,marginLeft:-200,width:+450,height:+450,opacity:0},750,function(){$(this).remove();});
			
			}
		
		}
	},
	rated: function(tweetID, tweetRating){
		$(".tweet[tweet-id="+tweetID+"]").attr("tweet-rating", tweetRating);
		LNJF.progress();
	},
	updateRatings:function(tweetRatings){
		for(tweetID in tweetRatings){
			if(tweetRatings[tweetID]!=LNJF.tweets[tweetID]['rating_3']){
				LNJF.tweets[tweetID]['rating_3']=tweetRatings[tweetID];
				LNJF.rated(tweetID, tweetRatings[tweetID]);
			}
		}
	},
	sound: function(tweetRating){
		if($("input[name=fx]").attr('checked')){
			$("#audio").html("<embed src='/audio/"+(tweetRating==3?'good':(tweetRating==2?'okay':'bad'))+".wav' autostart='true' loop='false'/>");
		}
	},
	progress:function(){
		console.info('progress');
		var percentComplete=Math.floor(($(".tweet").size()-$(".tweet[tweet-rating='0']").size())/$(".tweet").size()*100);
		
		$("#progress .percent").text(percentComplete);
		
		$("#progress .Ts").text($(".tweet[tweet-rating='3']").size());
		$("#progress .STs").text($(".tweet[tweet-rating='2']").size());
		
	},
	checkTweetRatings:function(){
		$.ajax("/ajax_getRatings.php", {type:"post", dataType:'json', data:"hashtag="+LNJF.hashtag, success:function(data){
			LNJF.updateRatings(data);
			LNJF.timers['checkRatings']=setTimeout(LNJF.checkTweetRatings, 1000);
		}});
	},
	init:function(){
		
		console.info('tweets 3');
		
		LNJF.tweets=json_tweets;
		LNJF.hashtag=hashtag;
		
		$("#tweets .tweet button[name=rating]").live("click", function(){
			var tweetID=$(this).parents(".tweet:first").attr('tweet-id');
			
			LNJF.rate(tweetID, $(this).val());
			LNJF.rated(tweetID, $(this).val());
		});
		
		$("#tweets .tweet button[name=rating]").live("mousedown", function(){
			LNJF.sound($(this).val());
		});
		
		HT.fave.renderTweets();
		
		LNJF.checkTweetRatings();
		
		LNJF.progress();
		
	}
	
}

LNJF.init();