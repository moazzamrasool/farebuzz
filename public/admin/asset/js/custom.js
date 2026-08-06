$(document).ready(function () {

 $("#vertical-menu .plus-icon").click(function () {
    $("#vertical-menu ul ul").slideUp();
    $('.plus',this).html('+');
    
    if (!$(this).next().is(":visible")) {
        $(this).next().slideDown();
          $('.plus').html('+');
        $('.plus',this).html('-');
    }
});


 $(window).width() > 991 && $(window).scroll(function() {
        var e = $(".navbar-light");
        $(window).scrollTop() >= 50 ? e.addClass("fixed") : e.removeClass("fixed")
    }),
    
  
 
 $('span.navbar-toggler-icon').click(function() {
        $(this).toggleClass('cross');
    });


	$('#nav li a').click(function(){
       $('#nav').removeClass('show');
    })  
    

$('.product-nav ul a').click(function(){
    $('.product-nav ul li a').addClass("active");
    //$(this).addClass("active");
  });
  


    
 $('.get-btn a').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
        && location.hostname == this.hostname) {
          var $target = $(this.hash);
          $target = $target.length && $target
          || $('[name=' + this.hash.slice(1) +']');
          if ($target.length) {
            var targetOffset = $target.offset().top -70;
            $('html,body')
            .animate({scrollTop: targetOffset}, 1000);
           return false;
          }
        }
      });
 



$('.hero-slider').slick({
  infinite: true,
  dots:true,
  slidesToShow:1,
  slidesToScroll: 1,
  autoplay:true,
  autoplaySpeed:2500,
  easing: 'easeOutElastic',
  speed:2500,
  arrows: false,
  /*prevArrow: '<span class="product-showcase-carousel-controls product-showcase-carousel-controls--left"><img src="images/left-arrow.svg"></span>',
  nextArrow: '<span class="product-showcase-carousel-controls product-showcase-carousel-controls--right"><img src="images/right-arrow.svg"></span>',*/

});


$('.service-slider').slick({
  infinite: true,
  dots:false,
  slidesToShow:3,
  slidesToScroll: 1,
  autoplay:false,
  false:true,
  easing: 'easeOutElastic',
  speed:2000,
  arrows: true,
  prevArrow: '<span class="product-showcase-carousel-controls product-showcase-carousel-controls--left"><img src="images/left-arrow.svg"></span>',
  nextArrow: '<span class="product-showcase-carousel-controls product-showcase-carousel-controls--right"><img src="images/right-arrow.svg"></span>',
  responsive: [
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 560,
      settings: {
        slidesToShow: 1,
        
      }
    }
    ]
});





$('.testimonials-slider').slick({
    dots: false,
    infinite: true,
    loop:true,
    speed: 1e3,
    slidesToShow: 1,
    slidesToScroll: 1,
    pauseOnHover: false,
    autoplay: true,
    nextArrow:false,
    prevArrow:false,
    
}); 

$('.slide-left').click(function(){
$('.testimonials-slider').slick('slickPrev');
});

$('.slide-right').click(function(){
$('.testimonials-slider').slick('slickNext');
});


 


$(function() {
	var e = window.location.href;
/about-us.php/.test(e) && (
		$("body").addClass("navigation_black")),
/contact-us.php/.test(e) && (
		$("body").addClass("navigation_black"))		
		
		

		
		

});

 $("input.type-hide").on('change', function() {
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        $('span.file-name').text(filename);
    });  
  

    
wow = new WOW({
    boxClass: "wow", // default
    animateClass: "animated", // default
    offset: 0, // default
    mobile: true, // default
    live: true, // default
});
wow.init();

if(localStorage.getItem('#myModal3')!=='true')
	{
	    
		 $('#myModal3').modal('show');
		localStorage.setItem('#myModal3',true);
	}
    
});

function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode < 48 || charCode > 57) {
            return false;
        }
        return true;
    }

