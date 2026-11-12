// const cursor = document.querySelector('.cursor-circle');
// window.addEventListener('mousemove', (e) => {
//     cursor.style.left = e.clientX + 'px';
//     cursor.style.top = e.clientY + 'px';
// });
// const hoverTargets = document.querySelectorAll('a, button, [data-cursor-hover]');
// hoverTargets.forEach(el => {
//     el.addEventListener('mouseenter', () => {
//         cursor.classList.add('cursor-circle--hover');
//     });
//     el.addEventListener('mouseleave', () => {
//         cursor.classList.remove('cursor-circle--hover');
//     });
// });
// 
$(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.to-top').fadeIn().css('display', 'flex'); 
        } else {
            $('.to-top').fadeOut(); 
        }
    });

    $('.to-top').click(function(e){
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0        }, 800);
    });

$('.filter-btn').on('click', function (e) {
    e.preventDefault();
	 $(this).toggleClass('active');
    $('.filter-section').toggleClass('active');
    
});
$('.product-page__category a').on('click', function (e) {
    e.preventDefault();
    $('.product-page__category a').removeClass('active');
    $(this).addClass('active');
    $('input[name="Flavours"]').val($(this).text());
	$('.product-page__action span.error').removeClass('active');
	$('.btn-product').removeClass('is-disabled')
});
$('.btn-product').on('click', function (e) {
    e.preventDefault();
    $('input[name="Flavours"]').val($('.product-page__category a.active').text())
    $('input[name="product"]').val($('.product-page__title-main').text())
});
$('.hero-slider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 4000,
    infinite: true,
    dots: true,
    appendDots : $('.hero-dots'),
    prevArrow: $('.hero-prev'),
    nextArrow: $('.hero-next'),
});
$('.hero-slider').on('beforeChange', function(event, slick, current, next){
    $('.slick-dots li button').removeClass('progress');
});

$('.hero-slider').on('afterChange', function(){
    $('.slick-dots li.slick-active button').addClass('progress');
});
setTimeout(() => {
    $('.hero-dots li.slick-active button').addClass('progress');
}, 50);
$('.reviews-slider').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: false,
    infinite: false,
    // dots: false,
    // appendDots : $('.hero-dots'),
    prevArrow: $('.reviews-prev'),
    nextArrow: $('.reviews-next'),
    responsive: [
        {
            breakpoint: 769,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            }
        },
    ]
});
$('.faq-item').on('click', function (e){
	e.preventDefault();
    $(this).toggleClass('active');
})
$('.sort-section__title').on('click', function (e){
	e.preventDefault();
	$(this).toggleClass('active');
    $(this).next().toggleClass('active');
});
$('.lang-title').on('click', function (e){
	e.preventDefault();
    $(this).next().toggleClass('active');
})
$(document).mouseup( function(e){
    var divLang = $( ".header-action__lang" );
    if ( !divLang.is(e.target)
        && divLang.has(e.target).length === 0 ) {
        $('.lang-list').removeClass('active');
    }
});
$('.sort-section__list li').on('click', function (e){
	e.preventDefault();
    $('.sort-section__title').html($(this).text()).removeClass('active');
    $('.sort-section__list').removeClass('active');
})
$('.search-btn').on('click', function (e){
	e.preventDefault();
    $('.search-form').addClass('active');
})
// $('.search-form-btn').on('click', function (e){
//     $('.search-form').removeClass('active');
// })
$('.search-form-close').on('click', function (e){
    $('.search-form').removeClass('active');
})
$(document).mouseup( function(e){
    var div = $( ".sort-section" );
    if ( !div.is(e.target)
        && div.has(e.target).length === 0 ) {
        $('.sort-section__list').removeClass('active');
		$('.sort-section__title').removeClass('active');
    }
});
$(document).mouseup( function(e){
    var div = $( ".search-form" );
    if ( !div.is(e.target)
        && div.has(e.target).length === 0 ) {
        $('.search-form, .search-form__list').removeClass('active');
    }
});
document.addEventListener('DOMContentLoaded', function () {
    // Беремо всі форми
    const forms = document.querySelectorAll('.contact-form');

    forms.forEach(function (form) {
        const phoneInput = form.querySelector('.phone');
        const errorEl = form.querySelector('.phone-error'); // span/div під інпутом

        if (!phoneInput) return; // якщо раптом нема - пропускаємо

        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "gb",
            preferredCountries: ["gb", "de"],
            separateDialCode: true,
            autoPlaceholder: "polite",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"
        });

        function setError(msg) {
            if (errorEl) {
                errorEl.textContent = msg || "";
            }
            if (msg) {
                phoneInput.classList.add('is-invalid');
            } else {
                phoneInput.classList.remove('is-invalid');
            }
        }

        // Валідація на сабміт конкретно цієї форми
        // form.addEventListener('submit', function (e) {
        //     if (!phoneInput.value.trim() || !iti.isValidNumber()) {
        //         e.preventDefault();
        //         setError('Invalid format');
        //         return;
        //     }
        //     setError('');
        //     const fullNumber = iti.getNumber();
        //     // тут далі робиш, що треба з fullNumber
        //     // form.submit(); // якщо тобі треба форснути
        // });
    });
});

function popupOpen() {
    $(document).on('click', '.btn-popup', function (e) {
        e.preventDefault();
        var $this = $(this);
        if ($this.is(':disabled') || $this.hasClass('is-disabled') || $this.attr('aria-disabled') === 'true') {
			$('.product-page__action span.error').addClass('active');
            return;
        }
        var popupButtonData = $this.data('popup'); 
        $('.header-btn').removeClass('active');
        $('.header').removeClass('active');
        $('body').removeClass('is-scroll');
        $('.popup').removeClass('active');
        if ($this.data('video-link')) {
            $('.popup video').attr('src', $this.data('video-link'));
        }
        $('div[data-popup="' + popupButtonData + '"]').addClass('active');
        $('body').addClass('is-scroll');
    });
}

popupOpen();
popupOpen();
$('.popup-close').on('click', function (e) {
    var $this = $(this);
    $this.parent().parent().removeClass('active');
    $('.popup-overlay').removeClass('active');
    $('body').removeClass('is-scroll');
});
$('.popup-overlay').on('click', function (e) {
    var $this = $(this);
    $this.removeClass('active');
    $('.popup').removeClass('active');
    $('body').removeClass('is-scroll');
});
$('.header-btn').on('click', function (e) {
    e.preventDefault();
    $(this).toggleClass('active');
    $('.header').toggleClass('active');
    $('body').toggleClass('is-scroll');
});
$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();

    var $section = $(this).closest('.js-products-section');

    if ($section.length) {
        $('html, body').animate({
            scrollTop: $section.offset().top - 100 // Можна змінити відступ
        }, 400);
    }
});
$('select').selectric();
jQuery(function ($) {
    if (typeof MyProductsAjax === 'undefined') {
        console.warn('MyProductsAjax is not defined');
        return;
    }

    $('.js-products-section').each(function () {
        var $section    = $(this);
        var $wrap       = $section.find('.js-product-wrap');
        var $pagination = $section.find('.js-product-pagination');
        var $sortList   = $section.find('.js-sort-list');
        var $pagCurrent = $section.find('.js-page-current, .js-pagination-current');

        if (!$wrap.length) return;

        var currentSort = 'date_desc';
        var taxFilters  = {};

        function loadProducts(page) {
            var currentPage  = page || 1;
            var source_id    = $wrap.data('source-id') || $section.data('source-id') || 0;
            var perPage      = $wrap.data('per-page') || $section.data('per-page') || 8;
            var mode         = $section.data('products-mode') || 'catalog';
            var manualIdsStr = $section.data('manual-ids') || ''; // 🔹 додаємо

            $.ajax({
                url: MyProductsAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'load_products',
                    nonce: MyProductsAjax.nonce,
                    page: currentPage,
                    sort: currentSort,
                    source_id: source_id,
                    per_page: perPage,
                    mode: mode,
                    manual_ids: manualIdsStr,   // 🔹 ось тут
                    tax: taxFilters,
                    lang: MyProductsAjax.lang
                },
                beforeSend: function () {
                    $wrap.addClass('is-loading');
                },
                success: function (res) {
                    if (!res.success) return;

                    $wrap.html(res.data.html);
                    $wrap.data('current-page', res.data.current);
                    $wrap.data('max-pages', res.data.max_pages);

                    buildPagination(res.data.current, res.data.max_pages);
                    if ($pagCurrent.length) {
                        $pagCurrent.text(res.data.current);
                    }
                },
                complete: function () {
                    $wrap.removeClass('is-loading');
                }
            });
        }

        function buildPagination(current, max) {
            if (!$pagination.length) return;

            if (!max || max < 1) {
                $pagination.html('');
                return;
            }

            var html = '<ul>';
            html += '<li><a href="#" class="prev' + (current <= 1 ? ' disabled' : '') + '"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path d="M12 4L7 9.5L12 15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>\n' +
                '</svg></a></li>';

            for (var i = 1; i <= max; i++) {
                if (i === current) {
                    html += '<li><span class="active" data-page="' + i + '">' + i + '</span></li>';
                } else {
                    html += '<li><a href="#" data-page="' + i + '">' + i + '</a></li>';
                }
            }

            html += '<li><a href="#" class="next' + (current >= max ? ' disabled' : '') + '"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path d="M7 4L12 9.5L7 15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>\n' +
                '</svg></a></li>';
            html += '</ul>';

            $pagination.html(html);
        }

        $section.on('change', '.js-tax-filter', function () {
            var $sel      = $(this);
            var taxonomy  = $sel.data('taxonomy');
            var termSlug  = $sel.val() || '';

            if (!taxonomy) return;

            if (termSlug) {
                taxFilters[taxonomy] = termSlug;
            } else {
                delete taxFilters[taxonomy];
            }

            loadProducts(1);
        });

        $sortList.on('click', 'li', function () {
            var sort = $(this).data('sort');
            if (!sort) return;

            currentSort = sort;
            $sortList.find('li').removeClass('active');
            $(this).addClass('active');

            loadProducts(1);
        });

        $pagination.on('click', 'a', function (e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;

            var current = parseInt($wrap.data('current-page'), 10) || 1;
            var max     = parseInt($wrap.data('max-pages'), 10) || 1;
            var page    = current;

            if ($(this).hasClass('prev')) {
                page = current > 1 ? current - 1 : 1;
            } else if ($(this).hasClass('next')) {
                page = current < max ? current + 1 : max;
            } else {
                page = parseInt($(this).data('page'), 10) || 1;
            }

            if (page !== current) {
                loadProducts(page);
            }
        });

        loadProducts(1);
        $section.on('click', '.js-go-last', function (e) {
            e.preventDefault();

            var lastPage = parseInt($(this).data('last'), 10) || 1;

            // просто викликаємо той же AJAX, що й для пагінації
            loadProducts(lastPage);

            // якщо хочеш — ще й скрол до секції
            $('html, body').animate({
                scrollTop: $section.offset().top - 100
            }, 400);
        });
    });
});


jQuery(function($) {

    const $form   = $('.js-search-form');
    const $input  = $('.js-search-input');
    const $result = $('#search-results');
	const $wrapper = $('.resultsearch-form__list');
    if (!$form.length || !$input.length || !$result.length) return;

    let timer = null;
    const delay = 300; 

    function fetchResults(page = 1) {
        const query = $.trim($input.val());

        if (query.length < 2) {
            $result.html('');
            return;
        }else{
			
		}

        $.ajax({
			url: MyProductsAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'ajax_products_search',
                s: query,
                paged: page,
            },
            beforeSend() {
                $result.addClass('loading'); 
				$result.addClass('active');
            },
            success(html) {
                $result.html(html);
				
            },
            complete() {
                $result.removeClass('loading');
            }
        });
    }


    $form.on('submit', function(e) {
        e.preventDefault();
        fetchResults(1);
    });

    $input.on('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => fetchResults(1), delay);
		
    });

    $result.on('click', '.js-search-page', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'), 10) || 1;
        fetchResults(page);
    });

});
