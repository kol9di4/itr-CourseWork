
document
    .querySelectorAll('.item')
    .forEach((tag) => {
        addTagFormDeleteLink(tag)
    })

function addFormToCollection(e) {
    addTagFormDeleteLink(item);
}

function addTagFormDeleteLink(item) {
    const removeFormButton = document.createElement('button');
    removeFormButton.innerText = 'Delete this tag';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}