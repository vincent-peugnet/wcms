USER MANUAL
===========

Welcome to W's manual !

To understand the key concepts of W, you should check the introduction on [W's website](https://w.club1.fr/usage.html).




Management
----------

W let you manage a database of pages. All those pages have an [unique ID](#page-id) and have an address corresponding to `/PAGE_ID`.

You can create, edit and delete pages using [URL commands](#url-based-command-interface):

- To __create a page__, you can use the [/add](#add) command. Type in your address bar `PAGE_NAME/add`.
- To __edit a page__, you can use the [/edit](#edit) command. Type `/edit` after a page ID.

To create a page, users need to be [editors](#editor) or above.

### Home


The Home interface is the main view of your project. You can access it only when you're connected. All the pages of your database are listed here.

The [readers](#reader) and [invite editors](#invite-editor) can't access this interface.



### Filters

Filter system is at the heart of W. It's the only way to organize your pages as there is no other form of hierarchy.

You can use them to filter your page database and later to insert [list of pages](#page-list) in your pages.



### Bookmarks

Bookmarks are there to store a preset of [filters](#filters).

To create a bookmark, after ajusting [filters and sorting options](#options), select the bookmark menu in the [home menu](#home-menu), add a name and a symbol. This will add a new bookmark on the left side of the home view !

You can ajust bookmarks infos more precisely later: select a bookmark in the [bookmark panel](#bookmark-panel), then click on the bookmark tab in the [home menu](#home-menu). **Title** and **description** can be edited.

There are two kinds of bookmarks:

- **public bookmark** Can only be created and edited by [super editor](#super-editor) and above. Every users that can access home view will be able to see and use them.
- **private bookmark** Can be created by [editor](#editor) and above. They are user specific. Personnal bookmarks can only be accessed by their owners.




#### Publish a RSS feed

__Public__ bookmarks can be used to publish a RSS feed.

After selecting a bookmark in the panel, open the bookmark menu and select "publish". This will generate an XML Atom file in the `assets` folder. You can see this file by clicking on the RSS symbol <i class="fa fa-rss"></i>, next to a published bookmark.

To help visitor access this RSS feed, you can copy and paste a piece of [code to include in pages](#rss-link).

When you update or add pages in the bookmark's scope, you need to manually refresh the RSS feed by clicking the refresh <i class="fa fa-refresh"></i> button.

















Page Editing
------------

Editing pages is the core feature of W. The edition interface is accessible when [typing `/edit`](#edit) after an existing [page ID](#page-id) in the address bar. Or from the [home](#home), by clicking on the pencil button.

The pages are rendered and then cached until some new edit is performed on them or their [templates](#templating). If you want to manually render a page, use the [render](#render) URL command by typing `/render` after the page ID.

To have a more precise idea of how the render process work, you can check [RENDER.md](https://github.com/vincent-peugnet/wcms/blob/master/RENDER.md) file.

### Edition interface

[Invite editors](#invite-editor) and [editors](#editor) users can only access edition of pages where they are listed as [author](#authors).

The Edition view is composed of a main content edition area, surounded by two lateral panels that you can deploy or hide.

This is where you can edit page [metadatas](#metadatas).

**shortcuts**

- Press <kbd>CTRL</kbd> + <kbd>S</kbd> or "update" button to save your edits.
- Press <kbd>CTRL</kbd> + <kbd>D</kbd> or "display" button to view your page. This will open a new tab.


### Syntax

W take advantage of the *Markdown* syntax, flavored with a few specific syntax.

#### Markdown

Markdown can be used by default in [content elements](#content-elements) but never in the [BODY](#body).

Markdown language is a common markup language. It's meant to be more human readable than HTML, but at the same time more limited.

It's important to notice that you can mix  Markdown and HTML. But beware, as HTML can be used inside markdown, the opposite is not possible.

- [Markdown Guide](https://www.markdownguide.org/) Markdown Syntax guide
- [Michel Fortin's Markdown extra](https://michelf.ca/projects/php-markdown/extra/) The library used in W.

#### Internal links

Internal links are links that point to the pages that are managed by this instance of W. As those pages are all under the same domain and path, you don't need to use the full `https://...` adress to create internal links.
You just need to use the [page identifier](#page-id).

Here are some examples

Using __Markdown__:

    [a link to my nice page](PAGE_ID)

With __HTML__:

    <a href="PAGE_ID">link to my page</a>

As you can see, you always need to setup the text of the link. But what if we could just have [page's title](#page-title) instead ? [Wiki links are here for that purpose](#wiki-links).

##### Combine with commands

Internal links can be combined with the [URL commands](#url-based-command-interface) to achieve diabolic means.

For example, you can create a link to directly edit a page using like this:

    [edit the page](PAGE_ID/edit)



#### Wiki links

Wiki links can be used both in [content](#content-elements) and in the [BODY](#body).

Wiki style links are __only for internal links__. Their main interest compared to markdon links is that the clickable part of the link will be replaced by [page's title](#page-title) if it exist.

    [[PAGE_ID]]

Will generate :

    <a href="ID" title="DESCRIPTION" class="internal page">TITLE</a>

Where `DESCRIPTION`, `TITLE` and `ID` are the [description](#description) and [title](#page-title) of the page related to the [id](#page-id).

Note that the `.internal` class have been added to the `a` html link, allowing you to differenciate internal link styling from `.external`.









### Styling links

During the render process, some semantic datas are added to the __HTML classes of links__. It's intended to help editor style their Webpages.


- `page` link to a page of your W
- `internal`, `external` differenciate internal and external links
- `existnot`, `exist` in case of internal link, indicate if page exist or not
- `public`, `private`, `not_published` if page exist, indicate it's [privacy](#privacy) level
- `current_page` the link point to the current page


#### URL checker

If this option is activated in admin panel, W can verify if external links are still working. If a link have been checked, a class is added to the link:

- `ok` if the url is considered as working
- `dead` if the url seems to be dead

URL check is done every time a page is displayed, if the page has been edited and new urls have been added.

Status of all checked URLs is stored in cache and stay valid during 90 days.

To avoid taking to much time, if a lot of links have been added or need to be re-checked: all links may not be processed at once. The rest will be checked the next time the page is displayed.




#### CSS examples

To color in red internal links to page that does not exist:

```css
a.page.existnot {
    color: red;
}
```

Add a symbol after external links:

```css
a.external::after {
    content: "➚";
}
```

```css
a.dead::after {
    content: " ☠️";
}
```

> 💡 If you want to go further, you can also use [CSS attribute selector](https://developer.mozilla.org/en-US/docs/Web/CSS/Attribute_selectors) to achieve more complex rules.







### Inclusions

Inclusion is an advanced feature of W that allow editors to insert metadatas, generate content using codes. Those codes are always inside percent signs `%`. Inclusions can be called in [content elements](#content-elements) and in [body](#body).

#### Basic inclusions

Those codes are mainly used to print page's [metadatas](#metadatas). They are very usefull combined with [BODY templating](#body-template)

##### Title inclusion

    %TITLE%

This will include the page [title](#page-title).

##### Description inclusion

    %DESCRIPTION%

This will include the page [description](#description).

##### Date and time inclusion

Will print date metadata of the page into an HTML `<time>` tag. The `datetime` attribute will specify the date.

    %DATE% | %TIME% | %DATEMODIF% | %TIMEMODIF%

- `%DATE%` and `%TIME%` will print the [date and time](#date) infos of the page.
- `%DATEMODIF%` and `%TIMEMODIF%` will print the [last modification date and time](#datemodif) of the page.

Language page's metadata will be used for formating over Configuration.

Options are:

- `format` can be set to `none`, `short`, `medium`, `long`, `full`. Default is `short`.
- `lang` can be set to a *locale identifier* using [two letters language identifier](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes).

More complex example:

    %DATE?format=short&lang=fr%


##### Thumbnail inclusion

Print the [thumbnail](#thumbnail) of the page inside an `<img>` HTML tag with the `thumbnail` class.

    %THUMBNAIL%

##### Authors inclusion

    %AUTHORS%

This will print a HTML list of every [authors](#authors) of the page. If they have a specified URL or display name set in their profile, it will be rendered as well.

```html
<ul class="userlist">
    <li><span class="user user-ID"><a href="URL">NAME</a></span>
</ul>
```

##### ID inclusion

    %PAGEID% | %ID%

You have two options to print the [page ID](#page-id).

This is powerfull when combined with [URL commands](#url-based-command-interface) and [BODY templating](#body-template).

For instance, use this in a BODY template to create an edition link for the current page:

    <a href="%PAGEID%/edit">edit me !</a>

Or to add a manual render button:

    <a href="%PAGEID%/render">refresh me !</a>


##### path inclusion

    %PATH%

Include the page absolute path, including potential subfolders after domain.

##### url inclusion

    %URL%

Include the page exact URL.

##### Counters inclusion

Those counters will allow to display stats about your page.

    %VISITCOUNT%

Will display the number of [visit](#visitcount) of the page.

    <span class="counter visitcount">X</span>

----

    %EDITCOUNT%

Will display the number of [edits](#editcount) of the page.

    <span class="counter editcount">X</span>

----

    %DISPLAYCOUNT%

Will display the number of [display](#displaycount) of the page.

    <span class="counter displaycount">X</span>



##### Login inclusion

    %CONNECT%

This will include a connection form, redirecting to the actual page.

    %CONNECT?dir=<page_id>%

This will include a connection form, redirecting to a specified page. Where `<page_id>` is the [ID](#page-id) of the page.









##### Summary

    %SUMMARY%

This will include a summary based on the page headers titles.

    %SUMMARY?min=<min>&max=<max>&element=<element>%

Where `<min>` and `<max>` are integers beetwen `1` to `6`.

You can set `<min>` and `<max>` values to filters beetwen `<h*>` and `<h*>` headlines to take care of.

You can specify an [element](#content-elements) to target with the `element` parameter. The summary will only refer to this `element` headlines.








#### Page list

    %LIST%

Sometimes, when you want to add links to a lot of pages, it can be way too long. This tool is here to help you generate list of links automatically.

It use the same logic as the page filters in the [home view](#home).

    %LIST?sortby=datecreation&order=-1&secure=0%

*For example, this will print all public pages sorted by creation date in descending order.*

When you are in the [home view](#home), ajust the filters using the [options panel](#options) to achieve the page selection you desire. Then select "filters" in the [menu](#home-menu), choose options you prefer and hit "generate". You can now copy and paste the code obtainded that way in one of the [elements](#content-elements) of a page.

Display options are :

- title : display the [title](#page-title) of the page
- description : display the [description](#description) of the page.
- thumbnail : display the [thumnail](#thumbnail) of the page.
- date : display the [date](#date) of the page.
- time : display the [time](#date) of the page.
- author: display the [authors](#authors)
- hide current: if checked, hide the currently browsed from the list.

Generate :


    <ul class="pagelist">
        <li><a href="ID">TITLE</a> DESCRIPTION DATE TIME ... </li>
        ....
    </ul>

##### Using bookmarks

You can use an existing [bookmark](#bookmarks) as filter and sorting preset. For example, with a bookmark with the id: `published-articles`

    %LIST?bookmark=published-articles%

It is even possible to combine bookmark and filters! Specific filters and sorting will overide bookmark settings.

##### Backlinks

A specific case exist when if you want to use the [internal links](#linkto) filter in templates: Let's say you want to list all the pages that point to the current page. This is a traditionnal wiki feature called [backlinks](https://en.wikipedia.org/wiki/Backlink#Wikis).

If you want to do this, you can use the wildcard character `*` as a value of `linkto` parameter.

    %LIST?linkto=*%

It will be replaced by the [id](#page-id) of the currently displayed page.






#### Map

This feature allow you to integrate a worldmap in your page with markers linking to pages that have coordinates ([latitude](#latitude) and [longitude](#longitude) attributes) set. The syntax is very close to [page lists](#page-list) syntax.

    %MAP?tagfilter[]=france%

*For example, this will add to the map all pages that have the tag `france`, and __have coordinates__*

This will include a `div` HTML tag and some javascript.

    <div id="geomap" class="map"></div>

When you are in the [home view](#home), ajust the filters using the [options panel](#options) to achieve the page selection you desire. Then select "filters" in the [menu](#home-menu), under the "map" title, you can now copy and paste the code obtainded that way in one of the [elements](#content-elements) of a page.

**⚠️ this feature is limited to one map per page**

##### Using bookmarks

You can use an existing [bookmark](#bookmarks) as filter and sorting preset. For example, with a bookmark with the id: `my-favorite-places`

    %LIST?bookmark=my-favorite-places%

It is even possible to combine bookmark and filters! Specific filters and sorting will overide bookmark settings.


#### Random page

This surprising feature allow you to create buttons to explore your pages randomly. The syntax is very close to [page lists](#page-list) syntax.

    %RANDOM?tagfilter[]=article%

This will be replaced by an URL during rendering.
To use it properly you need to put this code inside the target of an HTML link. For example, using markdown:

    [discover a random article](%RANDOM?tagfilter[]=article%)

This is the complete syntax to create a link that will open a random page that have the tag `article`.

To generate a `%RANDOM%` code, you can ajust desired filtering in [home view](#home). Then naviguate to the menu **Filter > Random page**, copy the generated code and put it in your page.

You can use a 

If the link point to an empty set of pages, an error page will be shown.

##### Using bookmarks

You can use an existing [bookmark](#bookmarks) as filter and sorting preset. For example, with a bookmark with the id: `published-articles`

    %RANDOM?bookmark=published-articles%

It is even possible to combine bookmark and filters! Specific filters and sorting will overide bookmark settings.

##### Using internal link filter in templates

Just like the [backlinks](#backlinks) strategy, it's possible use the internal links filter in template for this specific use case: A template that have a link pointing to a random page, which is linking to the current one.

If you want to do this, you can use the wildcard character `*` as a value of `linkto` parameter.

    %RANDOM?linkto=*%

It will be replaced by the [id](#page-id) of the currently displayed page.



#### Media list

Just like [page lists](#page-list), media list are used to print a list of media based on filters.

    %MEDIA?path=<path>%

- `path` path of the folder
- `sortby` how medias are sorted
- `order` order of the sorting, can be `1` or `-1`
- `type[]` an array of media types

It's way more easier to generate such a code using [media filters](#media-filters) menu in the media panel.





#### RSS link

This code will generate a path to the corresponding [bookmark](#bookmarks) RSS feed XML file. And it will __reference the feed in the page `<head>`__.

    %RSS?id=<bookmark_id>%

The most common usage is tu use it as a link `href`. For example, with a published bookmark called `my-blog` and using [markdown](#markdown):

    [RSS feed of my blog](%RSS?id=my-blog%)









### BODY Content insertion


The BODY tab allow you to create more complex canvas for your pages.

It cannot interpret Markdown language, you have to use HTML. But still you can use all the W [inclusions codes](#inclusions).

Depending on whitch page version you use, BODY synthax may be slightly different.

#### Element inclusion

The main purpose of BODY, is to display [contents](#content-elements) of your page.

    %<element>%

**In pages V1** `<element>` can be `MAIN`, `HEADER`, `ASIDE`, `NAV` or `FOOTER`. This will invoke the selected element into your page's BODY.

**In page V2** `<element>` can only be `CONTENT`

#### External Element inclusion

##### Simple usage

    %<element>?id=<page_id>%

Just specify the [ID](#page-id) of the page [elements](#content-elements) you want to include.


##### concatenate elements


|page versions  |1  |2  |
|---------------|---|---|
|support        |✔️  |❌ |

**In pages V1**, ou can concatenate differents pages [elements](#content-elements), using `+` symbol separating [pages IDs](#page-id). All contents of differents pages elements will be concatenated inside one element.

    %<element>?id=<page1_id>+<page2_id>+*%

Where `*` is the page ID of the rendered page.

**In page V2** this is useless as you can simply repeat the simple inclusion syntax multiple times.

#### Rendering options


##### HTML tags


|page versions  |1  |2  |
|---------------|---|---|
|support        |✔️  |❌ |

    %<element>?tag=(0|1)%

This will determine if HTML tags will be printed around included element. This may be usefull for advanced users to achieve more precise HTML editing.
The default behavior can be set globaly in the [admin panel](#administration).

Example :

    %NAV?tag=0%

This will include the content of the *nav* element of your page, but without any `<nav>` and `</nav>` around.


##### Markdown disable


|page versions  |1  |2  |
|---------------|---|---|
|support        |✔️  |✔️  |

    %<element>?mardown=(0|1)%

Activate or desactivate [markdonw](#markdown) parser in called `<element>`. By default, Markdown is set to `1`.


##### Header ID


|page versions  |1  |2  |
|---------------|---|---|
|support        |✔️  |✔️  |

    %<element>?headerid=<x>-<y>%

By default, HTML `#id` are generated for every `<h1>` to `<h6>` headings. You can specify a range of headers outside which no ID will be added.

You can also set `headerid=0` to totaly disable ID generation for this element.


##### Anchor links in title


|page versions  |1  |2  |
|---------------|---|---|
|support        |✔️  |✔️  |

    %<element>?headeranchor=(0|1|2)%

This render option is used to add automatic target link to title anchors.

choose option 1 to transform headings into links. Choose option 2 to add a link after the title using a number sign `#`.

This settings is deactivated by default.


##### URL linker


|page versions  |1  |2  |
|---------------|---|---|
|support        |✔️  |✔️  |

    %<element>?urllinker=(0|1)%

URL Linker is a tool that will transform a plain text URL into a link. This can be enabled or disabled specificly for each elements. The default behavior can be set globaly in the [admin panel](#administration).

##### Everylink


|page versions  |1  |2  |
|---------------|---|---|
|support        |✔️  |✔️  |

Everylink is an powerfull but surprising feature that will replace everything you type with links.

    %<element>?everylink=<level>%

Where `<level>` is an integer. By default `everylink=0`.

Everylink will transform each word containing a minimum of `<level>` letter(s), into internal links.





### Javascript

The Javascript section is untunched and will be linked to your page in the head section.
You can use a pre-defined constant object called `w` to access pages datas.

- `w.page.id` current page [id](#page-id)
- `w.page.title` current page [title](#page-title)
- `w.page.description` current page [description](#description)
- `w.page.secure` current page [privacy level](#privacy) (0: public, 1: private, 2: not published)
- `w.domain` domain name URL
- `w.basepath` folderpath after which W is installed
- `w.user.id` current user ID
- `w.user.name` current user name
- `w.user.level` current [user level](#user-levels) (0: visitor to 10: admin)




### Templating

There is no particular template document, each page can be used as a template.
There is 3 types of templating in W :

#### BODY template

This will call the BODY of another page instead of current page's BODY.

If they are sets, BODY template also provide a page [thumbnail](#thumbnail) and [favicon](#favicon).

You can use [inclusions](#inclusions) to take advantage of BODY templating.

Example of a basic but efficient BODY template, basic inclusions and some [URL commands](#url-based-command-interface).

```
<header>
    <h1>%TITLE%</h1>
    <p>%DESCRIPTION%</p>
</header>
<main>
    %CONTENT%
</main>
<footer>
    <a href="%PAGEID%/edit">edit page</a>
    <a href="%PAGEID%/delete">delete page</a>
</footer>
```




#### CSS template

CSS template allow you to link another page's stylesheet to your page.
But you still can use current page's CSS to overide rules, it will always be the last stylesheet loaded.

This templating is reccursive : If the template is itsleft using a CSS template, then all stylsheets are stacked.


#### Javascript template

This will link another's page Javascript file.








Media management
----------------


When you need to use images, sound or videos in your pages, or any other type of files, you can use the media manager to host them before including them in your pages.

[Invite editors](#invite-editor) don't have access to this place.

The media menu allow you to do more powerfull function like moving medias or delete folders it is only accessible by [super editors](#super-editor) and above.




### Media upload

Editors can upload [multiple files from their computer](#from-computer) or [one file from an URL](#from-url).

#### from computer

You may upload many files from here as long as it does'nt reach your server upload limit, which is displayed in this menu.

This upload method may have two options:

##### clean filenames

If this one is checked, W will remove or convert non-latin, uppercases or space caracters from the filename. It use the same filter as page IDs.

##### optimize images

A second option may be present if your server have `PHP-gd` and/or `PHP-Imagick` installed.

Optimization will reduce image's size on the serer which will aslo fasten the load time when broadcasted in your website.

W try to detect images that need optimization. Image that are considered un-optimized for Web diffusion are images that have **height or width above 1920px** or have a **bit/pixel ratio over 0.5**. Optimization only apply to PNG, JPEG, WEBP and BMP files (which is detected from file's extension).

Optimization take some times as the server will encode a new image. If you have a lot of images, you may convert them on your computer using a dedicated program, or make small groups of ~10 files when uploading through W.

This option is supposed to cover basic Web optimization use cases, which is why it's the default setting. You may want to disable it for complex scenarios.


#### from URL

Why should we have to go through our computer when we want to store on W a file that is already on the Web ?

This upload method try to solve this.

Just paste the URL of the file you want to add on your server and W will try to download it. Unfortunately, this is'nt always working. Hosted files may have obscure configurations that prevent this tool to reach them. But you still can download them on your computer and then [upload them from it](#from-computer).




### Media Filters

Filters and sorting options can be set by choosing wich types of files to show and how to sort them. They can be used to filter and sort a [medialist](#media-list) using the __filter__ tab in the [media menu](#media-menu).

File types are determined by their file extention. The association table is hardcoded in W's code and can be found [here](https://github.com/vincent-peugnet/wcms/blob/master/app/class/Media.php#L48).



### Magic folders

Not all folders are created equals ! There are some magic ones inside the media folder. Those folders are indestrctibles, this means that W will rebuild them if there are not present.

#### Fonts folder

The `fonts` folder is supposed help you use fonts in your project.

When you put fonts files that have a known extension like `.otf`, `.ttf`, `.woff` or `.woff2`, W will generate a css file including all of those fonts in a *@font-face* CSS rule.

This file will be saved inside the [css folder](#css-folder) and will be named `fonts.css`. It will be linked from every page you create.

That way, to use a font you've previously uploaded in this folder in your page, you can directly write the folling rule:

    font-family: <font-name>

Where `<font-name>` is the filename of the font without the extension.

To go further, you can even define font *styles*, *weights* and *stretchs*. To do so, just add those parameters in your font filename using separating dots.
For example:

    helvetica.woff2
    helvetica.italic.woff2
    helvetica.bold.woff2
    helvetica.condensed.woff2
    helvetica.italic.bold.woff2

This will add them all to the same `helvetica` font family, and indicate specificities using the *@font-face* CSS rule.

You can as well add multiple file format for the same font in order to maximize browser compatibility. To achieve this, simply add multiple files sharing the same filename that just have differents extensions.
For example:

    tahoma.woof2
    tahoma.otf

The `fonts.css` file will be automatically re-generated each time you upload or rename files in this folder using the web interface. If you add or modify files by any other way, you can manually trigger the generation. Under the *File > Magic folder* menu, select: *Regenerate @fontface CSS file*


##### Authorized properties

Here is the full list of font properties you can use in W.

- **style**
    - `italique`
    - `oblique`
- **weight**
    - `thin` conveted to `100`
    - `extra-light` conveted to `200`
    - `light` conveted to `300`
    - `medium` conveted to `500`
    - `semi-bold` conveted to `600`
    - `bold`
    - `extra-bold` conveted to `800`
    - `black`  conveted to `900`
- **stretch**
    - `ultra-condensed`
    - `extra-condensed`
    - `condensed`
    - `semi-condensed`
    - `semi-expanded`
    - `expanded`
    - `extra-expanded`
    - `ultra-expanded`


CSS properties for weight only use `normal` and `bold` as absolute values. otherwise, you have to specify it with a number value. **W** will convert text defined weight to numbered values for other common weights.


#### CSS folder

This contain two files:

- the `global.css` file, that admins can edit in the [admin panel](#administration).
- the `fonts.css` file, that is automatcly generated according to content of [fonts folder](#fonts-folder).



#### Favicon folder

This folder is supposed to contain favicons. Once favicon files (that can be `.ico`, `.png`, etc.) are uploaded here, they are listed in the favicon selection drop down list in every pages or in the admin panel.


#### Thumbnail folder

This folder is supposed to contain thumbnails images. Once images files (that can be `.jpg`, `.png`, etc.) are uploaded here, they are listed in the thumbnail selection drop down list in every pages or in the admin panel.










Administration
--------------

Acces to admin interface is reserved to [admin](#administrator) users. This is mostly a graphical representation of the `/config.json` file located at the root of W installation path.

### Themes

You change the interface theme in the admin panel, under *interface*. There is a kit of presets, but you can even add your own themes ! And it is actualy *super easy* 😎. To do so, go to the folder `/assets/css/themes/` and duplicate one of the preset you prefer. Then you can edit the CSS variables to quickly stylise your theme colors.


### LDAP auth

In addition to the traditionnal internal authentication (password is checked against W internal users database), external authentication can be used through [the LDAP protocol](https://fr.wikipedia.org/wiki/Lightweight_Directory_Access_Protocol). In order to do that, W need to be able to read data from an existing LDAP server. LDAP connexion can be configured int the admin panel.

Users authenticated through LDAP and users using internal authentication can coexist ! One major diference, is that W won't allow to edit the password of LDAP users.

#### User creation

LDAP users cannot be created from the user management interface. User may be converted to LDAP auth by setting password to `null`, in their associated JSON file in W's database.

But it's possible to automatically create new user as they connect for the first time using LDAP. To do this, __a default user class__ must be set in the admin panel.



Profile
-------

The profile panel allow each users to edit some preferences. To access it, click on your username preceded by <i class="fa fa-user"></i> on the right side of the main menu.

__Display name__ ande __url__ are used in case of an [authors inclusion](#authors-inclusion) or a [page list](#page-list) inclusion with `authors=1`.









References
---------


### Page structure


Technicaly, each page is stored as a JSON object in yout fatabase folder.

A page consist of meta informations and contents.

#### Metadatas

Page metadatas can be set through the page [edition interface](#edition-interface) or with multi edit in the [home menu](#home-menu).

##### Page ID

__The unique identifier of a page__. It can only contain lowercases characters from `a-z`, numbers `0-9`, underscore `_` and hyphen `-`.
Also, `assets` and `media` are reserved paths, therefore cannot be used as page ID.

Normaly W will take care of cleaning your pages's ID, by lowering uppercases, removing some accents, and remplacing special characters or spaces with hyphens.


##### Page title

The page title is also very important. It's like the official name of a page. It will be displayed in the browser tab of your page.


##### Description

The description will be used by web search engines when they display lists of pages. This is also usefull for social media sharing.


##### Tag

Tags are very powerfull to help you organize your pages. As in __W__, there is no hierarchy between pages, this is the only tool to create groups of pages.

Tags can be set in the left panel of the editor interface using comma to separate tags, or by using the multi edit tool in the Home menu bar > Edit.


##### Privacy

Pages have 3 possible levels of privacy:

- `0` **public** Free to read for every [visitor](#visitor).
- `1` **private** Hidden to [visitor](#visitor), but accessible to [reader users](#reader).
- `2` **not published** Only readable for users allowed to edit the page.

##### Date

When you create a page, the date is set to the currente date and time.

This metadata can later be set manually by the page editors.


##### datecreation

*read only*

Page creation date.


##### Datemodif

*read only*

It's just the last time the page has been edited.


##### Latitude

Must be between -90 and 90.

##### Longitude

Must be between -180 and 180.


##### Favicon

The page favicon can be set in the [edition view](#edition-interface).
A default favicon for all pages can be set in the [administration interface](#administration).

If no favicon is specified for a page, then the BODY template favicon will be used. If the page have no BODY template or the favicon of the template is not set, then the default favicon is used.


##### Thumbnail

The thumbnail have 3 use cases :

- When you share a link to a page on a social network
- When you generate a [list of page](#page-list) and activated the `thumbail` option. 
- When using [thumbnail inclusion](#thumbnail-inclusion) in a page.

The page thumbnail can be set in the [edition view](#edition-interface).
A default thumbnail for all pages can be set in the [administration interface](#administration).

If no thumbnail is specified for a page, then the BODY template thumbnail will be used. If the page have no BODY template or the thumbnail of the template is not set, then the default thumbnail is used.


##### Authors

List of [users](#user-levels) that have edited the page, or that can do it. You have to be at least [super-editor](#super-editor) to add or remove authors.

##### Linkto

*read only*

It's a list of all internal links contained by a page. A link is considered internal if it point to anoter page of your W. This is analysed when the page is rendered. This metadata is also used to display the page graph in the home view.

##### Visitcount

*read only*


Count the number of visitors that opened the page. Unlike [displaycount](#displaycount), only un-connected users are counted this way.

##### Editcount

*readonly*


Count the number of time the page has been edited. Even empty edits are counted.

##### Displaycount

*readonly*


Count the number of time the page is opened.








#### Content

##### content elements

**In pages V1**, there are 5 content elements : Main, Nav, Aside, Header, Footer.

**In pages V2**, there is only one content element called "content".

##### CSS

Each page have a dedicated stylesheet, that can be called by other pages using [templating](#templating).

##### BODY

##### Javascript






### User levels

Not all users are equal. Users have rights according to their level (from 0 to 10).

#### Visitor

> 👤 level : 0

Someone browsing your Website but not connected.

#### Reader

> 👤 level : 1

Reader users are allowed to read [private pages](#privacy). They cannot do any editing.

#### Invite Editor

> 👤 level : 2

Invite Editors are the lowest editor status possible. They can only access the [Edition interface](#edition-interface). They can't create page but only edit page when listed as [author](#authors).

#### Editor

> 👤 level : 3

- Can create pages and edit them.
- Can only edit pages when listed as [authors](#authors).
- Can only delete pages if they are the only author.

#### Super Editor

> 👤 level : 4

- Can edit any pages they like.
- Can manage [author](#authors) of a page.
- Can use the home menu and media menu to access powerfull features.
- Can delete any page.
- Have access to mass edit features.

#### Administrator

> 👤 level : 10

- Can access the [admin interface](#administration).
- Can create new users.

### URL based command interface

#### Pages commands

Type thoses commands after a __page_id__ 

    <page_id>/<COMMAND>

##### /add

Command used to add a page in the database.

It's possible to directly type the [title](#page-title) of the page, the ID will be automatically cleaned from white space or invalid characters.

##### /add:id

    /add:<page_id>

Will create a new page, as a copy of `<page_id>`.

##### /edit

Command used to edit a page. If you're not logged in, it will ask for your credentials.

##### /delete

Command used to delete a page from the database. This will ask you for a confirmation.

##### /render

Force the rendering of a page.

##### /download

Simply download the page as a JSON object file. Reserved to users that can edit the page.

##### /log

Show a `var_dump` of the page object. This could be usefull for debbuging.

##### /duplicate

    <pageid>/duplicate:<newpageid>

Duplicate current page with under a new ID.

##### /login

If you are not connected yet, this will ask you for credentials.


##### /logout

If you were connected, this will disconnect you.

