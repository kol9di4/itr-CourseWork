$(function(){
    $('body').on('click','a.collection-delete',function(e){
        e.preventDefault();
        url = $(this).attr('href');
        $.post(window.location.origin+url,
            function(response) {
                window.location.href = window.location.origin;
        });
    });
    $('body').on('click','a.item-delete',function(e){
        e.preventDefault();
        url = $(this).attr('href');
        $.post(window.location.origin+url,
            function(response) {
                window.location.href = window.location.origin;
            });
    });
})