
HT.rate={
	timers:{},
	rate: function(tweetID, tweetRating){
		
		clearTimeout(HT.rate.timers['checkRatings']);
		
		console.info('ajax_rateTweet');
		$.ajax("/ajax_rateTweet.php", {type:"post", data:"level=3&id="+tweetID+"&rating="+tweetRating, error:function(thing){alert("Error!");},success:function(){
		console.info('ajax_rateTweet finished');
			HT.rate.timers['checkRatings']=setTimeout(HT.rate.checkTweetRatings, 1000);
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
		HT.rate.progress();
	},
	updateRatings:function(tweetRatings){
		for(tweetID in tweetRatings){
			if(tweetRatings[tweetID]!=HT.tweets[tweetID]['rating_3']){
				HT.tweets[tweetID]['rating_3']=tweetRatings[tweetID];
				HT.rate.rated(tweetID, tweetRatings[tweetID]);
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
		$.ajax("/ajax_getRatings.php", {type:"post", dataType:'json', data:"hashtag="+HT.hashtag, success:function(data){
			HT.rate.updateRatings(data);
			HT.rate.timers['checkRatings']=setTimeout(HT.rate.checkTweetRatings, 1000);
		}});
	},
	init:function(){
		
		console.info('tweets_3 init');
		
		$("#tweets .tweet button[name=rating]").live("click", function(){
			var tweetID=$(this).parents(".tweet:first").attr('tweet-id');
			
			HT.rate.rate(tweetID, $(this).val());
			HT.rate.rated(tweetID, $(this).val());
		});
		
		$("#tweets .tweet button[name=rating]").live("mousedown", function(){
			HT.rate.sound($(this).val());
		});
		
		//HT.fave.renderTweets();
		
		HT.rate.checkTweetRatings();
		
		HT.rate.progress();
		
	}
	
}

$(document).ready(HT.rate.init);
