let arturl;

onmessage = function (e) {
    switch (e.data.type) {
        case 'init':
            arturl = e.data.arturl;
            break;
        case 'stillEditing':
            stillEditing();
            break;
        case 'quitEditing':
            quitEditing();
            break;
    };
}

function stillEditing() {
    console.log('send still editing');
    const url = arturl + '/editby';
    const req = new XMLHttpRequest();
    req.open('POST', url, false);
    req.send(null);

    const res = JSON.parse(req.responseText);
    console.log(res);
}

function quitEditing() {
    console.log('send quit editing');
    const url = arturl + '/removeeditby';
    const req = new XMLHttpRequest();
    req.open('POST', url, false);
    req.send(null);

    const res = JSON.parse(req.responseText);
    console.log(res);
}