
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
    $(this).toggleClass('active');
})
$('.sort-section__title').on('click', function (e){
    $(this).next().toggleClass('active');
})
$('.sort-section__list li').on('click', function (e){
    $('.sort-section__list').removeClass('active');
})
$('.search-btn').on('click', function (e){
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
    }
});
$(document).mouseup( function(e){
    var div = $( ".search-form" );
    if ( !div.is(e.target)
        && div.has(e.target).length === 0 ) {
        $('.search-form').removeClass('active');
    }
});
$('.product-page__category a').on('click', function (e) {
    e.preventDefault();
    $('.product-page__category a').removeClass('active');
    $(this).addClass('active');
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
            preferredCountries: ["us", "fr", "de", "es"],
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
    var $popupButton = $('.btn-popup');
    $popupButton.on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        var popupButtonData = $this.data('popup');
        $('.header-btn').removeClass('active');
        $('.header').removeClass('active');
        $('body').removeClass('is-scroll');
        if($this.data('video-link')){
            $('.popup video').attr('src', $this.data('video-link'))
            $('.popup').removeClass('active');
            $('div[data-popup = '+popupButtonData+']').addClass('active');
            $('body').addClass('is-scroll');
        }else{
            $('.popup').removeClass('active');
            $('div[data-popup = '+popupButtonData+']').addClass('active');
            $('body').addClass('is-scroll');
        }

    });

}
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
$('select').selectric();
