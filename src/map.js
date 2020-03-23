import cytoscape from 'cytoscape';
import coseBilkent from 'cytoscape-cose-bilkent';

cytoscape.use(coseBilkent);

let options = {
    container: document.getElementById('graph'),
};

Object.assign(options, data);

let cy = cytoscape(options);
