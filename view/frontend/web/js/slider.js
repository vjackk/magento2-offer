require([
    'jquery',
    'jquery/ui',
    'slick'
], function($) {
    $(document).ready(function() {
        var slider = $(".slick-slider");

        slider.slick({
            dots: false,
            infinite: false,
            slidesToShow: 1,
            slidesToScroll: 1
        });

        slider.show();
    });
});
