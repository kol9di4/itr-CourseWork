function addCollectionAttribute(){
    const collectionHolder = document.querySelector('#custom-attributes-wrapper');

    const item = document.createElement('div');

    item.className = 'item';

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );
    document.querySelector('#custom-attributes-wrapper').classList.remove('d-none');

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
    addRemoveAttributeButton(item);
}

function addRemoveAttributeButton(item) {
    const removeFormButton = document.createElement('a');
    removeFormButton.setAttribute('href', '#')
    removeFormButton.className = ("btn btn-sm btn-outline-primary mb-4");
    removeFormButton.innerText = 'Delete attribute';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        item.remove();
    });
}

// document.addEventListener('DOMContentLoaded',()=>{
//     document.querySelector('#add-custom-attribute')
//         .addEventListener('click', (e) =>{
//         e.preventDefault();
//         addCollectionAttribute();
//     })
//     document.querySelectorAll('#custom-attributes-wrapper div.item')
//         .forEach((row)=>{
//             addRemoveAttributeButton(row);
//         });
// })

$(function(){
    $('body').on('click','#add-custom-attribute',function (e){
        e.preventDefault();
        addCollectionAttribute();
    })
    $('#custom-attributes-wrapper div.item').forEach((row)=>{
        addRemoveAttributeButton(row);
    });
})