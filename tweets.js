var LNJF={
	fave:{
		ajaxCalls:{},
		keydown:function(e){
		
		},
		init:function(){
			$(document).live("keydown", LNJF.fave.keydown);
			$(".tweet").live("click", function(){
			var tweet_id=$(this).attr("tweet");
			var tweet_rating=$(this).attr("rating")+1;
			$(this).attr("rating", tweet_rating);
		  LNJF.fave.faveTweet(tweet_id, tweet_rating);
		}
		
	},

		faveTweet:function(id, rating){

			LNJF.fave.ajaxCalls["fave"+id].cancel();

	LNJF.fave.ajaxCalls["fave"+id]=$.ajax("/faveTweet.php", {data:"id="+id+"&rating="+rating});

			LNJF.fave.tweetFaved(id, rating);

		},
		tweetFaved:function(id, rating){

			$(".tweet[tweet="+id+"]").attr("rating", rating);

		},
		getMore:function(){



		},

	}

}

$.ready(LNJF.fave.init);