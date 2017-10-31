jQuery( document ).ready( function($) {
    
    $( 'a' ).click(function() {
       $( 'a.active' ).removeClass( 'active' );
       $(this).addClass( 'active' );
    });
    
    /* Smooth Scroll from CSS Tricks - specific to front page */
    /* @link: https://css-tricks.com/snippets/jquery/smooth-scrolling/ */
    $(function() {
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top-55
                    }, 1000);
                    return false;
                } else {
                    $('html,body').animate({
                        scrollTop: 0
                    }, 1000);
                    return false;
                }
            }
        });
    });
    
    /* Turn on active state on the correct link when scrolling */
    /* @link:  http://codetheory.in/change-active-state-links-sticky-navigation-scroll/ */
    var sections = $( 'section' ),
        nav = $( 'nav' ),
        nav_height = nav.outerHeight();
        
    $(window).on( 'scroll', function() {
       var cur_pos = $(this).scrollTop();
       
       sections.each(function() {
          var top = $(this).offset().top  - nav_height -15,
              bottom = top + $(this).outerHeight();
              
          if ( cur_pos >= top && cur_pos <= bottom ) {
              nav.find( 'a' ).removeClass( 'active' );
              sections.removeClass( 'active' );
              
              //$(this).addClass( 'active' );
              nav.find( 'a[href="#' + $(this).attr('id') + '"]').addClass( 'active' );
          }
       });
    });
    
    
   /* 
    * @link: http://codepen.io/micahgodbolt/pen/FgqLc
    * 
    * Thanks to CSS Tricks for pointing out this bit of jQuery
    * http://css-tricks.com/equal-height-blocks-in-rows/
    * It's been modified into a function called at page load and then each time the page is resized. One large modification was to remove the set height before each new calculation.
    */
    equalheight = function(container){

    var currentTallest = 0,
         currentRowStart = 0,
         rowDivs = new Array(),
         $el,
         topPosition = 0;
     $(container).each(function() {

       $el = $(this);
       $($el).height('auto')
       topPostion = $el.position().top;

       if (currentRowStart != topPostion) {
         for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
           rowDivs[currentDiv].height(currentTallest);
         }
         rowDivs.length = 0; // empty the array
         currentRowStart = topPostion;
         currentTallest = $el.height();
         rowDivs.push($el);
       } else {
         rowDivs.push($el);
         currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
      }
       for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
         rowDivs[currentDiv].height(currentTallest);
       }
     });
    }

    $(window).load(function() {
      equalheight('.equally');
    });


    $(window).resize(function(){
      equalheight('.equally');
    });

    
    
    /* Modify the width at which the Front Page nav compresses */
    /* @TODO: Fix this... */
    var main_menu = $(".main-navigation");
    var main_menu_container = main_menu.find('.menu-main-menu-container').first();
    var submenu = $('#menu-main-menu');

    main_menu.click(function() {
            if($(window).outerWidth() <= 1040) {
                    if(main_menu.hasClass("opened")) {
                            main_menu_container.animate({
                                    'height': 0
                            }, 500, function() {
                                    main_menu.removeClass("opened");
                            });
                    } else {
                            main_menu.addClass("opened");
                            var h = submenu.outerHeight();
                            main_menu_container.css('height', '0');
                            main_menu_container.animate({
                                    'height': h + "px"
                            }, 500);
                    }
            }
    });

    /* Slick Slider - Projects */
    $('.front-page-projects').slick({
        arrows: true,
        cssEase: 'ease',
        dots: true,
        infinite: true,
        responsive: [
            {
                breakpoint: 900,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: true
                }
            }
        ],
        slidesToShow: 4,
        slidesToScroll: 4,
   });
    
    /* Slick Slider - Rotating Services */
    $('.rotating-services-div').slick({
        adaptiveHeight: false,
        autoplay: true,
        autoplaySpeed: 6000,
        arrows: true,
        cssEase: 'ease',
        dots: false,
        infinite: true,
        pauseOnHover: true,
        slidesToShow: 2,
        slidesToScroll: 2,
        useCSS: true,
   });
   
       /* Slick Slider - Testimonial Quotes */
    $('.testimonial-quotes').slick({
        adaptiveHeight: true,
        autoplay: true,
        autoplaySpeed: 10000,
        arrows: false,
        cssEase: 'ease',
        customPaging : function(slider, i) {
            var thumb = $(slider.$slides[i]).data('thumb');
            return '<img class="thumb" src="'+thumb+'">';
        },
        dots: true,
        infinite: true,
        pauseOnHover: true,
        pauseOnDotsHover: true,
        slidesToShow: 1,
        slidesToScroll: 1,
   });
   
       /* Slick Slider - Clients */
    $('.clients-list').slick({
        arrows: false,
        dots: true,
        infinite: true,
        responsive: [
            {
                breakpoint: 900,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false,
                    arrows: true,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false,
                    arrows: true,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false,
                    arrows: true,
                }
            },
        ],
        slidesToShow: 6,
        slidesToScroll: 6,
   });
   
    
    $('.rotating-services-div').on('setPosition', function() {
        $(this).find('.slick-slide').height('auto');
        var slickTrack = $(this).find('.slick-track');
        var slickTrackHeight = $(slickTrack).height();
        $(this).find('.slick-slide').css('height', slickTrackHeight + 60 + 'px');
    });

});