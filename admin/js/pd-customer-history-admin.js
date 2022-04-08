(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    var $win = $(window);
    var $doc = $(document);

    $doc.ready(function () {
        var params = getUrlVars();
        if ( typeof params['user_id']  !== "undefined" && params['page'] == 'pd-woo-customer' ) {
            pdwchs_woo_open_admin_menu( 'toplevel_page_pd-woo-dashboard', 'pd-customers-menu' );
        }

        if( typeof( params['page'] ) !== "undefined" && params['page'] !== null && ( params['page'] == 'pd-woo-searches' || params['page'] == 'pd-woo-trash' ) ) {
            pdwchs_woo_open_admin_menu( 'toplevel_page_pd-woo-dashboard', 'pd-sessions-menu', 'pd-sessions-tab' );
        }

        if( typeof( params['page'] ) !== "undefined" && params['page'] !== null && ( params['page'] == 'pd-woo-search-stats' || params['page'] == 'pd-woo-stats-spent' ) ) {
            pdwchs_woo_open_admin_menu( 'toplevel_page_pd-woo-dashboard', 'pd-keyword-stats-menu', 'pd-stats-tab' );
        }

        if( typeof( params['pd_taxonomy'] ) !== "undefined" && params['pd_taxonomy'] !== null ) {
            pdwchs_woo_open_admin_menu( 'toplevel_page_pd-woo-dashboard', 'pd-keyword-stats-menu', 'pd-stats-tab' );
        }


        $('.pd-wcch-close').on('click', function() {
            $(this).parents('.pd-wcch-form-group').find('.pd-wcch-inpu-wrapper').hide();
            $(this).parents('.pd-wcch-form-group').find(".pd-wcch-select").prop('selectedIndex',0);
            $(this).parents('.pd-wcch-form-group').find('.pd-wcch-form-control').val('');
        });
        $('.pd-wcch-close-cus').on('click', function() {
            $(this).parents('.pd-wcch-form-group').find('.pd-wcch-inpu-wrapper').hide();
            $('.drp-buttons .cancelBtn').trigger('click');
        });
        $('.pd-wcch-select').on('change', function() {
            if(this.value.length > 0){
                $(this).parents('.pd-wcch-form-group').find('.pd-wcch-inpu-wrapper').show();
            }else{
				$(this).parents('.pd-wcch-form-group').find('.pd-wcch-inpu-wrapper').hide();
			}
        });

        $( window ).load(function() {
            $( ".pd-wcch-select" ).each(function() {
                var selectedCountry = $(this).children("option:selected").val();
                if(selectedCountry){
                    $(this).parents('.pd-wcch-form-group').find('.pd-wcch-inpu-wrapper').show();
                }
            });

            if ( $('#orders .pd-wcch-form-group #daterange').length > 0 ) {
                var daterange = $('#orders .pd-wcch-form-group #daterange').val();
                if(daterange){
                    $('#orders .pd-wcch-form-group .pd-wcch-close-cus-wrap').show();
                }
            }
        });

        $(function() {
            $('input#daterange').daterangepicker({
                autoUpdateInput: false,
                opens: 'center',
                maxDate: moment(),
                locale: {
                    cancelLabel: 'Clear'
                }
            },function() {
                $('input#daterange').parent('.pd-wcch-form-group').find('.pd-wcch-inpu-wrapper').show();
            });

            $('input#daterange').on('apply.daterangepicker', function(ev, picker) {
                $('input#start-date').val('');
                $('input#end-date').val('');
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                $('input#start-date').val(picker.startDate.format('YYYY-MM-DD') + ' 00:00:00');
                $('input#end-date').val(picker.endDate.format('YYYY-MM-DD') + ' 23:59:59');
                $('input#daterange').parent('.pd-wcch-form-group').find('.pd-wcch-inpu-wrapper').show();
            });

            $('input#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('input#start-date').val('');
                $('input#end-date').val('');
            });
        });

        //TOGGLE FONT AWESOME ON CLICK
        $('.toggle-section').click(function(){
            $(this).find('.dashicons').toggleClass('dashicons-arrow-up-alt2 dashicons-arrow-down-alt2');
            $(`#${ $(this).data("toggle-content") }`).stop().slideToggle();
        });
        $('.toggle-section').blur(function(){
            $(this).find('.dashicons').toggleClass('dashicons-arrow-up-alt2 dashicons-arrow-down-alt2');
            $(`#${ $(this).data("toggle-content") }`).stop().slideToggle();
        });

        $(".delete-custom-btn").on('click', function(e) {
            e.preventDefault();
            $('#daterange,.delete-custom-btn-confirm').removeClass('d-none');
            $(this).addClass('d-none');
        });

        $(".pd-toggle-btn").click(function(e){
            var target = $(this).attr("data-target");
            $(this).siblings(".pd-toggle-btn").removeClass("pd-active-btn");
            $(this).addClass("pd-active-btn");

            if(target=='pd-table-content'){
                $(this).parents(".pd-woo-panel").find(".pd-chart-content").hide();
            }else{
                $(this).parents(".pd-woo-panel").find(".pd-table-content").hide();
            }
            $(this).parents(".pd-woo-panel").find("."+target).show();

        });

        $('select#show-items').on('change', function() {
            if( this.value ){
                var currentUrl = window.location.href;
                var url = new URL(currentUrl);
                url.searchParams.set("show-items", this.value);
                var newUrl = url.href;
                window.location.href = newUrl;
            }
        });
    });

    $win.load(function () {
        $('.pd-woo-main-container:nth-child(even)').after('<div class="pd-woo-clearfix"></div>');
    });

})( jQuery );
/*
 *	Open Admin Menu
 */

function pdwchs_woo_open_admin_menu( menu_id = null, submenu_id = null, tab_id = null ) {
    jQuery( '#' + menu_id ).removeClass('wp-not-current-submenu').addClass('current wp-has-current-submenu');
    jQuery( '#' + menu_id + '>a' ).removeClass('wp-not-current-submenu').addClass('current wp-has-current-submenu');
    jQuery( '#' + menu_id + ' > ul.wp-submenu' ).css('position','static');
    jQuery( '#' + submenu_id ).closest('li').addClass('current');
    jQuery( '#' + tab_id ).addClass('nav-tab-active');
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}


