/***
 * AUTO-SOLID s.c. (c) 2018
 *
 * WebUI v1.1
 *
 */

'use strict';

var WebUI = {
    version: '1.1',
    debug: true,
    appUrl: 'https://autosolid.eu/',

    themeParams: {
        logoFile: 'logo-white.png',
        logoShrinkedFile: 'logo-color.png'
    },

    init: function () {
        if (typeof jQuery === 'undefined') {
            if(WebUI.debug) alert('Error: jQuery object undefined. Initialization stopped.');
            return false;
        }

        // Init events
        WebUI.Events.addListeners();
        WebUI.Events.onStart();

        // Init page elements
        WebUI.initHeader();
        WebUI.initNav();
        WebUI.initDropdowns();
        WebUI.initContactForm();

        // Init features
        WebUI.initSearch();
        WebUI.initCarousel();
        WebUI.initSlider();
        WebUI.initCountUp();
        WebUI.initEffects();
    },

    initHeader: function () {
        $('#header-logo').attr('src', WebUI.appUrl + 'public/img/' + WebUI.themeParams.logoFile);
    },

    initNav: function () {
        if (typeof $.fn.dropdownMenu === 'undefined') {
            if(WebUI.debug) WebUI.Helpers.log('Error: jQuery dropdownMenu() undefined. Nav initialization stopped.');
        } else {
            $('.consult-nav').dropdownMenu({
                menuClass: 'consult-menu',
                breakpoint: 992,
                toggleClass: 'active',
                classButtonToggle: 'navbar-toggle',
                subMenu: {
                    class: 'sub-menu',
                    parentClass: 'menu-item-has-children',
                    toggleClass: 'active'
                }
            });
        }

        $('.navbar-toggle').each(function(index, el) {
            $(el).on('click', function(event) {
                event.preventDefault();
                $(el).toggleClass('open');
            });

            $(document).on('click', function(event) {
                if ($.contains(el, event.target)) {
                    return;
                }

                if ($(el).hasClass('open')) {
                    $(el).removeClass('open');
                }
            });
        });
    },

    initSearch: function () {
        $('.search-form').each(function(index, el) {
            $(el).on('click', function(event) {
                event.preventDefault();
                $(el).addClass('open');
            });

            $(document).on('click', function(event) {
                if ($.contains(el, event.target)) {
                    return;
                }
                if ($(el).hasClass('open')) {
                    $(el).removeClass('open');
                }
            });
        });

        $('#searchValue').on('keyup', function (event) {
            var text = $('#searchValue').val();

            if(text.length > 2 && event.key === 'Enter') {
                WebUI.Helpers.highlight(text);
            } else {
                WebUI.Helpers.clearHighlight();
            }
        });
    },

    initCarousel: function () {
        if (typeof $.fn.owlCarousel === 'undefined') {
            if(WebUI.debug) WebUI.Helpers.log('Error: jQuery owlCarousel() undefined. Carousel initialization stopped.');
            return false;
        }

        $('.carousel-element').each(function() {
            var self = $(this),
                optData = eval('(' + self.attr('data-options') + ')'),
                optDefault = {
                    items: 1,
                    nav: true,
                    dot: true,
                    loop: true,
                    autoplay: true,
                    autoplayTimeout: 10000
                },
                options = $.extend(optDefault, optData);

            self.owlCarousel(options);
        });
    },

    initSlider: function () {
        var windowWidth    = $(window).innerWidth();
        var containerWidth = $('.container').width();
        var outerPadding   = (windowWidth - containerWidth)/2;

        if(windowWidth > 1200) {
            $('.js-consult-slider').css('marginRight', - outerPadding);
        }
    },

    initCountUp: function () {
        if (typeof $.fn.countUp === 'undefined') {
            if(WebUI.debug) WebUI.Helpers.log('Error: jQuery countUp() undefined. CountUp initialization stopped.');
            return false;
        }

        $('.js-counter').countUp();
    },

    initDropdowns: function () {
        $('[data-init="dropdown"]').each(function(index, el) {

            $(el).find('a.dropdown-toggle').on('click', function(event) {
                event.preventDefault();
                $(el).find('.dropdown-content').toggleClass('open');
                $(el).toggleClass('open');
            });

            $(document).on('click', function(event) {
                var $content = $(el).find('.dropdown-content');
                if ($.contains(el, event.target)) {
                    return;
                }

                if ($(el).hasClass('open')) {
                    $(el).removeClass('open');
                }

                if ($content.hasClass('open')) {
                    $content.removeClass('open');
                }
            });
        });
    },

    initContactForm: function () {
        var formElement = $('.js-consult-form .js-consult-form-content');
        var _hForm = formElement.outerHeight() / 2;
        var _paddingTop = _hForm + 50;

        if(windowWidth > 1200) {
            _paddingTop = _hForm + 200;
        }

        formElement.css('bottom', - _hForm);

        if(windowWidth > 768) {
            $('.js-consult-form + *').css('padding-top', _paddingTop);
        }
    },

    initEffects: function () {
        $('.js-post-effect').each(function() {
            var contentHeight = $(this).find('.image-content-content').height() + 30;

            if(windowWidth > 768) {
                contentHeight = $(this).find('.image-content-content').height() + 50;
            }

            $(this).find('.image-content-body').css('transform', 'translateY(' + contentHeight + 'px)');

            $(this).hover(function() {
                $(this).find('.image-content-body').css('transform', 'translateY(' + 0 + 'px)');
            }, function() {
                $(this).find('.image-content-body').css('transform', 'translateY(' + contentHeight + 'px)');
            });
        });
    },

    initScroll: function () {
        $('a[href^="#"].clickScroll').on('click',function (e) {
            e.preventDefault();

            var target = this.hash;
            var $target = $(target);

            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 900, 'swing', function() {
                window.location.hash = target;
            });
        });

        $('#back-to-top').on('click', function (e) {
            e.preventDefault();
            $('html,body').animate({
                scrollTop: 0
            }, 700);
        });

        if (typeof $.fn.scroll === 'undefined') {
            if(WebUI.debug) WebUI.Helpers.log('Error: jQuery scroll() undefined. Scroll initialization stopped.');
            return false;
        }
        if (typeof $.fn.scrollTop === 'undefined') {
            if(WebUI.debug) WebUI.Helpers.log('Error: jQuery scrollTop() undefined. Scroll initialization stopped.');
            return false;
        }

        $(window).scroll(function() {
            WebUI.Helpers.setThemeParams();

            if ($(document).scrollTop() > 100) {
                $('.header').addClass('shrink');
                $('#header-logo').attr('src', WebUI.appUrl + 'public/img/' + WebUI.themeParams.logoShrinkedFile);
            } else {
                $('.header').removeClass('shrink');
                $('#header-logo').attr('src', WebUI.appUrl + 'public/img/' + WebUI.themeParams.logoFile);
            }
        });
    },

    scrollTo: function (target) {
        var $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 900, 'swing', function() {
            //window.location.hash = target;
        });
    },

    // -- Events --

    Events: {
        addListeners: function () {
            $(document).ready(function(){
                WebUI.Events.onLoad();
            });

            window.addEventListener('resize', WebUI.Events.onResize);
        },

        onStart: function () {
            WebUI.Helpers.setThemeParams();

            window.isMobile = WebUI.Helpers.isMobile;
            window.isIE = WebUI.Helpers.isIE();
            window.windowHeight = window.innerHeight;
            window.windowWidth = window.innerWidth;

            $('.row-eq-height > [class*="col-"]').matchHeight();

            WebUI.Helpers.log('WebUI v' + WebUI.version + ' started. Have a nice day :)');
        },

        onLoad: function () {
            WebUI.initScroll();

            $('.window-height').css('max-height', window.innerHeight - 100 + 'px');
        },

        onResize: function () {

            WebUI.Helpers.debounce(function() {
                $('.row-eq-height > [class*="col-"]').matchHeight();
            }, 250);

            WebUI.Helpers.debounce(function() {
                WebUI.initContactForm();
            }, 250);

            WebUI.Helpers.debounce(function() {
                WebUI.initSlider();
            }, 250);
        }
    },

    // -- Helpers --

    Helpers: {
        debounce: function (func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        isMobile: {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        },

        isIE: function () {
            return /(MSIE|Trident\/|Edge\/)/i.test(navigator.userAgent);
        },

        setThemeParams: function () {
            if($('#header-logo').hasClass('page-start')) {
                WebUI.themeParams.logoFile = 'logo-white.png';
                WebUI.themeParams.logoShrinkedFile = 'logo-color.png';
            } else {
                WebUI.themeParams.logoFile = 'logo-color.png';
                WebUI.themeParams.logoShrinkedFile = 'logo-color.png';
            }
        },

        redirect: function (url)
        {
            if( !url ) url = '';

            document.location = url;
        },

        log: function (data) {
            if (typeof console === 'undefined') return false;

            console.log(data);
        },

        highlight: function (text) {
            if (typeof $.fn.mark === 'undefined') {
                if(WebUI.debug) alert('Error: jQuery mark.js plugin object undefined. Operation canceled.');
                return false;
            }

            $('body').mark(text);
        },

        clearHighlight: function () {
            if (typeof $.fn.mark === 'undefined') {
                if(WebUI.debug) alert('Error: jQuery mark.js plugin object undefined. Operation canceled.');
                return false;
            }

            $('body').unmark();
        }
    }
};

WebUI.init();