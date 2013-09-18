
	var HT={
		state:{
			clickable:true
		},
		click:function(event){
			console.info('click');
			if(!HT.state.clickable){
				event.preventDefault();
				console.info("false");
				return false;
			}
		},
		init:function(){
			
			$("input[type=button]#refresh").live("click", function(){
				
				$.ajax("/ajax_refreshTweetChecker.php", {type:'post', success:function(response){
					alert("Tweet Checker reset. Reload the page.");
				}});
				
			});
			
			$(document).live("click, mousedown", HT.click);
			
			/*
			$("#header #hashtag").live("click", function(){
				var newHashtag=prompt("What's this week's hashtag?");
				$.ajax("/ajax_setNewHashtag.php", {type:'post', data:"&hashtag="+newHashtag, success:function(response){
					window.location='/index.php';
				}});
			});
			*/
			
		}
	}
	
	$(document).ready(HT.init);