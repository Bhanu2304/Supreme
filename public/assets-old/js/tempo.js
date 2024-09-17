// JavaScript Document

/* ==========================================================================
   NOTE:
   This file is being used to activate and/or set options for all jQuery plugins
   for this template. Please don't modify this file unless necessary. This will 
   make it easy for you to upgrade your website with new template files easily 
   when new version of this template will be available. Use main.js for your 
   custom JavaScript/jQuery codeing instead.
   
   --------------------------------------------------------------------------
	TABLE OF CONTENT
   --------------------------------------------------------------------------
	1. preloader animation
	2. tooltip settings
	3. Horizontal Tab
	4. Vertical Tab
	5. iCheck settings
	6. custom select list
	7. spinedit (spinner form element)
	8. wow.js animations on scroll
	9. menu dropdown effect
	10. custom navigation
	11. custom scrollbar
	12. offcanvas menu
	14. Initializing portfolio slider
	15. Initializing creative slider
	16. Initializing corporate slider
	17. Initializing construction slider
	18. Nivo Lightbox
	19. full screen header
	20. Initializing blog slider
	21. animated progress-bar
	22. PiE Charts	
	23. play button
	24. responsive video
	25. section background using 'data-path'
	26. a. portfolio 4cols (juicy projects)
		b. portfolio 3cols (juicy projects)
		c. portfolio 2cols (juicy projects)
		d. portfolio infinite-scroll (juicy projects)
		e. portfolio 4cols (lightbox gallery)
		f. portfolio 3cols (lightbox gallery)
		g. portfolio 2cols (lightbox gallery)
		h. portfolio infinite-scroll (lightbox gallery)
		i. portfolio 4cols (minimal)
		j. portfolio 3cols (minimal)
		k. portfolio 2cols (minimal)
		l. portfolio infinite-scroll (minimal)
		m. portfolio 4cols (creative)
		n. portfolio 3cols (creative)
		o. portfolio 2cols (creative)
		p. portfolio infinite-scroll (creative)
		q. portfolio 4cols (masonry)
		r. portfolio 3cols (masonry)
		s. portfolio 2cols (masonry)
		t. portfolio infinite-scroll (masonry)
		u. portfolio 4cols (masonry-alt)
		v. portfolio 3cols (masonry-alt)
		w. portfolio 2cols (masonry-alt)
		x. portfolio infinite-scroll (masonry-alt)
		y. portfolio 4cols (awesome-work)
		z. portfolio 3cols (awesome-work)
		aa. portfolio 2cols (awesome-work)
		ab. portfolio infinite-scroll (awesome-work)
		ac. portfolio 4cols (agency)
		ad. portfolio 3cols (agency)
		ae. portfolio 2cols (agency)
		af. portfolio infinite-scroll (agency)
	27. carousel (juicy projects)
	28. bouncing arrow (page-header)
	29. featured posts slider (blog1)
	30. featured projects carousel (blog aside widget)
	31. flickr feed
	32. Click effects
	33. twitter feed
	34.a. blog masonry 4col
	   b. blog masonry 3col
	   c. blog masonry 2col
	   d. blog masonry infinite scroll
	35. similar posts carousel
	36.a. shop 4cols
	   b. shop 3cols
	   c. shop 2cols
	   d. shop infinite scroll
	37. shop related products
	38. product gallery
	39. image gallery1
	40. image gallery2
	41. cubeportfolio fallback
	42. thumbs carousel (images slideshow)
	43. offer carousel (shop-style1 header small thumbnail carousel)
	44. clients carousel (with logos)
	45. services carousel (featured services)
	46. shop2 slider
	47. testimonials style1
	48. fun facts (counter)
	49. single page navigation settings
	50. campaign monitor subscription
	51. mailchimp settings
	52. faq sortable
	53. team members (carousel)
	54. simple tabs
	55. juicy projects mosaic
	56. mosaic fullwidth projects
	57. Initializing shop one slider
	58. countdown timer
   ========================================================================== */

// window dot load START
$(window).load(function() {
	"use strict";
		
	// 1. preloader animation
    $('.spinner-wrap').fadeOut();
	$('.preloader').delay(500).fadeOut('slow');	
	
	// 2. tooltip settings
	//for complete documentation ( http://getbootstrap.com/javascript/#tooltips )
	$('[data-toggle="tooltip"]').tooltip();
	
	// 3. Horizontal Tab
	// for complete documentation ( https://github.com/samsono/Easy-Responsive-Tabs-to-Accordion )
	$('#horizontalTab1, #horizontalTab2').easyResponsiveTabs({
		type: 'default', //Types: default, vertical, accordion
		width: 'auto', //auto or any width like 600px
		fit: true, // 100% fit in a container
		tabidentify: 'hor_1', // The tab groups identifier
		activate: function(event) { // Callback function if tab is switched
			var $tab = $(this);
			var $info = $('#nested-tabInfo');
			var $name = $('span', $info);
			$name.text($tab.text());
			$info.show();
		}
	});

	// 4. Vertical Tab
	// for complete documentation ( https://github.com/samsono/Easy-Responsive-Tabs-to-Accordion )
	$('#verticalTab').easyResponsiveTabs({
		type: 'vertical', //Types: default, vertical, accordion
		width: 'auto', //auto or any width like 600px
		fit: true, // 100% fit in a container
		closed: 'accordion', // Start closed if in accordion view
		tabidentify: 'hor_1', // The tab groups identifier
		activate: function(event) { // Callback function if tab is switched
			var $tab = $(this);
			var $info = $('#nested-tabInfo2');
			var $name = $('span', $info);
			$name.text($tab.text());
			$info.show();
		}
	});
		
	// 5. iCheck settings (to style the checkboxes and redio buttons)
	// for complete documentation ( https://github.com/fronteed/iCheck/ )
	$('input').iCheck({
		checkboxClass: 'icheckbox_square',
		radioClass: 'iradio_square',
		increaseArea: '20%' // optional
	});	
	
	// 6. custom select list
	// for complete documentation ( https://github.com/Brankub/selectik )
	$('.select').selectik({
		minScrollHeight: 20
	});	
	
	// 7. spinedit (spinner form element)
	// for complete documentation ( https://github.com/geersch/bootstrap-spinedit )
	$('.spin').spinedit({
		minimum: -10000,
		maximum: 10000,
		step: 1,
		value: 1,
		numberOfDecimals: 0
	});	
	
	// 8. wow.js animations on scroll
	// for complete documentation ( http://mynameismatthieu.com/WOW/docs.html )
	new WOW().init({
		boxClass: 'wow',
		animateClass: 'animated',
		offset: 90,
		mobile: false, 
		live: true 
	});	
	
	// 9. menu dropdown effect
	$('.dropdown-toggle').dropdownHover().dropdown();
	$(document).on('click', '.mega-menu .dropdown-menu', function(e) {
		e.stopPropagation()
	})	
	
	// 10. custom navigation
	// to collapse the navbar on scroll
        
        
	$(function(){
		if ($(".navbar").offset().top > 70) {
			$(".navbar-fixed-top").addClass("top-nav-collapse");
                        $(".navbar-fixed-top").addClass("ac");
			$('.transparent-nav .navbar-brand img').attr('src','./assets/img/logos/maslogo.png');
		} else {
			$(".navbar-fixed-top").removeClass("top-nav-collapse");
                        $(".navbar-fixed-top").removeClass("ac");
			$('.transparent-nav .navbar-brand img').attr('src','./assets/img/logos/maslogo.png');
		}
	});
        
        
	$(window).scroll(function() {
		if ($(".navbar").offset().top > 70) {
			$(".navbar-fixed-top").addClass("top-nav-collapse");
                        $(".navbar-fixed-top").addClass("ac");
			$('.transparent-nav .navbar-brand img').attr('src','./assets/img/logos/maslogo.png');
		} else {
			$(".navbar-fixed-top").removeClass("top-nav-collapse");
                        $(".navbar-fixed-top").removeClass("ac");
			$('.transparent-nav .navbar-brand img').attr('src','./assets/img/logos/maslogo.png');
		}
	});	
	
	// 11. custom scrollbar
	$(".search-cart .dropdown.cart .dropdown-menu .cart-wrap, .nav-items, .icon-box.style4").mCustomScrollbar({
		scrollButtons:{enable:false},
		theme:"minimal"
	});
	
	// 12. offcanvas menu
	$('.vertical-nav .nav-items .dropdown > a').on('click', function(){
		$(this).parent().toggleClass('open');
		$(this).next().slideToggle();
	});
  	$('[data-toggle="offcanvas"]').on('click', function () {
    	$('.content.offcanvas, .vertical-nav, .cloud-container').toggleClass('active');
	});
		
	// 14. Initializing portfolio slider
	$('#portfolioSlider').cloudSlider({
		width: 1200,
		height: 700,
		fullSize: true,
		onHoverPause: false,
		video: {
			autoPlay: false
		}
	});
		
	// 15. Initializing creative slider
	$('#creativeSlider').cloudSlider({
		width: 1200,
		height: 700,
		fullSize: true,
		onHoverPause: false,
		video: {
			autoPlay: true
		}
	});
		
	// 15. Initializing agency slider
	$('#agencySlider').cloudSlider({
		width: 1200,
		height: 700,
		fullSize: false,
		fullWidth: true,
		onHoverPause: false,
		video: {
			autoPlay: true
		}
	});
		
	// 16. Initializing corporate slider
	$('#corporateSlider').cloudSlider({
		width: 1200,
		height: 630,
		fullWidth: true,
		onHoverPause: false,
		video: {
			autoPlay: true
		}
	});
		
	// 17. Initializing construction slider
	$('#constructionSlider').cloudSlider({
		width: 1200,
		height: 630,
		fullWidth: true,
		onHoverPause: false,
		video: {
			autoPlay: true
		}
	});
	
	// 18. Nivo Lightbox
	$('.nivo-lightbox').nivoLightbox({
		effect: 'slideUp',                             // The effect to use when showing the lightbox
		theme: 'default',                             // The lightbox theme to use
		keyboardNav: true,                             // Enable/Disable keyboard navigation (left/right/escape)
		clickOverlayToClose: true,                    // If false clicking the "close" button will be the only way to close the lightbox
		onInit: function(){},                         // Callback when lightbox has loaded
		beforeShowLightbox: function(){},             // Callback before the lightbox is shown
		afterShowLightbox: function(lightbox){},     // Callback after the lightbox is shown
		beforeHideLightbox: function(){},             // Callback before the lightbox is hidden
		afterHideLightbox: function(){},             // Callback after the lightbox is hidden
		onPrev: function(element){},                 // Callback when the lightbox gallery goes to previous item
		onNext: function(element){},                 // Callback when the lightbox gallery goes to next item
		errorMessage: 'The requested content cannot be loaded. Please try again later.' // Error message when content can't be loaded
	});
	
	// 19. full screen header
	function fullScreenHeader() {
		var screenHeight = $(window).height();
		$(".full-screen").css('min-height',screenHeight); 
	}	
	$(document).ready(function() {
		fullScreenHeader();
		$(window).bind('resize', fullScreenHeader);
	});
		
	// 20. Initializing blog slider
	$('#blogSlider').cloudSlider({
		width: 1200,
		height: 500,
		fullSize: false,
		fullWidth: true,
		onHoverPause: false,
		video: {
			autoPlay: true
		}
	});
	
	// 21. animated progress-bar
	$('.progress-bar').appear(function(){
		var pBar = $(this);
		pBar.each(function(indx){
			$(this).css("width", $(this).attr("aria-valuenow") + "%");
		});
	});
	
	// 22. PiE Charts
	// docs: http://rendro.github.io/easy-pie-chart/
	$('.chart').appear(function(){
		$(this).easyPieChart({
			barColor: '#333',
			trackColor: 'rgba(230, 230, 230, 0.4)',
			scaleColor: false,
			lineCap: 'round',
			easing: 'easeOutBounce',
			rotate: -90,
			lineWidth: 6,
			size: 110,
			animate: 2000,
			onStep: function(from, to, percent) {
				$(this.el).find('.percent').text(Math.round(percent));
			}
		});
	});
	
	// 23. play button
	$('.play-btn, .hover-effect').hover(
		function(){$(this).addClass('cbutton--click');},
		function(){$(this).removeClass('cbutton--click');}
	);
	
	// 24. responsive video
	$(".responsive-video").fitVids();
		
	// 25. section background using 'data-path'
	$( ".thumbnail.style2 .img-box, .page-title, .featured-post, .section, .section-bg, .login-page" ).each(function() {
		var attr = $(this).attr('data-path');
		if (typeof attr !== typeof undefined && attr !== false) {
			$(this).css('background', 'url('+attr+')');
	  	}
	});
	
	// 26. portfolio section
	// 26.a. portfolio 4cols (juicy projects)
    $('.juicy-projects-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomReveal',
        displayType: 'sequentially',
        displayTypeSpeed: 80,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.b. portfolio 3cols (juicy projects)
    $('.juicy-projects-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'bounceTop',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomReveal',
        displayType: 'sequentially',
        displayTypeSpeed: 80,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.c. portfolio 2cols (juicy projects)
    $('.juicy-projects-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 3
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomReveal',
        displayType: 'sequentially',
        displayTypeSpeed: 80,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.d. portfolio infinite-scroll (juicy projects)
    $('.juicy-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomReveal',
        displayType: 'sequentially',
        displayTypeSpeed: 80,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.e. portfolio 4cols (lightbox-gallery)
	$('.lightbox-gallery-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.f. portfolio 3cols (lightbox-gallery)
	$('.lightbox-gallery-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'fadeOut',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.g. portfolio 2cols (lightbox-gallery)
	$('.lightbox-gallery-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 3
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.h. portfolio infinite-scroll (lightbox-gallery)
	$('.lightbox-gallery-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.i. portfolio 4cols (minimal)
    $('.minimal-4col').cubeportfolio({
        filters: '#filters-container',
		loadMore: '#loadMore-container',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        gapHorizontal: 50,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'fadeIn',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.j. portfolio 3cols (minimal)
    $('.minimal-3col').cubeportfolio({
        filters: '#filters-container',
		loadMore: '#loadMore-container',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        gapHorizontal: 50,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'fadeIn',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.k. portfolio 2cols (minimal)
    $('.minimal-2col').cubeportfolio({
        filters: '#filters-container',
		loadMore: '#loadMore-container',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        gapHorizontal: 50,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 3
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'fadeIn',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.l. portfolio infinite-scroll (minimal)
    $('.minimal-infinite').cubeportfolio({
        filters: '#filters-container',
		loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        gapHorizontal: 50,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'fadeIn',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.m. portfolio 4cols (creative)
    $('.creative-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'fadeOutTop',
        gapHorizontal: 0,
        gapVertical: 0,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.n. portfolio 3cols (creative)
    $('.creative-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'fadeOutTop',
        gapHorizontal: 0,
        gapVertical: 0,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.o. portfolio 2cols (creative)
    $('.creative-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'fadeOutTop',
        gapHorizontal: 0,
        gapVertical: 0,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 3
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.p. portfolio infinite-scroll (creative)
    $('.creative-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'fadeOutTop',
        gapHorizontal: 0,
        gapVertical: 0,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.q. portfolio 4col (masonry)
    $('.masonry-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 25,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.r. portfolio 3col (masonry)
    $('.masonry-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 25,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.s. portfolio 2col (masonry)
    $('.masonry-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 25,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.t. portfolio infinite-scroll (masonry)
    $('.masonry-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 25,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.u. portfolio 4col (masonry-alt)
    $('.masonry-alt-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomAlong',
        displayType: 'bottomToTop',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.v. portfolio 3col (masonry-alt)
    $('.masonry-alt-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomAlong',
        displayType: 'bottomToTop',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.w. portfolio 2col (masonry-alt)
    $('.masonry-alt-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomAlong',
        displayType: 'bottomToTop',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.x. portfolio infinite-scroll (masonry-alt)
    $('.masonry-alt-infinite-scroll').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomAlong',
        displayType: 'bottomToTop',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 26.y. portfolio 4col (awesome-work)
    $('.awesome-work-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'scaleSides',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 400,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.z. portfolio 3col (awesome-work)
    $('.awesome-work-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'scaleSides',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 400,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.aa. portfolio 2col (awesome-work)
    $('.awesome-work-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'scaleSides',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 400,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.ab. portfolio infinite-scroll (awesome-work)
    $('.awesome-work-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'scaleSides',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 400,
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 26.ac. portfolio 4col (agency)
    $('.agency-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideLeft',
        gapHorizontal: 35,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100
    });
	// 26.ac. portfolio 3col (agency)
    $('.agency-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideLeft',
        gapHorizontal: 35,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100
    });
	// 26.ac. portfolio 2col (agency)
    $('.agency-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideLeft',
        gapHorizontal: 35,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 3
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100
    });
	// 26.ac. portfolio infinite-scroll (agency)
    $('.agency-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'slideLeft',
        gapHorizontal: 35,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1400,
            cols: 5
        }, {
            width: 1100,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100
    });	
	
	// 27. carousel (juicy projects)
    $('.juicy-projects-carousel').cubeportfolio({
        layoutMode: 'slider',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        showPagination: false,
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomReveal',
        displayType: 'sequentially',
        displayTypeSpeed: 80,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        }
    });	
	
	// 28. bouncing arrow (page-header)
	jQuery('.bounce.arrow').on('click', function(){	
		var el = jQuery(this).attr('href');
		var elWrapped = jQuery(el);		
		scrollToDiv(elWrapped,40);
		return false;	
	});
	function scrollToDiv(element,navheight){	
		var offset = element.offset();
		var offsetTop = offset.top;
		var totalScroll = offsetTop-navheight;
		
		jQuery('body,html').animate({
				scrollTop: totalScroll
		}, 500);
	}	
	
	// 29. featured posts slider (blog1)	
    $('.featured-slider').cubeportfolio({
        layoutMode: 'slider',
        drag: true,
        auto: true,
        autoTimeout: 6000,
        autoPauseOnHover: true,
        showNavigation: false,
        showPagination: false,
        rewindNav: true,
        scrollByPage: false,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 2
        }, {
            width: 480,
            cols: 1
        }],
        gapHorizontal: 0,
        gapVertical: 0,
        caption: '',
        displayType: 'lazyLoading',
        displayTypeSpeed: 400,
    });	
	
	// 30. featured projects carousel (blog aside widget)	
    $('.featured-projects').cubeportfolio({
        layoutMode: 'slider',
        drag: true,
        auto: true,
        autoTimeout: 6000,
        autoPauseOnHover: true,
        showNavigation: false,
        showPagination: true,
        rewindNav: true,
        scrollByPage: false,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 380,
            cols: 1
        }],
        gapHorizontal: 0,
        gapVertical: 0,
        caption: '',
        displayType: 'lazyLoading',
        displayTypeSpeed: 400,
    });	
	
	// 31. flickr feed
	$('.flickr-feed').jflickrfeed({
		limit: 12,
		qstrings: {
			id: '52617155@N08'
		},
		itemTemplate: '<li><a href="{{image_b}}"><img src="{{image_s}}" alt="{{title}}" /></a></li>'
	});
	
	// 32. Click effects
	!function(){function i(){var i=!1;return function(n){(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(n)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(n.substr(0,4)))&&(i=!0)}(navigator.userAgent||navigator.vendor||window.opera),i}var n={animations:Modernizr.cssanimations},t={WebkitAnimation:"webkitAnimationEnd",OAnimation:"oAnimationEnd",msAnimation:"MSAnimationEnd",animation:"animationend"},a=t[Modernizr.prefixed("animation")],o=function(i,t){var o=function(i){if(n.animations){if(i.target!=this)return;this.removeEventListener(a,o)}t&&"function"==typeof t&&t.call()};n.animations?i.addEventListener(a,o):o()},e=i()?"touchstart":"click";[].slice.call(document.querySelectorAll(".cbutton")).forEach(function(i){i.addEventListener(e,function(){classie.add(i,"cbutton--click"),o(classie.has(i,"cbutton--complex")?i.querySelector(".cbutton__helper"):i,function(){classie.remove(i,"cbutton--click")})})})}();
	
	// 33. twitter feed
	$('#tweets').tweecool({
		username : 'oscodoco',
		limit:3,
		profile_image : true,
		show_time : true,
		show_media : false,
		show_media_size: 'thumb',  //values: small, large, thumb, medium 
		callback: function() {
			$('#tweets').find('ul').addClass('tweet-slider');
			$('.tweet-slider').owlCarousel({
				items:1
			});			
		}
	});
	
	// 34. masonry blog
	// 34.a. blog masonry 4col
	$('.blog-masonry-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        search: '#js-search-blog-posts',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'fadeOut',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 34.b. blog masonry 3col
	$('.blog-masonry-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        search: '#js-search-blog-posts',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'fadeOut',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 34.c. blog masonry 2col
	$('.blog-masonry-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        search: '#js-search-blog-posts',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'fadeOut',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });
	// 34.d. blog masonry infinite scroll
	$('.blog-masonry-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        search: '#js-search-blog-posts',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'fadeOut',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>'
    });	
	
	// 35. similar posts carousel
	$('.similar-posts-carousel').cubeportfolio({
        layoutMode: 'slider',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'fadeOut',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
    });
	
	// 36.a. shop 4cols
	$('.shop-4col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 36.b. shop 3cols
	$('.shop-3col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 36.c. shop 2cols
	$('.shop-2col').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 3
        }, {
            width: 1000,
            cols: 2
        }, {
            width: 800,
            cols: 2
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });
	// 36.d. shop infinite scroll
	$('.shop-infinite').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'auto',
        layoutMode: 'grid',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePageInline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'below',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePageInline content use the following method: this.updateSinglePageInline(yourContent)
            var t = this;
            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 10000
                })
                .done(function(result) {
                    t.updateSinglePageInline(result);
                })
                .fail(function() {
                    t.updateSinglePageInline('AJAX Error! Please refresh the page!');
                });
        }
    });	
	
	// 37. shop related products
	$('.related-products').cubeportfolio({
        layoutMode: 'slider',
        mediaQueries: [{
            width: 1600,
            cols: 5
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        caption: 'overlayBottomPush',
        displayType: 'sequentially',
        displayTypeSpeed: 100,
		showPagination: false
    });
	
	// 38. product gallery
	$(".product-gallery").cloudSlider({
		width: 1100,
		height: 1200,
		skin: 'polygon',
		navType: 'thumb',
		thumb: {
			visibility: 'show',
			positionOffset: '-10px',
			containerBgColor: '#fff',
			itemBorderColor: '#fff',
			itemOpacity: 0.6,
			activatedBorderColor: '#000',
			itemMargin: '0px',
			itemSize: {
				width: 202,
				height: 123
			}
		}
	});
	
	// 39. image gallery1
    $('.gallery1').cubeportfolio({
        filters: '#filters-container',
		loadMore: '#loadMore-container',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1600,
            cols: 4
        }, {
            width: 1000,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'fadeIn',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100
    });
	
	// 40. image gallery2
	$(".gallery2").cloudSlider({
		width: 1140,
		height: 650,
		navType: 'thumb',
		thumb: {
			visibility: 'show',
			positionOffset: '-10px',
			containerBgColor: '#fff',
			itemBorderColor: '#fff',
			itemOpacity: 0.6,
			activatedBorderColor: '#000',
			itemMargin: '0px',
			itemSize: {
				width: 202,
				height: 123
			}
		}
	});	
	
	// 41. cubeportfolio fallback
	$(".cbp").on('pluginResize.cbp', function() {
		$(".livicon").addLivicon();
        $(".responsive-video").fitVids();
		$('[data-toggle="tooltip"]').tooltip();
		$('.nivo-lightbox').nivoLightbox();
    });	
	
	// 42. thumbs carousel (images slideshow)
	$('.thumbs-carousel').owlCarousel({
		loop:true,
		margin:0,
		nav:true,
		items:1,
		autoplay:true,
		autoplayHoverPause:true,
		animateOut:'fadeOut',
		animateIn:'fadeIn',
		dots: false
	});	
	
	// 43. offer carousel (shop-style1 header small thumbnail carousel)
	$('.offer-carousel').owlCarousel({
		loop:true,
		margin:0,
		nav:false,
		dots:false,
		items:1,
		autoplay:true,
		autoplayHoverPause:true,
		animateOut:'fadeOut',
		animateIn:'fadeIn'
	});	

    // 44. clients carousel (with logos)
    $('.our-clients').cubeportfolio({
        layoutMode: 'slider',
        drag: true,
        auto: true,
        autoTimeout: 3000,
        autoPauseOnHover: true,
        showNavigation: false,
        showPagination: false,
        rewindNav: true,
        scrollByPage: false,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1100,
            cols: 6
        }, {
            width: 800,
            cols: 5
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        gapHorizontal: 0,
        gapVertical: 40,
        caption: 'opacity',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
    });	

    // 45. services carousel (featured services)
    $('.our-services').cubeportfolio({
        layoutMode: 'slider',
        drag: true,
        auto: true,
        autoTimeout: 3000,
        autoPauseOnHover: true,
        showNavigation: false,
        showPagination: false,
        rewindNav: true,
        scrollByPage: false,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1100,
            cols: 3
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        gapHorizontal: 0,
        gapVertical: 30,
        caption: '',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
    });	
	
	// 46. shop2 slider
	$("#shop-slider").cloudSlider({
		width: 1280,
		height: 620,
		fullSize: false,
		fullWidth: true,
		progressBarPosition: 'hide',
		navType: 'thumb',
		autoSlideCarousel: 'combine',
		arrow: {
			visibility: 'hide'
		},
		thumb: {
			visibility: 'show',
			itemMargin: '3px',
			itemOpacity: 0.4,
			itemBorderWidth: '1px',
			itemBorderColor: '#aaa',
			containerBgColor: 'transparent',
			activatedBorderColor: '#333'
		},
		camera_carousel_option: {
			overlay: '-120%', // Overlay for each level - {%}, {px}
			duration: 700, // Flow duration
			delay: 4000, // Flow Delay
			arrow: {
				visibility: 'hover', // hide carousel arrow
				position: '20px'
			},
			itemSize: {
				width: 400
			},
			level: [{ // Level 2
				scale: 0.5, // scale
				opacity: 0.2 // opacity
			}, { // Level 2
				scale: 0.1, // scale
				opacity: 0.2 // opacity
			}]
		},
		rings_carousel_option: {
			overlay: '-200%', // Overlay for each level - {%}, {px}
			duration: 700, // Flow duration
			delay: 6000, // Flow Delay
			direction: 'left', // Flow Direction
			arrow: {
				visibility: 'hover', // hide carousel arrow
				position: '20px'
			},
			itemSize: {
				width: 300
			},
			level: [{ // Level 2
				scale: 0.5, // scale
				opacity: 0.2 // opacity
			}, { // Level 2
				scale: 0.1, // scale
				opacity: 0.2 // opacity
			}]
		},
		tshirt_carousel_option: {
			overlay: '-200%', // Overlay for each level - {%}, {px}
			duration: 700, // Flow duration
			delay: 5000, // Flow Delay
			arrow: {
				visibility: 'hover', // hide carousel arrow
				position: '20px'
			},
			itemSize: {
				width: 300
			},
			level: [{ // Level 2
				scale: 0.5, // scale
				opacity: 0.2 // opacity
			}, { // Level 2
				scale: 0.1, // scale
				opacity: 0.2 // opacity
			}]
		}
	});	
	
	// 47. testimonials style1
    $('.testimonials-style1').cubeportfolio({
        layoutMode: 'slider',
        drag: true,
        auto: true,
        autoTimeout: 5000,
        autoPauseOnHover: true,
        showNavigation: false,
        showPagination: true,
        rewindNav: false,
        scrollByPage: false,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1,
            cols: 1
        }],
        gapHorizontal: 0,
        gapVertical: 0,
        caption: '',
        displayType: 'default'
    });	
	
	// 48. fun facts (counter)
	$(".fun-counter").appear(function(){
		var count = $(this);
		count.countTo({
			from: 0,
			to: count.html(),
			speed: 1300,
			refreshInterval: 5
		});		
	});	
	
	// 49. single page navigation settings
	$(".navbar .nav a").smoothScroll({speed: 800});	
	
	// 50. campaign monitor subscription
	// form settings	
	$(function () {
		$('#campaign-monitor').on('submit', function (e) {
			e.preventDefault();
			$.getJSON(
			this.action + "?callback=?",
			$(this).serialize(),
			function (data) {
				if (data.Status === 400) {
					alert("Error: " + data.Message);
				} else { // 200
					alert("Success: " + data.Message);
				}
			});
		});
	});
	
	// 51. mailchimp settings	
	$('#mailchimp').ajaxChimp({
		callback: mailchimpCallback,
		url: "http://oscodo.us9.list-manage.com/subscribe/post?u=aef5e76b30521b771cf866464&amp;id=f9f9e8db45"
		//replace above url with your own mailchimp post url inside the "".
		//to learn how to get your own URL, please check documentation file.
	});	
	function mailchimpCallback(resp) {
		 if (resp.result === 'success') {
			$('#mailchimp .subscription-success').html('<i class="icon_check_alt2"></i>' + resp.msg).fadeIn(1000);
			$('#mailchimp .subscription-error').fadeOut(500);
			
		} else if(resp.result === 'error') {
			$('#mailchimp .subscription-success').fadeOut(500);
			$('#mailchimp .subscription-error').html('<i class="icon_close_alt2"></i>' + resp.msg).fadeIn(1000);
		}  
	}
	
	// 52. faq sortable
    $('#js-grid-faq').cubeportfolio({
        filters: '#js-filters-faq',
        defaultFilter: '*',
        animationType: 'sequentially',
        gridAdjustment: 'default',
        displayType: 'default',
        caption: 'expand',
        gapHorizontal: 0,
        gapVertical: 0
    });
	
	// 53. team members (carousel)
    $('#js-grid-slider-team').cubeportfolio({
        layoutMode: 'slider',
        drag: true,
        auto: false,
        autoTimeout: 5000,
        autoPauseOnHover: true,
        showNavigation: false,
        showPagination: true,
        rewindNav: true,
        scrollByPage: true,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1680,
            cols: 5
        }, {
            width: 1350,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 480,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        gapHorizontal: 0,
        gapVertical: 45,
        caption: '',
        displayType: 'lazyLoading',
        displayTypeSpeed: 400,
    });
	
	// 54. simple tabs
    $('#js-grid-tabs').cubeportfolio({
        filters: '#js-filters-tabs',
        defaultFilter: '.about',
        animationType: 'fadeOutTop',
        gridAdjustment: 'default',
        displayType: 'default',
        caption: '',
    });
	
	// 55. juicy projects mosaic
    $('#js-grid-mosaic-projects').cubeportfolio({
        filters: '#js-filters-mosaic-projects1,#js-filters-mosaic-projects2',
		loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'mosaic',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 35,
        gapVertical: 30,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1500,
            cols: 5
        }, {
            width: 1100,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 480,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'sequentially',
        displayTypeSpeed: 80,

        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
    });
	
	// 56. mosaic fullwidth projects
    $('#js-grid-full-width').cubeportfolio({
        filters: '#js-filters-full-width',
        loadMore: '#js-loadMore-full-width',
        loadMoreAction: 'auto',
        layoutMode: 'mosaic',
        sortToPreventGaps: true,
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 0,
        gapVertical: 0,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1500,
            cols: 5
        }, {
            width: 1100,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 480,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,

        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
    });
	
	// 57. Initializing shop one slider
	$('#shopOneSlider').cloudSlider({
		width: 750,
		height: 450,
		fullSize: false,
		fullWidth: false,
		onHoverPause: false,
		video: {
			autoPlay: true
		}
	});
	
	// 58. countdown timer	
	//countdown timer
	//for detailed documentation visit: https://github.com/rendro/countdown/
	$('.countdown').countdown({
	  render: function(data) {
		$(this.el).html("<div>" + this.leadingZeros(data.days, 3) + " <span>days</span></div><div>" + this.leadingZeros(data.hours, 2) + " <span>hrs</span></div><div>" + this.leadingZeros(data.min, 2) + " <span>min</span></div><div>" + this.leadingZeros(data.sec, 2) + " <span>sec</span></div>");
	  }
	});
	
});
// window dot load END