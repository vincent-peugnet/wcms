console.log('yoo');

const formFilter = document.querySelector('form');

function w_filterpagelist() {
    let tagCheckboxesChecked = document.querySelectorAll(
        'input[type="checkbox"]:checked'
    );

    let pages = document.querySelectorAll('.pagelist li');
    let tagCount = tagCheckboxesChecked.length;

    for (var li of pages) {
        li.classList.remove('w_filter-or-out');
        li.classList.remove('w_filter-and-out');
        if (tagCount > 0) {
            let counter = 0;
            for (var tag of tagCheckboxesChecked) {
                let dataAttr = 'data-tag_' + tag.value;
                if (li.hasAttribute(dataAttr)) {
                    counter++;
                }
            }
            if (counter < 1) {
                li.classList.add('w_filter-or-out');
            }
            if (counter !== tagCount) {
                li.classList.add('w_filter-and-out');
            }
        }
    }
}

formFilter.addEventListener('click', w_filterpagelist);
