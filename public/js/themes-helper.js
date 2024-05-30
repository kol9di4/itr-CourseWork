$(function(){
    function switchTeheme(colorSheme){
        localStorage.setItem('theme', colorSheme);
        $('body').attr('data-bs-theme',localStorage.getItem('theme'));
    }
    $('body').on('click','#light-theme-button',function (e){
        e.preventDefault();
        switchTeheme('light');
    })
    $('body').on('click','#dark-theme-button',function (e){
        e.preventDefault();
        switchTeheme('dark');
    })
})