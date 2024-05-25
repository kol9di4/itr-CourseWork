$(function(){
    $('button.like').on('click',function(){
        var likeType = 1;
        const data = {
            likeType: likeType
        };
        $.post(window.location.href+'/like',
            data,
            function(response) {
            console.log(response);
        }, 'json');
    });
    $('button.dislike').on('click',function(){
        var likeType = -1;
        const data = {
            likeType: likeType
        };
        $.post(window.location.href+'/like',
            data,
            function(response) {
                console.log(response);
            });
    });
})
