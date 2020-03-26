import cytoscape from 'cytoscape';
import coseBilkent from 'cytoscape-cose-bilkent';

cytoscape.use(coseBilkent);

let options = {
    container: document.getElementById('graph'),
};

Object.assign(options, data);

let cy = cytoscape(options);

cy.on('tap', 'node', function() {
    try {
        // your browser may block popups
        window.open(this.data('id'));
    } catch (e) {
        // fall back on url change
        window.location.href = this.data('id');
    }
});

cy.on('cxttap', 'node', function() {
    try {
        // your browser may block popups
        window.open(this.data('edit'));
    } catch (e) {
        // fall back on url change
        window.location.href = this.data('edit');
    }
});
