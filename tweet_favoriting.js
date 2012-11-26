function isScrolledIntoView(elem)
{
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).outerHeight();

	
	if ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) &&  (elemTop >= docViewTop)) { return 'completely'; }
	if((elemBottom >= docViewTop) && (elemTop <= docViewBottom)){ return 'partly'; }
	return 'not';
	
}

HT.fave={
	level:null,
	state: {
		highlighted:false
	},
	wrap:function(){
		$(".tweet").each(function(){

			var tweetID=$(this).attr('tweetid');

			var content="";
			var tweet=jsonTweets[tweetID];
			var text=tweet['tweet'];

			text=text.replace(new RegExp('(#'+hashtag+')([\s]{0,})', 'gim'), "<span class='hash'>\$1 </span>");
			text=text.replace(new RegExp('(@jimmyfallon)([\s]{0,})', 'gim'), "<span class='atjimmy'>\$1 </span>").replace(new RegExp('(@latenightjimmy)([\s]{0,})', 'gim'), "<span class='atjimmy'>\$1 </span>");
			text=replaceURLWithHTMLLinks(text);
			text='<a href="http://www.twitter.com/'+tweet['username']+'" class="username">'+tweet['username']+' </a> '+text;

			//console.info(text);
			$(this).html(text);
		});

	},
	setVisibleTweets:function(){
		$(".tweet").each(function(){
			$(this).attr('visible', isScrolledIntoView(this));
		});
	},
	ajaxCalls:{},
	highlight:function(id, scroll){
		console.info('highlight tweet: '+id);
		
		if(HT.fave.state.highlighted){
			$(".tweet[tweet-id='"+HT.fave.state.highlighted+"']").attr('tweet-state', '');
		}
		HT.fave.state.highlighted=id;
		$(".tweet[tweet-id='"+id+"']").attr('tweet-state', 'highlighted');
		
		if(scroll){
			var tweetTop=$(".tweet[tweet-id='"+id+"']:first").offset().top;
			$('body').animate({'scroll-top':tweetTop-200}, 100);
		}
	},
	keys:{
		up:function(){
			console.info('up');
			
			if(HT.fave.state.highlighted){
				var hiEl=$(".tweet[tweet-id='"+HT.fave.state.highlighted+"']");
				if($(hiEl).attr('visible')!='not'){
					var selectedEl=$(hiEl).prev('.tweet');
					if($(selectedEl).size()){
						HT.fave.highlight($(selectedEl).attr('tweet-id'), true);
					}
				} else {
					HT.fave.highlight($('.tweet[visible!=not]:first').attr('tweet-id'), true)
				}
			} else {	
				HT.fave.highlight($('.tweet[visible!=not]:first').attr('tweet-id'), true);
			}
			
		},
		down:function(){
			console.info('down');
			
			if(HT.fave.state.highlighted){
				var hiEl=$(".tweet[tweet-id='"+HT.fave.state.highlighted+"']");
				if($(hiEl).attr('visible')!='not'){
					var selectedEl=$(hiEl).next('.tweet');
					if($(selectedEl).size()){
						HT.fave.highlight($(selectedEl).attr('tweet-id'), true);
					}
				} else {
					HT.fave.highlight($('.tweet[visible!=not]:first').attr('tweet-id'), true)
				}
			} else {	
				HT.fave.highlight($('.tweet[visible!=not]:first').attr('tweet-id'), true);
			}
		},
		left:function(){
			console.info('left');
			
			if(HT.fave.state.highlighted){
				var hiEl=$(".tweet[tweet-id='"+HT.fave.state.highlighted+"']");
				if($(hiEl).attr('visible')!='not'){
					var tweet_rating=parseInt($(hiEl).attr("tweet-rating"))-1;
					if(tweet_rating<0){
						return false;
					}
					HT.fave.faveTweet($(hiEl).attr('tweet-id'), tweet_rating);
				}
			}
			
		},
		right:function(){
			console.info('right');
			
			if(HT.fave.state.highlighted){
				var hiEl=$(".tweet[tweet-id='"+HT.fave.state.highlighted+"']");
				if($(hiEl).attr('visible')!='not'){
					var tweet_rating=parseInt($(hiEl).attr("tweet-rating"))+1;
					if(HT.fave.level==1 && tweet_rating>2){tweet_rating=2;} else if (HT.fave.level==2 && tweet_rating>1){ tweet_rating=1; }
					HT.fave.faveTweet($(hiEl).attr('tweet-id'), tweet_rating);
				}
			}
		},
		
	},
	keypress:function(){
		
	},
	keydown:function(e){
		HT.fave.setVisibleTweets();
		switch(e.which){
			case 37:
			HT.fave.keys.left();
			return false;
			break;
			case 38:
			HT.fave.keys.up();
			return false;
			break;
			case 39:
			HT.fave.keys.right();
			return false;
			break;
			case 40:
			HT.fave.keys.down();
			return false;
			break;
		}
	},
	init:function(){
		console.info('init');
		
		HT.fave.level=$("body:first").attr('id').replace('tweets_','');
		
		$("select#page").live("change", function(){
			location.href='?page='+$(this).val();
		});
		
		$("select#whoiam").live("change", function(){
			$.ajax('/ajax_setUser.php', {data:'id='+$(this).val(),success:function(){window.location.reload(true);}});
		});
		
		$("select#whoiamfavoriting").live("change", function(){
			$.ajax('/ajax_setUserFavoriting.php', {data:'id='+$(this).val(),success:function(){window.location.reload(true);}});
		});
		
		$("#morebutton").live("click", HT.fave.getMore);
		
		$(document).live("keydown", HT.fave.keydown);
		$(document).live("keypress", HT.fave.keypress);
		$(".tweet").live("click", function(){
			var tweet_id=$(this).attr("tweet-id");
			var tweet_rating=parseInt($(this).attr("tweet-rating"))+1;
			if((HT.fave.level==1 && tweet_rating>2) || (HT.fave.level==2 && tweet_rating>1)){tweet_rating=0;}
			HT.fave.highlight(tweet_id, false);
		  	HT.fave.faveTweet(tweet_id, tweet_rating);
		});
		
		if(tweets && HT.fave.level < 3){ HT.fave.renderTweets(tweets); }
	},
	tweetCode:function(tweet){
		//console.info('tweetCode');
		var code='<div class="tweet" tweet-id="'+tweet['tweet_id']+'" tweet-rating="'+(HT.fave.level==1?tweet['rating_1']:tweet['rating_2'])+'">'+tweet['tweet']+'</div>';
		return code;
	},
	renderTweets:function(tweets){
		console.info('renderTweets');
		$('#tweets').html('');
		for(var i=0;i<tweets.length;i++){
			var tweetCode=HT.fave.tweetCode(tweets[i]);
			$('#tweets').append(tweetCode);
		}
		
	},
	
	faveTweet:function(id, rating){
		
		if(HT.fave.ajaxCalls["fave"+id]){
			HT.fave.ajaxCalls["fave"+id].abort();
		}
		
		HT.fave.ajaxCalls["fave"+id]=$.ajax("/ajax_rateTweet.php", {type:'post', data:"level="+HT.fave.level+"&id="+id+"&rating="+rating});
		
		console.info('fave id '+id+' to '+rating);
		
		HT.fave.tweetFaved(id, rating);
		
	},
	tweetFaved:function(id, rating){
		$(".tweet[tweet-id='"+id+"']").attr("tweet-rating", rating);
	},
	getMore:function(){
		console.info('getMore');
		$.ajax('/ajax_assignMoreTweets.php', {success:function(response){
			location.href='/tweets_1.php';
		}});
	}
};

$(document).ready(HT.fave.init);