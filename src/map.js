import cytoscape from 'cytoscape';

let options = {
    container: document.getElementById('graph'),
};

Object.assign(options, data);

let cy = cytoscape(options);
