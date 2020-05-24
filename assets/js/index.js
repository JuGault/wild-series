import '../css/index.scss';
const $ = require('jquery');
require('bootstrap');
$('.carousel').carousel({
    interval: false
})
$(document).ready(function() {
    $("#myCarousel").on("slide.bs.carousel", function(e) {
        let $e = $(e.relatedTarget);
        let idx = $e.index();
        let itemsPerSlide = 3;
        let totalItems = $(".carousel-item").length;

        if (idx >= totalItems - (itemsPerSlide - 1)) {
            let it = itemsPerSlide - (totalItems - idx);
            for (var i = 0; i < it; i++) {
                // append slides to end
                if (e.direction == "left") {
                    $(".carousel-item")
                        .eq(i)
                        .appendTo(".carousel-inner");
                } else {
                    $(".carousel-item")
                        .eq(0)
                        .appendTo($(this).find(".carousel-inner"));
                }
            }
        }
    });
});
