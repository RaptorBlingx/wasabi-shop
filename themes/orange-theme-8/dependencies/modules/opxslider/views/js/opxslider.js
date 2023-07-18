/* Richiede slick slider se non installato nel progetto */

jQuery(document).ready(function ($) {
  $('#js_dSlider').slick({
    dots: true,
    infinite: true,
    speed: 500,
    fade: !0,
    cssEase: 'linear',
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 8000,
    draggable: true,
    arrows: false,
    pauseOnHover: false,
    pauseOnFocus: false,
  });
});