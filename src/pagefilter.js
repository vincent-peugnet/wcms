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

    var tags = form.elements['tag'];

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

    for (var page of pages) {
        // remove old classes
        page.classList.remove('filtered-tag-or');
        page.classList.remove('filtered-tag-and');

        //
        if (checkedTags.size === 0) {
            continue;
        }

        if (page.hasAttribute('data-tag')) {
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
        input.addEventListener('change', filter);
    }
}

form.addEventListener('submit', filter);
