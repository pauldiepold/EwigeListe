$(function () {
    $('#navbarCollapse').on('show.bs.collapse', function () {
        $('#fa-bars').hide();
        $('#fa-times').show();
    });
    $('#navbarCollapse').on('hide.bs.collapse', function () {
        $('#fa-bars').show();
        $('#fa-times').hide();
    });
});