// JavaScript Document
$(document).ready(function() {
	var autoPlayTime=7000;
	autoPlayTimer = setInterval( autoPlay, autoPlayTime);
	function autoPlay(){
		Slidebox('next');
	}
	$('#moveItem .next').click(function () {
		Slidebox('next','stop');
	});
	$('#moveItem .previous').click(function () {
		Slidebox('previous','stop');
	});
	var yPosition=($('#moveItem').height()-$('#moveItem .next').height())/2;
	$('#moveItem .next').css('top',yPosition);
	$('#moveItem .previous').css('top',yPosition);
	$('#moveItem .thumbs a:first-child').removeClass('thumb').addClass('selected_thumb');
	$("#moveItem .content").each(function(i){
		moveItemTotalContent=i*$('#moveItem').width();	
		$('#moveItem .container').css("width",moveItemTotalContent+$('#moveItem').width());
	});
});

function Slidebox(slideTo,autoPlay){
    var animSpeed=1000; //animation speed
    var easeType='easeInOutExpo'; //easing type
	var sliderWidth=$('#moveItem').width();
	var leftPosition=$('#moveItem .container').css("left").replace("px", "");
	if( !$("#moveItem .container").is(":animated")){
		if(slideTo=='next'){ //next
			if(autoPlay=='stop'){
				clearInterval(autoPlayTimer);
			}
			if(leftPosition==-moveItemTotalContent){
				$('#moveItem .container').animate({left: 0}, animSpeed, easeType); //reset
				$('#moveItem .thumbs a:first-child').removeClass('thumb').addClass('selected_thumb');
				$('#moveItem .thumbs a:last-child').removeClass('selected_thumb').addClass('thumb');
			} else {
				$('#moveItem .container').animate({left: '-='+sliderWidth}, animSpeed, easeType); //next
				$('#moveItem .thumbs .selected_thumb').next().removeClass('thumb').addClass('selected_thumb');
				$('#moveItem .thumbs .selected_thumb').prev().removeClass('selected_thumb').addClass('thumb');
			}
		} else if(slideTo=='previous'){ //previous
			if(autoPlay=='stop'){
				clearInterval(autoPlayTimer);
			}
			if(leftPosition=='0'){
				$('#moveItem .container').animate({left: '-'+moveItemTotalContent}, animSpeed, easeType); //reset
				$('#moveItem .thumbs a:last-child').removeClass('thumb').addClass('selected_thumb');
				$('#moveItem .thumbs a:first-child').removeClass('selected_thumb').addClass('thumb');
			} else {
				$('#moveItem .container').animate({left: '+='+sliderWidth}, animSpeed, easeType); //previous
				$('#moveItem .thumbs .selected_thumb').prev().removeClass('thumb').addClass('selected_thumb');
				$('#moveItem .thumbs .selected_thumb').next().removeClass('selected_thumb').addClass('thumb');
			}
		} else {
			var slide2=(slideTo-1)*sliderWidth;
			if(leftPosition!=-slide2){
				clearInterval(autoPlayTimer);
				$('#moveItem .container').animate({left: -slide2}, animSpeed, easeType); //go to number
				$('#moveItem .thumbs .selected_thumb').removeClass('selected_thumb').addClass('thumb');
				var selThumb=$('#moveItem .thumbs a').eq((slideTo-1));
				selThumb.removeClass('thumb').addClass('selected_thumb');
			}
		}
	}
}