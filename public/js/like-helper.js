$(function(){
    function likeAjax(likeType){
        const data = {
            likeType: likeType
        };
        $.post(window.location.href+'/like',
            data,
            function(response) {
                $('.likes').html(JSON.parse(response));
            });
    }
    $('body').on('click','button.like',function(){
        likeAjax(1);
    });
    $('body').on('click','button.dislike',function(){
        likeAjax(-1);
    });
})
