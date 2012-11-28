function replaceURLWithHTMLLinks(text) {
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(exp,"<a href='$1' target=_NEW>$1</a>"); 
}

var LNJF={
	
	format:function(){
		
		HT.fave.quickFormat();
		LNJF.slowFormat();
			
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
								$(this).attr('color', 'green');
							} else {
								$(this).attr('color', 'red');
							}
						} else if (divideBy==3){
							if(i>=(numOfThese*(2/3))){
								$(this).attr('color', 'blue');
							} else if(i>=(numOfThese/3)) {
								$(this).attr('color', 'green');
							} else {
								$(this).attr('color', 'red');
							}
							
						} else if (divideBy==4){
							if(i>=numOfThese*.75){
								$(this).attr('color', 'yellow');
							} else if(i>=numOfThese/2) {
								$(this).attr('color', 'blue');
							} else if(i>=numOfThese/4) {
								$(this).attr('color', 'green');
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
	init:function(){
		
		HT.fave.wrap();
		
		$("#viewControls input").live("click", function(){
			var rating=$(this).attr('rating');
			$("#tweets, #viewControls").attr('rating', rating);
		});
		
		$("#formatting input[name=includehash], #formatting input[name=includejimmy], #formatting input[name=includeusername], #formatting input[name=dofont]").live("change", HT.fave.quickFormat);
		
		$("#formatting input[name=shownumbers], #formatting select").live("change", function(){
			LNJF.slowFormat();
		});
		
		LNJF.format();
		
	}
	
}

$(document).ready(LNJF.init);