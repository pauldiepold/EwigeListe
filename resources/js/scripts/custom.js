$(function () {
    $('body').on('hidden.bs.modal', '.modal', function() {
        $('.btn').blur();
    });
});


