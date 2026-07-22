const form = document.querySelector('form[data-filterform]');

var pages = document.querySelectorAll('a.internal');

var autoSubmit = true;

// enable form because JS is enabled
for (var input of form.elements) {
    input.disabled = false;
}

// check for submit button
for (var input of form.elements) {
    if (input.type == 'submit') {
        var autoSubmit = false;
    }
}

/**
 *
 * @param {Event} event
 */
function filter(event) {
    event.preventDefault();

    var tags = form.elements['tag'] ?? [];

    // fix case where element is alone
    if (!(tags instanceof RadioNodeList)) {
        tags = [tags];
    }

    // identify selected tags
    var checkedTags = new Set();
    for (var tag of tags) {
        if (tag.value === '') {
            continue;
        }

        if (tag.type === 'checkbox' || tag.type === 'radio') {
            if (tag.checked) {
                checkedTags.add(tag.value);
            }
        } else if (tag.localName === 'select') {
            if (tag.multiple) {
                console.log('filter form: mutliple select cannot be used');
            } else {
                checkedTags.add(tag.value);
            }
        }
    }

    if (form.elements['search']) {
        var search = form.elements['search'].value;
    } else {
        var search = '';
    }

    for (var page of pages) {
        // remove old classes
        page.classList.remove('filtered-tag-or');
        page.classList.remove('filtered-tag-and');
        page.classList.remove('filtered-search');

        // filter out non matching search
        if (search !== '') {
            if (
                !page.text.toLowerCase().includes(search.toLowerCase()) &&
                !page.title.toLowerCase().includes(search.toLowerCase()) &&
                !page.dataset.id.includes(search.toLowerCase())
            ) {
                page.classList.add('filtered-search');
            }
        }

        // filter out non-matching tags
        if (checkedTags.size !== 0 && page.hasAttribute('data-tag')) {
            var pageTags = new Set(page.dataset.tag.split(' '));

            var intersection = checkedTags.intersection(pageTags);
            if (intersection.size === 0) {
                page.classList.add('filtered-tag-or');
            }

            if (checkedTags.size !== intersection.size) {
                page.classList.add('filtered-tag-and');
            }
        }
    }
}

if (autoSubmit) {
    filter(new Event('firstLoad'));
    for (var input of form.elements) {
        if (input.type === 'search' || input.type === 'text') {
            input.addEventListener('input', filter);
        } else {
            input.addEventListener('change', filter);
        }
    }
}

form.addEventListener('submit', filter);
