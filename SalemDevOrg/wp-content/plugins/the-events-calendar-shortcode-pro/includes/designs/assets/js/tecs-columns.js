(function($){
    $(document).ready(function(){
        var $ecsGrid = $('.ecs-grid').imagesLoaded( function() {
            $ecsGrid.masonry({
                itemSelector: '.ecs-event',
                columnWidth: '.ecs-grid-sizer',
                percentPosition: true,
                //fitWidth: true,
                gutter: '.ecs-gutter-sizer'
            });
        });
    });
})(jQuery);