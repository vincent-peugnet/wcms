Wcms API documentation
=================

Authentication
--------------

### Using a token

W support [Bearer Authentication](https://swagger.io/docs/specification/v3_0/authentication/bearer-authentication/).

To authenticate a request, present a token in the `Authorization` HTTP Header, after the `Bearer` keyword :

    Authorization: Bearer <token>

To get, a token, you have to use the [auth route](#auth).


Page related api
----------------


### access

To access a page means to get the full JSON page object. User needs to be allowed to edit the coresponding page in order to access it.

    GET     /api/v0/page/<page_id>

possible error codes:

- `401` if user does'nt have the rights to access the page
- `404` if page is not found
- `406` in case of invalid ID

### update

To update a page, you'll have to provide some fields of [Page data](#page).
Only the given fields will be updated.
The `datemodified` field is important as it's used for conflict detection. It has to be the same as stored Page, otherwise, a `409` response is thrown.
An optional `force=1` [search parameter](https://developer.mozilla.org/en-US/docs/Web/API/URL/search) can be added to bypass conflict detection.

In case of success, you will get a `200` HTTP CODE and recieve the full JSON page object.
The `datemodif` and `editcount` field will be updated.

    POST    /api/v0/page/<page_id>/update[?force=1]

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

List all pages IDs.
This method is way more efficient than the [query](#query) version.

    GET     /api/v0/pages/list

possibles error codes:

- `401` if user does'nt have the rights to view list of page


#### example response

```json
{
    "pages": [
        "kitchen", "about-love", "22", "morbier", "birthday-2034"
    ]
}
```

### query

List pages ID using filters and sorting options.

    POST    /api/v0/pages/query

You need to provide a JSON representing search options.
Those are the same as the Web interface options.

An extra key `fields` define which field will be outputed.
If not present, all fields will be returned.
Provide an empty array if you want no field.

All the keys are optionnal.

possible error codes:

- `400` if the POST datas are not recieved or in case of JSON decoding error
- `401` if user does'nt have the rights to view list of page



#### example request

```json
{
    "fields": ["datemodif", "version"],
    "sortby": "datemodif",
    "order": 1,
    "tagfilter": ["cc", "dille"],
    "tagcompare": "AND",
    "tagnot": false,
    "authorfilter": ["vincent", "audrey"],
    "authorcompare": "OR",
    "secure": 0,
    "linkto": "the-pagest-page",
    "invert": false,
    "limit": 2,
    "since": "2022-12-09T23:27",
    "until": "2025-01-01T10:10"
}
```

#### example response

```json
{
    "pages": {
        "about-love": {
            "datemodif": "2022-12-09T23:27",
            "version": 2
        },
        "morbier": {
            "datemodif": "2021-06-09T13:33",
            "version": 2
        }
    }
}
```


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


Public API
----------

### version

Return `200` if W instal is healthy.

    GET     /api/v0/version

```json
{
    "version": "v2.3.0"
}
```

### auth

Return `200` in case of auth success.

    POST    /api/v0/auth

possible error codes:

- `400` If auth failed

#### example request

```json
{
    "username": "michel",
    "password": "ilovecrepes"
}
```

#### example response

```json
{
    "token": "ddo3jsdjqsj:fgfpgoeaalkakn5eooddf:dpapajj235redao"
}
```

Schemas
-------

### Page

```json
{
  "id": "bar",
  "title": "Bar (le duc)",
  "description": "ba bidou bap bap oh yeahhhhh",
  "lang": "",
  "tag": ["room", "enfer", "kroute"],
  "latitude": null,
  "longitude": null,
  "date": "2024-01-02T20:29:00+01:00",
  "datecreation": "2024-01-19T20:29:32+01:00",
  "datemodif": "2025-07-17T02:14:22+02:00",
  "daterender": "2025-07-16T19:35:10+02:00",
  "css": "",
  "javascript": "",
  "body": "<main>%CONTENT%</main>",
  "externalcss": [],
  "customhead": "",
  "secure": 0,
  "interface": "content",
  "linkto": [],
  "templatebody": "",
  "templatecss": "",
  "templatejavascript": "",
  "favicon": "",
  "thumbnail": "",
  "authors": ["vincent", "audrey"],
  "displaycount": 31,
  "visitcount": 3,
  "editcount": 63,
  "sleep": 0,
  "redirection": "",
  "refresh": 0,
  "password": "",
  "postprocessaction": false,
  "externallinks": [],
  "version": 2,
  "content": "Un bar! Binjour"
}
```


Usages example
--------------

### Update a page element.

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

### Get the list of pages

```js
const options = {sortby:"datemodif", limit: 40, tagfilter: ["galaxy", "sublime"]};
fetch('http://localhost:8080/api/v0/pages/query', {
    method: "POST",
    body: JSON.stringify(options),
})
    .then(res => res.json())
    .then(console.log);
```

### Create a page with generated ID

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


