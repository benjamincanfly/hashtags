
	var HT={
		init:function(){
			
			$("#header #hashtag").live("click", function(){
				var newHashtag=prompt("What's this week's hashtag?");
				$.ajax("/ajax_setNewHashtag.php", {type:'post', data:"&hashtag="+newHashtag, success:function(response){
					window.location='/index.php';
				}});
			});
			
		}
	}
	
	$(document).ready(HT.init);