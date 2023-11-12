USER MANUAL
===========

Introduction
------------

Welcome to __W__, this manual is here to help you get the best of this tool.

If it's you're first time using it, you should learn how to [create your first page](#create-your-first-page).

If you already know the basics, you may want to check the references :

- Discover how to [navigate](#navigation).
- Get to know the [structure of a page](#page-structure) to edit meta content.
- Learn more about the [URL based commands](#url-based-command-interface) you can type in the address bar.
- Master the [render engine](#page-editing) to release the full potential of __W__.



### Create your first page

This 4 steps tutorial will introduce you to the basic __W__ moves.

#### Add a new page


The first thing you have to do before creating a page is to **choose an address** for this page. Each page have a unique address that identify it, it is the [**page id**](#page-id). You can use the address bar of your web browser to directly access, [edit](#edit) or [delete](#delete) a page.

You can type anything in your address bar (after your W installation root). If it is an already exisiting page_id, you will access a page. Otherwise, you will arrive to an empty space waiting to be filled.

Once you've typed an address and found nothing, if you are connected, you now have the opportunity to create a page at this address.

There is two ways to do this :

1. Graphicaly, by clicking the "create" button
2. Using the address bar, by typing `/add` just after the address

There it is ! Congratulation, you've created your first page using W.


#### Edit your page content

Now you should be in front of the [edit interface](#editor) of your page. If you check the address bar, you will now see `/edit` written after your page address.

You can type a few words in the main text area. Once you're happy with what you wrote, it's time to save your work : click on the <kbd>update</kbd> button in the top left corner. You can use the shortcut <kbd>CTRL</kbd> + <kbd>S</kbd> too if you prefer. After you've done this, you sould have notice that the asterix `*` next to your page_id have left, it's the sign that saving was sucesfull.

Let's come back to the page reading view to see the result of our editing. There are different approaches for doing so :

1. By removing the `/edit` command after your page address
2. Pressing the "display" button (next to "update"), or using <kbd>CTRL</kbd> + <kbd>D</kbd> shortcut.

The first method will use the same tab while the second will create a new one, wich is usefull if you want to keep your [edit interface](#editor) open to come back quickly.

You should now see something that look like what you've typed.


#### Create a hyperlink


The [markdown syntax](https://commonmark.org/), this is a formating standard well known accross the web. It's main goal is to be a intermediate between an easy to read prose text, and the internet standard tag language, the HTML. One of the main interest of Markdown, is it is ability to be mixed with HTML. That way, people can type more easily basic text, and switch to HTML at any time, to achieve a more complex layout. You can use the official Markdonw syntax with __W__ alongside with HTML to build your pages.

[Official website describing Markdown syntax](https://commonmark.org/help/)

One of the most interesting things to do when you use internet publishing, is to create HYPERLINKS. __W__ encourage you creating those links between pages using a very light syntax. There are multiple ways to do it.

1. Using classic HTML : `<a href="<page_id>">click here</a>`
2. Using Markdown :  `[click here](<page_id>)`
3. Using wiki syntax : `[[<page_id>]]`

All those methods will create a link pointing to the `<page_id>` you've given.

Those kind of links are called internal links beccause they stay inside of your domain. To set a link outside of your website, simply remplace `<page_id>` by the website adress you whant to target (ex : `https://w.club1.fr`), but this won't work with the third method as it is'nt a W page.


#### Insert images

When adding an image to a page, you can either use an already web-hosted image, wich is a bit dangerous, or host yourself the image using W's [media interface](#media-manager).

Markdown syntax for inserting an image :

    ![<alt_comment>](<image_address>)

It's the equivalent of HTML (More info about `<img>` tag on [MDN](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/Img)) :

    <img src="<image_address>" alt="<alt_comment>">

If your image is already hosted, just use it's URL address for `<image_address>`. Otherwise, access W's [media interface](#media-manager) using the top bar menu, just after "home", or by typing `<your-W-url>/!media`.

Once you're here, you'll have to first choose, using the navigation panel on the left, or create a folder, by selecting __File > New Folder__ in the menu.

When you've selected the folder you prefer for hosting your image, it's time for uploading it using __File > Upload__. Browse you computer or drag n' drop files onto the "Choose Files" button, then click "Upload". Note that you can upload multiple files at once !

Now you should see your files appearing in the table. W will remove whitespaces and special characters of your medias to avoid syntax problems.

To add the image on your page, the simplest method is to copy the Makdown generated code on the right side.

*For bigger files transfer, you can always use FTP or SSH connections to add, delete, move files by accessing the `/media` directory of your W installation folder*












### Navigation

Discover the differents interfaces of __W__.






#### Home

The Home interface is the main view of your project. You can access it only when you're connected. All the pages of your database are listed here.

The [readers](#reader) and [invite editors](#invite-editor) can't access this interface.

##### Home menu

In the home menu [super editors and above](#super-editor) can :

- __File :__ Import pages as JSON file. (usefull for transfering pages from a W instance to another).
- __Edit :__ Apply changes, render, or delete multiple pages at once.
- __Filters :__ Use your [filtering options](#options) to generate a [automatic menu](#page-list) you can later include in a page.
- __Bookmarks :__ Create some [bookmarks](#bookmarks) to save your [filtering options](#options) as presets.
- __Display :__ Set columns to be shown (user based) and tag colors.


##### Bookmark panel

This panel list public and personnal [bookmarks](#bookmarks).
Select a bookmark to apply preset filters and sortings options.
If your filters and sort options match an existing bookmark, it will be highlighted.

##### Options

The option panel contains multiples options for sorting and filtering your pages database.

Just select the options you want and press "Fitler". Use the "Reset" button to come back to the default settings. W will keep the settings for the SESSION time.

This panel is also usefull to set up a [page list](#page-list) to include the same list of page you've filtered in any page.
##### Deep Search Bar

The Deep Search bar help you to look for words or regular expressions in your pages.

By default, searching only look in [title](#page-title), [description](#description) and [contents (markdown elements)](#content-elements), but the "other" checkbox will allow you to look up in [css](#css), [javascript](#javascript) and [BODY](#body) contents.

Unlike the [filterings options](#options) below, searches can't be saved or used for [page lists](#page-list).


##### list view

The table is composed of [meta](#meta-infos) datas and actions links that are :

__EDIT__, __READ__, __DELETE__ and __DOWNLOAD__, they are equivalent to [pages commands](#pages-commands). Don't hesitate to open edit or read links in a new tab, to keep the Home view open.

To edit columns you want to see, use the [menu](#home-menu)>Display submenu.

##### graph view

The graph give you an overview of your website, showing you links between pages.

By default orphans pages are hidden, but you can ajust settings to fit your needs.

- left click on a page to read it
- right click to edit it








#### Edition

The edition interface is accessible when [typing `/edit`](#edit) after an existing [page_id](#page-id) in the address bar. Or from the [home](#home), by clicking on the pencil button.

[Invite editors](#invite-editor) and [editors](#editor) users can only access edition of pages where they are listed as [author](#authors).

The Edition view is composed of a main content edition area, surounded by two lateral panels that you can deploy or hide.

##### left panel

This is the 

- Meta infos: edit [title](#title), [description](#description) and more infos about your page.
- Geolocalisation: to set [latitude](#latitude) and [longitude](#longitude) metadatas
- Templating
- Advanced
- Help : a quick syntax reference for markdown and W syntax

##### contents area

Each tab allow you to edit a different type of content :

[main](#main), [nav](#nav), [aside](#aside)

Note that the MAIN tab is the default opened tab, this is meant to be the most spontaneous place to store information when you are using __W__ in *note taking style*.

##### right panel











#### Media manager

When you need to use images, sound or videos in your pages, or any other type of files, you can use the media manager to host them before including them in your pages.

[Invite editors](#invite-editor) don't have access to this place.

##### Media menu

The media menu allow you to do more powerfull function like moving medias or delete folders it is only accessible by [super editors](#super-editor) and above.

- __file :__ to import new files, create or delete directories
- __edit :__ to edit selected items
- __filter :__ to export filter settings as [medialist](#media-list)
- __Bookmarks :__ Create some [bookmarks](#bookmarks) to save your [filtering options](#media-filters) as presets.


##### Explorer

The explorer allow you to navigate between differents directories. It will show you the amount of files in every folder.

##### Media Filters

Filters and sorting options can be set by choosing wich types of files to show and how to sort them. They can be used to filter and sort a [medialist](#media-list) using the __filter__ tab in the [media menu](#media-menu).

File types are determined by their file extention. The association table is hardcoded in W's code and can be found [here](https://github.com/vincent-peugnet/wcms/blob/master/app/class/Media.php#L46).













#### Profile

The profile allow you to edit some user related preferences.

##### name and URL

Your __display name__ ande __url__ are used in case of an [authors inclusion](#authors-inclusion) or a [page list](#page-list) inclusion with `authors=1`.








#### Admin







#### User manager







#### Bookmarks

A bookmark is usefull to store a preset of pages filters and sorting settings.


To create a bookmark, after ajusting [filters and sorting options](#options), select the bookmark menu in the [home menu](#home-menu), add a name and a symbol. This will add a new bookmark on the left side of the home view !

You can ajust bookmarks infos more precisely later : select a bookmark in the [bookmark panel](#bookmark-panel), then click on the bookmark tab in the [home menu](#home-menu). **Title** and **description** can be edited.

There are two kinds of bookmarks:

- **public bookmark** Can only be created and edited by [super editor](#super-editor) and above. Every users that can access home view will be able to see and use them.
- **private bookmark** Can be created by [editor](#editor) and above. They are user specific. Personnal bookmarks can only be accessed by their owners.

##### Publish a RSS feed

__Public__ bookmarks can be used to publish a RSS feed.

After selecting a bookmark in the panel, open the bookmark menu and select "publish". This will generate an XML Atom file in the `assets` folder. You can see this file by clicking on the RSS symbol <i class="fa fa-rss"></i>, next to a published bookmark.

To help visitor access this RSS feed, you can copy and paste a piece of [code to include in pages](#rss-link).

When you update or add pages in the bookmark's scope, you need to manually refresh the RSS feed by clicking the refresh <i class="fa fa-refresh"></i> button.




















Page Editing
------------

### Syntax

#### Markdown

- [Daring Fireballs](https://daringfireball.net/projects/markdown/syntax) Official reference of Markdown Syntax
- [Michel Fortin's Markdown extra](https://michelf.ca/projects/php-markdown/extra/) The library used in W.


#### W specific syntax

##### Quick links

    [[<page_id>]]

Will generate :

    <a href="ID" title="DESCRIPTION" class="internal page">TITLE</a>

Where `DESCRIPTION`, `TITLE` and `ID` are the [description](#description) and [title](page-title) of the page related to the [id](#page-id).

Note that the `.internal` class have been added to the `a` html link, allowing you to differenciate internal link styling from `.external`.























### Inclusions

Inclusion is an advanced feature of W that allow editors to insert metadatas, generate content using codes. Those codes are always inside percent signs `%`.



#### Basic inclusions

Those codes are mainly used to print page's metadatas.

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

You have two options to print the page ID.

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
- time : display the [time](#time) of the page.
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

##### Linkto in templates

A specific case exist when if you want to use the [linkto](#linkto) filter in templates: If you want to list all the pages that point to the current page, you can use the wildcard character `*` as a value.

    %LIST?linkto=*%

##### styling

Some `.class` and `#id` are generated to help styling.

`.public`, `.private`, `not_published` are used to define the privacity level of pages.

`.current_page` is used to highlight the current page.

`#<pageid>` is set to each item to allow selective styling.





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

##### Linkto in templates

A specific case exist when if you want to use the [linkto](#linkto) filter in templates: If you want to create a selection of all the pages that point to the current page, you can use the wildcard character `*` as a value.

    %RANDOM?linkto=*%



#### Media list

Just like [page lists](#page-list), media list are used to generate a list of media based on filters.







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
The default behavior can be set globaly in the [admin panel](#admin).

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

URL Linker is a tool that will transform a plain text URL into a link. This can be enabled or disabled specificly for each elements. The default behavior can be set globaly in the [admin panel](#admin).

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

    w.page.id               current page ID
    w.page.title            current page title
    w.page.description      current page description
    w.page.secure           current page secure level (0: public, 1: private, 2: not published)
    w.domain                domain name URL
    w.basepath              folderpath after which W is installed
    w.user.id               current user ID
    w.user.name             current user name
    w.user.level            current user level (0: visitor to 10: admin)








### Templating

There is no particular template document, each page can be used as a template.
There is 3 types of templating in W :

#### BODY template

This will call the BODY of another page.

#### CSS template

CSS template allow you to link another page's stylesheet to your page.

Options are :

##### Recursive template

If templated page is already templating another page, this will add it to the stylesheet links.
If you don't want this recursivity, uncheck the option.

This option is checked by default.

##### External CSS

If templated page is using external stylesheet, this will include them as well.

This option is checked by default.


##### Favicon

Use this option if you want to use templated page favicon.

#### Javascript template

Use this if you want to use a javascript template.







Media management
----------------

In addition to [page editing](#page-editing), W gives you a dedicated tool to manage all the files you want to include in your website.

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


#### CSS folder

This contain two files:

- the `global.css` file, that admins can edit in the [admin panel](#admin).
- the `fonts.css` file, that is automatcly generated according to content of [fonts folder](#fonts-folder).



#### Favicon folder

This folder is supposed to contain favicons. Once favicon files (that can be `.ico`, `.png`, etc.) are uploaded here, they are listed in the favicon selection drop down list in every pages or in the admin panel.


#### Thumbnail folder

This folder is supposed to contain thumbnails images. Once images files (that can be `.jpg`, `.png`, etc.) are uploaded here, they are listed in the thumbnail selection drop down list in every pages or in the admin panel.




Administration
--------------

### Theming

You change the interface theme in the [admin panel](#admin), under *interface*. There is a kit of presets, but you can even add your own themes ! And it is actualy *super easy* 😎. To do so, go to the folder `/assets/css/themes/` and duplicate one of the preset you prefer. Then you can edit the CSS variables to quickly stylise your theme colors.





References
---------


### Page structure


Technicaly, each page is stored as a JSON object in yout fatabase folder.

A page consist of meta informations and contents.

#### Meta infos

##### Page ID

__The unique identifier of a page__. It can only contain lowercases characters from `a-z`, numbers `0-9`, underscore `_` and hyphen `-`.
Normaly W will take care of cleaning your pages's ID, by lowering uppercases, removing some accents, and remplacing special characters or spaces with hyphens.

##### Page title

The page title is also very important. It's like the official name of a page. It will be displayed in the browser tab of your page.

##### Description

The description will be used by web search engines when they display lists of pages. This is also usefull for social media sharing.

##### Tag

Tags are very powerfull to help you organize your pages. As in __W__, there is no hierarchy between pages, this is the only tool to create groups of pages.

Tags can be set in the [left panel of the editor interface](#left-panel) using comma to separate tags, or by using the multi edit tool in the [Home menu bar > Edit](#home-menu).

##### Privacy

Pages have 3 possible levels of privacy:

- **public** Free to read for every [visitor](#visitor).
- **private** Hidden to [visitor](#visitor), but accessible to [reader users](#reader).
- **not published** Only readable for users allowed to edit the page.

##### Date

This metadata can be set manually by the page editors.

By default, page's date and time are the same as creation date and time.

##### Datemodif

You can't edit manualy this date. It's just the last time the page has been edited.

##### Latitude

Must be between -90 and 90.

##### Longitude

Must be between -180 and 180.

##### Thumbnail

The thumbnail have two use cases :

- When you share a link to a page on a social network
- When you generate a [list of page](#page-list) and activated the `thumbail` option. 

##### Authors

List of [users](#user-levels) that have edited the page, or that can do it. You have to be at least [super-editor](#super-editor) to add or remove authors.

##### Linkto

This is not an editable metadata. It's a list of all pages that are linked from the current page. This is analysed when the page is rendered.

#### Content

##### content elements

**In pages V1**, there are 5 content elements : Main, Nav, Aside, Header, Footer.

**In pages V2**, there is only one content element called "content".

##### CSS

Each page have a dedicated stylesheet, that can be called by other pages using [templating](#templating).

##### BODY

##### Javascript


##### Visitcount

*read only*

    visitcount

Count the number of visitors that opened the page. Unlike [displaycount](#displaycount), only un-connected users are counted this way.

##### Editcount

*readonly*

    editcount

Count the number of time the page has been edited. Even empty edits are counted.

##### Displaycount

*readonly*

    displaycount

Count the number of time the page is opened is opened.






### User levels

#### Visitor

> level : 0

#### Reader

> level : 1

Reader users are allowed to read [private pages](#privacy).

#### Invite Editor

> level : 2

Invite Editors are the lowest editor status possible. They can only access the [Edition interface](#edition). They can't create page but only edit page when listed as [author](#authors).

#### Editor

> level : 3

- Can create pages and edit them.
- Can only edit pages when listed as [author](#authors).
- Can only delete pages if they are the only author.

#### Super Editor

> level : 4

- Can edit any pages they like.
- Can manage users as [author](#authors) of a page.
- Can use the home menu and media menu to access powerfull features.
- Can delete any page.
- Have access to mass edit features.

#### Administrator

> level : 10

- Can access the [admin interface](#admin).
- Can create new users.

### URL based command interface

#### Pages commands

Type thoses commands after a __page_id__ 

    <page_id>/<COMMAND>

##### /add

Command used to add a page in the database.

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

##### /login

If you are not connected yet, this will ask you for credentials.


##### /logout

If you were connected, this will disconnect you.

#### Home commands



##### //renderall

Render all pages in the database



