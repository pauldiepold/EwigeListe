$(function () {
    $('#navbarCollapse').on('show.bs.collapse', function () {
        $('#fa-bars').hide();
        $('#fa-times').show();
    });
    $('#navbarCollapse').on('hide.bs.collapse', function () {
        $('#fa-bars').show();
        $('#fa-times').hide();
    });

    $(document).click(function (event) {
        var navbar = $("#navbarCollapse");
        var clickover = $(event.target);
        var _opened = navbar.hasClass("show");
        if (_opened === true && clickover.closest('#navbar').length == 0) {
            navbar.collapse('hide');
        }
    });
});


