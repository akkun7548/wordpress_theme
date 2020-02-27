/**
 * slick.jsの設定
 */
jQuery( function() {
    jQuery('.slider-thumb').slick( {
        infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
        asNavFor: '.slider-nav',
        lazyLoad: 'progressive',
    } );
    jQuery('.slider-nav').slick( {
        centerMode: true,
        infinite: true,
		slidesToShow: 4,
		slidesToScroll: 1,
		asNavFor: '.slider-thumb',
        focusOnSelect: true,
        variableWidth: true,
    } );
} );

/**
 * slickスライダーとヘッダーが重複した時に透明にする。
 */
jQuery( function() {
    var height = jQuery('.widget_slider').height();
    if( height ) {
        var navbar = jQuery('.navbar');
        var dropdown_menu = jQuery('.dropdown-menu');
        var dropdown_toggle = jQuery('.dropdown-toggle');
        jQuery('main').addClass('with-slider');
        navbar.addClass('transparent');
        dropdown_menu.addClass('transparent');
        dropdown_toggle.addClass('transparent');
        jQuery(window).scroll( function() {
            var scroll = jQuery(this).scrollTop();
            if( scroll < height ) {
                navbar.addClass('transparent');
                dropdown_menu.addClass('transparent');
                dropdown_toggle.addClass('transparent');
            } else {
                navbar.removeClass('transparent');
                dropdown_menu.removeClass('transparent');
                dropdown_toggle.removeClass('transparent');
            }
        } );
    }
} );

/**
 * 親セレクタ代わり
 */
jQuery( function() {
    jQuery('.navbar-dark .navbar-nav li:has(li.active)').addClass('active');
} );

/**
 * stickyサイドバーの位置調整
 */
jQuery( function() {
    jQuery('.sticky').css('top', jQuery('.header').height() + jQuery('#wpadminbar').height() + 10 );
} );

/**
 * adminbar位置とヘッダーの重複対策
 */
jQuery( function() {
    if( jQuery('#wpadminbar').css('position') == 'absolute' ) {
        var height = jQuery('#wpadminbar').height();
        jQuery(window).scroll( function() {
            var scroll = jQuery(this).scrollTop();
            var scroll_switched = ( height > scroll ? height - scroll : 0 );
            jQuery('.header').css('top', scroll_switched);
        } );
    }
} );
