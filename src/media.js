import { activateCheckall, activateCloseSubmenus } from './fn/fn';

window.addEventListener('load', () => {
    activateCheckall('id[]', 'checkall');
    activateCloseSubmenus();
});


// drag and drop trigger regular file input

const fileInput = document.getElementById('file');
const fileMenu = fileInput.closest('details');

'drag dragstart dragend dragover dragenter dragleave drop'.split(' ').forEach(function(listener){
    document.body.addEventListener(listener,function(e) {
        e.preventDefault();
        e.stopPropagation();
    },false);
});

'dragover dragenter'.split(' ').forEach(function(listener){      
    document.body.addEventListener(listener,function(e) {
        fileMenu.setAttribute("open", true);
    },false);
});

document.body.addEventListener('drop',function(e) {
    const files = e.dataTransfer.files;
    const info = document.createElement("p");
    info.className = "dropped-files-info";
    for(const f of files){ 
        info.innerHTML += `<span>${f.name }</span> `; 
    } 
    fileInput.insertAdjacentElement("afterend", info);
    fileInput.files = files;  
}, false);

