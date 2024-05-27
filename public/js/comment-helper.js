// $(function(){
//     function likeAjax(likeType){
//         const data = {
//             likeType: likeType
//         };
//         $.post(window.location.href+'/like',
//             data,
//             function(response) {
//                 $('.likes').html(JSON.parse(response));
//             });
//     }
//     $('body').on('click','#send-comment',function(){
//         $.post(window.location.href,
//             function(response) {
//                 console.log(response);
//             });
//     });
//     $('body').on( "submit", 'form[name=comment]', function(e) {
//         e.preventDefault(); // avoid to execute the actual submit of the form.
//         var form = $(this);
//         var actionUrl = form.attr('action');
//         $.ajax({
//             type: "POST",
//             url: actionUrl,
//             data: form.serialize(), // serializes the form's elements.
//             success: function(data)
//             {
//                 alert(data); // show response from the php script.
//             }
//         });
//     });
// })
