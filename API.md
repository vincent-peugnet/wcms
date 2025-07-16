Wcms API documentation
=================


Page related api
----------------




<details>
    <summary>JSON page object sample</summary>
    <pre>
        <code>
{
    "id": "garden",
    "title": "A nice Garden !!",
    "description": "With a lot of snails",
    "lang": "en",
    "tag": [
        "place", "green"
    ],
    "date": "2022-03-20T20:47:00+01:00",
    "datecreation": "2022-03-20T20:47:18+01:00",
    "datemodif": "2022-05-29T14:59:58+02:00",
    "daterender": "2022-05-14T16:42:49+02:00",
    "css": "",
    "javascript": "",
    "body": "%HEADER%\r\n\r\n%NAV%\r\n\r\n%ASIDE%\r\n\r\n%MAIN%\r\n\r\n%FOOTER%",
    "header": "",
    "main": "# Welcome to my Garden !!",
    "nav": "",
    "aside": "",
    "footer": "",
    "externalcss": [],
    "customhead": "",
    "secure": 1,
    "interface": "main",
    "linkto": [],
    "templatebody": "",
    "templatecss": "",
    "templatejavascript": "",
    "favicon": "",
    "thumbnail": "",
    "authors": [
        "cindy",
        "vincent"
    ],
    "displaycount": 1,
    "visitcount": 0,
    "editcount": 3,
    "sleep": 0,
    "redirection": "",
    "refresh": 0,
    "password": null
}
        </code>
    </pre>
</details>


### access

To access a page means to get the full JSON page object. User needs to be allowed to edit the coresponding page in order to access it.

    GET     /api/v0/page/<page_id>

possible error codes:

- `401` if user does'nt have the rights to access the page
- `404` if page is not found
- `406` in case of invalid ID

### update

To update a page, you'll have to provide it with POST datas.
In case of success, you will get a `200` HTTP CODE and recieve the full JSON page object.

    POST    /api/v0/page/<page_id>/update

possible error codes:

- `400` if the POST datas are not recieved or in case of JSON decoding error
- `401` if user does'nt have the rights to update the page
- `404` if page is not found
- `406` in case of invalid ID
- `409` in case of conflict (someone or something else updated the page before you)
- `500` server error



### add

To create a page, just send this request using the desired page ID.

    POST    /api/v0/page/<page_id>/add

Optionaly, you can provide a JSON page object that will be used for the newly created page.

possibles error codes:

- `401` if user does'nt have the rights to create the page
- `405` if page already exist with this ID
- `406` in case of invalid ID
- `500` server error while saving page


### put

This will create a page if not existing or erase an existing one with given JSON.
`datecreation` will be reset if it already existed.

    PUT     /api/v0/page/<page_id>

possibles success codes:

- `200` if the page already existed and has been successfully overiden.
- `201` if the page was created created

possibles error codes:

- `401` if user does'nt have the rights to update/create the page
- `406` in case of invalid ID
- `500` server error while saving page



### delete

To delete a page, just send this request using the desired page ID.

    DELETE  /api/v0/page/<page_id>

possibles error codes:

- `401` if user does'nt have the rights to delete the page
- `404` if page is not found
- `406` in case of invalid ID
- `500` server error


### list

List all pages IDs

    GET     /api/v0/pages/list

possibles error codes:

- `401` if user does'nt have the rights to view list of page


### query

List pages as objects using given filters and sorting options.

    POST    /api/v0/pages/query

You need to provide a JSON storing search options.

```json
{
    sortby: "datemodif",
    order: 1,
    tagfilter: ["cc", "dille"],
    tagcompare: "AND",
    tagnot: false,
    authorfilter: ["vincent", "audrey"],
    authorcompare: "OR",
    secure: 0,
    linkto: "the-pagest-page",
    invert: false,
    limit: 40,
    since: 2022-12-09T23:27,
    until: 2025-01-01T10:10
}
```

possible error codes:

- `400` if the POST datas are not recieved or in case of JSON decoding error
- `401` if user does'nt have the rights to view list of page




User related API
----------------


### get

Retrieve an user as a JSON object

    GET     /api/v0/user/<userid>

possible error codes:

- `401` if user is'nt an admin
- `404` if user is not found



Media related API
-----------------


### upload

Upload a file. Specify a target `<path>` and send the file through the `body` of the request. Folders will be created automatically.

    POST    /api/v0/media/upload/<path>

possible error codes:

- `400` If a error occured while reading the file stream or when creating the file/folders
- `403` if user is'nt an editor
- `406` if user is not found



Usages example
--------------

### Get a page and then update the MAIN element.

```js
obj = await fetch('http://localhost:8080/api/v0/page/jardin')
    .then(res => res.json());
obj.main += "foobar";
fetch('http://localhost:8080/api/v0/page/jardin/update', {
    method: "POST",
    body: JSON.stringify(obj),
})
    .then(res => res.text())
    .then(console.log);
```

### Get the list of all pages.

```js
obj = await fetch('http://localhost:8080/api/v0/pages/list')
    .then(res => res.json());
```

### Get the list of pages filted and sorted.

```js
const options = {sortby:"datemodif", limit: 40, tagfilter: ["galaxy", "sublime"]};
fetch('http://localhost:8080/api/v0/pages/query', {
    method: "POST",
    body: JSON.stringify(options),
})
    .then(res => res.json())
    .then(console.log);
```

### Create a page with a random number as ID.

#### HTML

```html
<button id="create-page">Create a new page</button>
```

#### Javascript

```js
document.querySelector('button#create-page').addEventListener('click', function(){
    const random = Math.floor(Math.random() * 1000) + 1;
    var url = "/api/v0/page/" + random;

    var promise = fetch(url, {
        method: "PUT",
        body: JSON.stringify({
            id: random,
            tag: ['generated'],
            description: 'This page has been generated thanks to the API',
            title: 'Page n°' + random,
        }),
    })
    promise.then(function(response) {
        if (response.ok) {
            alert('The page has been created !');
        } else {
            alert('erreur: ' + response.status);
        }
    });
});
```


