jQuery(document).ready(function(){
    var jcarousel = jQuery('.jcarousel');

    jcarousel
    .on('jcarousel:reload jcarousel:create', function () {
        var carousel = jQuery(this),
        width = carousel.innerWidth();

        width = '50px'
        carousel.jcarousel('items').css('width', Math.ceil(width));
    })
    .jcarousel({
        wrap: 'circular'
    });

    jQuery('.jcarousel-control-prev')
    .jcarouselControl({
        target: '-=1'
    });

    jQuery('.jcarousel-control-next')
    .jcarouselControl({
        target: '+=1'
    });
});