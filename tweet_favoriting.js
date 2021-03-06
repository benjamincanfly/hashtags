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
	timeouts:{},
	state: {
		highlighted:false
	},
	replaceURLWithHTMLLinks:function(text) {
	    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
	    return text.replace(exp,"<a href='$1' target=_NEW>$1</a>"); 
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
		console.info(e.which);
		switch(e.which){
			case 37:
			case 100:
			HT.fave.keys.left();
			return false;
			break;
			case 38:
			case 104:
			HT.fave.keys.up();
			return false;
			break;
			case 39:
			case 102:
			HT.fave.keys.right();
			return false;
			break;
			case 40:
			case 98:
			HT.fave.keys.down();
			return false;
			break;
		}
	},
	jumpStart:function(){
		console.info('search.php ...');
		$.ajax('/search.php', {data:'ajax=true', dataType:'json', success:function(data){
			console.info('search.php finished');
			
			if(data['status']=='ok' && data['count']>0){
				console.info('found '+data['count']+' new tweets');
				if(unclaimedTweetCount<100){
					window.location.reload(true);
				}
			} else if(data['status']=='ok' && data['count']==0){
				console.info('found zero new tweets');
			} else if(data['status']=='error') {
				alert(data['error']);
				console.info(data['thing']);
			} else if(data['status']=='busy'){
				console.info('Busy. Waiting five seconds ...');
				HT.fave.timeouts['ajaxWait']=setTimeout(HT.fave.jumpStart, 5000);
			}
		}});
	},
	theme:function(){
		if($("#night").attr('checked')){
			$('body').addClass('night');
		} else {
			$('body').removeClass('night');
		}
		
		$.ajax('/ajax_setSessionVar.php', {type:'post', data:'night='+($("#night").attr('checked')?'yes':'no')});
	
	},
	init:function(){
		console.info('tweet_favoriting init');
		
		HT.tweets=typeof(json_tweets)!='undefined'?json_tweets:null;
		HT.hashtag=typeof(hashtag)!='undefined'?hashtag:null;
		
		HT.fave.level=$("body:first").attr('id').replace('tweets_','');
		
		if(HT.fave.level==1 && typeof(unclaimedTweetCount)!='undefined' && unclaimedTweetCount<=500){
			//HT.fave.jumpStart();
		}
		
		if(HT.fave.level==1){
			$("select#page").live("change", function(){
				location.href='?page='+$(this).val();
			});
			$("#morebutton").live("click", HT.fave.getMore);
			
			$("#night").live("click", HT.fave.theme);
		}
		
		if(HT.fave.level==1 || HT.fave.level==2){
			
			$("select#whoiam").live("change", function(){
				$.ajax('/ajax_setUser.php', {data:'id='+$(this).val(),success:function(){window.location.reload(true);}});
			});
			
			$(document).live("keydown", HT.fave.keydown);
			$(document).live("keypress", HT.fave.keypress);
			$(".tweet").live("click", function(){
				var tweet_id=$(this).attr("tweet-id");
				var tweet_rating=parseInt($(this).attr("tweet-rating"))+1;
				if((HT.fave.level==1 && tweet_rating>2) || (HT.fave.level==2 && tweet_rating>1)){tweet_rating=0;}
				HT.fave.highlight(tweet_id, false);
			  	HT.fave.faveTweet(tweet_id, tweet_rating);
			});
		
		}
		
		if(HT.fave.level==2){
			$("select#whoiamfavoriting").live("change", function(){
				$.ajax('/ajax_setUserFavoriting.php', {data:'id='+$(this).val(),success:function(){window.location.reload(true);}});
			});
		}
		
		if(HT.fave.level<4){
			$("#removestuff").live("change", HT.fave.reformat);
		}
		
		if(HT.tweets!=null){
			HT.fave.renderTweets(HT.tweets);
			//HT.fave.wrap();
		}
	},
	reformat: function(){
		if($("#removestuff").attr('checked')){
			$('#tweets').removeAttr('showusername showhash showatjimmy');
		} else {	$('#tweets').attr('showusername','yes').attr('showhash','yes').attr('showatjimmy','yes');
		}
	},
	tweetCode:function(tweet){
		var extraCode='';
		
		if(HT.fave.level==3){
			extraCode+='<div class="controls"><button name="rating" value="1">S</button><button name="rating" value="2">S/T</button><button name="rating" value="3">T</button></div>';
		}
		
		var text=tweet['tweet']?tweet['tweet']:'';
		
		text=text.replace(new RegExp('(#'+hashtag+')([\s]{0,})', 'gim'), "<span class='hash'>\$1 </span>");
		text=text.replace(new RegExp('(@jimmyfallon)([\s]{0,})', 'gim'), "<span class='atjimmy'>\$1 </span>").replace(new RegExp('(@jimmyfallon)([\s]{0,})', 'gim'), "<span class='atjimmy'>\$1 </span>");
		
		text=text.replace(/([\s]{1,})/g, ' ');
		text=$.trim(text);
		
		text=HT.fave.replaceURLWithHTMLLinks(text);
		text='<a href="http://www.twitter.com/'+tweet['username']+'" class="username">'+tweet['username']+' </a> '+text;
		
		var code='<div class="tweet" tweet-id="'+tweet['tweet_id']+'" tweet-rating="'+(HT.fave.level==1?tweet['rating_1']:(HT.fave.level==2?tweet['rating_2']:tweet['rating_3']))+'">'+text+extraCode+'</div>';
		
		return code;
	},
	renderTweets:function(tweets){
		console.info('renderTweets');
		$('#tweets').html('');
		for(tweet in tweets){
			var tweetCode=HT.fave.tweetCode(tweets[tweet]);
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
		
		HT.state.clickable=false;
		$("#morebutton").val('Retrieving tweets ...')
		
		$.ajax('/ajax_assignMoreTweets.php', {success:function(response){
			location.href='/tweets_1.php?gotMore=true';
		}});
	}
};

$(document).ready(HT.fave.init);