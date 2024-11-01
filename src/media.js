import {
    activateCheckall,
    activateCloseSubmenus,
    initWorkspaceForm,
} from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('id[]', 'checkall');
    activateCloseSubmenus();
    initWorkspaceForm();
});

/**
 * Drag and drop files anywhere to fill regular file input
 */
const fileInput = document.getElementById('file');
const fileMenu = fileInput.closest('details');
const droppedFilesSet = new Set(); // Track dropped files

const allDragAndDropListeners = [
    'drag',
    'dragstart',
    'dragend',
    'dragover',
    'dragenter',
    'dragleave',
    'drop',
];
for (const listener of allDragAndDropListeners) {
    document.body.addEventListener(
        listener,
        function(e) {
            e.preventDefault();
            e.stopPropagation();
        },
        false
    );
}

const dragListeners = ['dragover', 'dragenter'];
for (const listener of dragListeners) {
    document.body.addEventListener(
        listener,
        function(e) {
            fileMenu.setAttribute('open', true);
        },
        false
    );
}

document.body.addEventListener(
    'drop',
    function(e) {
        const droppedFiles = e.dataTransfer.files;
        const fileList = new DataTransfer();

        // Prepare user feeedback
        const info =
            document.querySelector('.dropped-files-info') ||
            document.createElement('p');

        // If feedback element doesn’t exist
        if (!document.querySelector('.dropped-files-info')) {
            info.className = 'dropped-files-info';
            fileInput.insertAdjacentElement('afterend', info);
        }

        // Loop through the currently existing files in the file input
        for (let i = 0; i < fileInput.files.length; i++) {
            fileList.items.add(fileInput.files[i]);
            // Track already added files
            droppedFilesSet.add(
                fileInput.files[i].name + fileInput.files[i].size
            );
        }

        // append only new files to droppedFiles array
        for (const file of droppedFiles) {
            const uniqueID = file.name + file.size;

            // Check if the file is already added
            if (!droppedFilesSet.has(uniqueID)) {
                fileList.items.add(file); // Add file if it's not already added
                droppedFilesSet.add(uniqueID); // Add to the tracking set
                info.innerHTML += `<span>${file.name}</span> `; // Feedback
            }
        }

        fileInput.files = fileList.files;
    },
    false
);
