import cytoscape from 'cytoscape';
import coseBilkent from 'cytoscape-cose-bilkent';
import fcose from 'cytoscape-fcose';
import euler from 'cytoscape-euler';

cytoscape.use(euler);
cytoscape.use(fcose);
cytoscape.use(coseBilkent);

let options = {
    container: document.getElementById('graph'),
};

Object.assign(options, data);

let cy = cytoscape(options);

cy.on('tap', 'node', function() {
    try {
        // your browser may block popups
        window.open(this.data('leftclick'));
    } catch (e) {
        // fall back on url change
        window.location.href = this.data('leftclick');
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
