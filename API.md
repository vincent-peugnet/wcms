API documentation
=================

A JSON REST API for W




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
    "date": "2022-03-20T20:47:00+0100",
    "datecreation": "2022-03-20T20:47:18+0100",
    "datemodif": "2022-05-29T14:59:58+0200",
    "daterender": "2022-05-14T16:42:49+0200",
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
    "secure": 1,        // can be 0 is public, 1 is private, 2 is not published
    "interface": "main",
    "linkto": [],
    "templatebody": "",
    "templatecss": "",
    "templatejavascript": "",
    "templateoptions": [
        "thumbnail",
        "recursivecss",
        "externalcss",
        "favicon",
        "externaljavascript"
    ],
    "favicon": "",
    "thumbnail": "",
    "authors": [
        "cindy",
        "vincent"
    ],
    "invites": [],
    "readers": [],
    "affcount": 1,
    "visitcount": 0,
    "editcount": 3,
    "editby": [],
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

    GET     /api/v1/<page_id>/access

possible error codes:

- `401` if user does'nt have the rights to access the page
- `404` if page is not found
- `406` in case of invalid ID

### update

To update a page, you'll have to provide it with POST datas.
In case of success, you will get a `200` HTTP CODE and recieve the full JSON page object.

    POST    /api/v1/<page_id>/update

possible error codes:

- `401` if user does'nt have the rights to update the page
- `404` if page is not found
- `406` in case of invalid ID
- `409` in case of conflict (someone or something else updated the page before you)
_ `500` server error



### add

To create a page, just send this request using the desired page ID.

    PUT     /api/v1/<page_id>/add

possibles error codes:

- `406` in case of invalid ID
- `401` if user does'nt have the rights to update the page
- `405` if page already exist with this ID
- `500` server error
