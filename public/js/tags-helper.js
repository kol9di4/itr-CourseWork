var input = document.querySelector('input#item_tag');

// initialize Tagify on the above input node reference
new Tagify(input)
var inputElm = document.querySelector('input#item_tag');

var mockAjax = (function mockAjax(){
    var timeout;
    return function(duration){
        clearTimeout(timeout); // abort last request
        return new Promise(function(resolve, reject){
            timeout = setTimeout(resolve, duration || 700, whitelist)
        })
    }
})()

// tag added callback
function onAddTag(e){
    console.log("onAddTag: ", e.detail);
    console.log("original input value: ", inputElm.value)
    tagify.off('add', onAddTag) // exmaple of removing a custom Tagify event
}

// tag remvoed callback
function onRemoveTag(e){
    console.log("onRemoveTag:", e.detail, "tagify instance value:", tagify.value)
}

// on character(s) added/removed (user is typing/deleting)
function onInput(e){
    console.log("onInput: ", e.detail);
    tagify.whitelist = null; // reset current whitelist
    tagify.loading(true) // show the loader animation

    // get new whitelist from a delayed mocked request (Promise)
    mockAjax()
        .then(function(result){
            tagify.settings.whitelist = result.concat(tagify.value) // add already-existing tags to the new whitelist array

            tagify
                .loading(false)
                // render the suggestions dropdown.
                .dropdown.show(e.detail.value);
        })
        .catch(err => tagify.dropdown.hide())
}

function onTagEdit(e){
    console.log("onTagEdit: ", e.detail);
}

// invalid tag added callback
function onInvalidTag(e){
    console.log("onInvalidTag: ", e.detail);
}

// invalid tag added callback
function onTagClick(e){
    console.log(e.detail);
    console.log("onTagClick: ", e.detail);
}

function onTagifyFocusBlur(e){
    console.log(e.type, "event fired")
}

function onDropdownSelect(e){
    console.log("onDropdownSelect: ", e.detail)
}