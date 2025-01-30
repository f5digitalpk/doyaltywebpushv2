$(document).ready(function() {
    // Toggle the menu when menu icon is clicked
    $('.menu-icon').click(function() {
        $('.panel').toggleClass('show-menu');
        $('.menu').toggleClass('active');
        $('.circle-1').toggleClass('slide');
        $('.dash-top').toggleClass('slide-top');
    });

    // Close the menu if clicking outside of the menu
    $(document).click(function(e) {
        if (!$(e.target).closest('.menu, .menu-icon').length) {
            $('.panel').removeClass('show-menu');
            $('.menu').removeClass('active');
            $('.circle-1').removeClass('slide');
            $('.dash-top').removeClass('slide-top');
        }
    });
});
