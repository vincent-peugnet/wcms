USER MANUAL
===========

Introduction
------------

Welcome to __W__, this manual is here to help you get the best of this tool.

If it's you're first time using it, you should learn how to [create your first page](#create-your-first-page).

If you already know the basics, you may want to check the references :

- Discover how to [navigate](#navigation).
- Get to know the [structure of a page](#page-structure) to edit meta content.
- Learn more about the [URL based commands](#url-based-command-interface) you can type in the adress bar.
- Master the [render engine](#page-editing) to release the full potential of __W__.



### Create your first page

This 3 steps tutorial will introduce you to the basic __W__ moves.

#### Add a new page


The first thing you have to do before creating a page is to **choose an adress** for this page. Each page have a unique adress that identify it, it's the [**page id**](#page-id). You can use the adress bar of your web browser to directly access, [edit](#edit) or [delete](#delete) a page.

You can type anything in your adress bar, if it's an already exisiting page_id, you will access a page, otherwise, you will arrive to an empty space waiting to be filled.

Once you've typed an adress and found nothing, if you are connected, you now have the opportunity to create a page at this adress.

There is two ways to do this :

1. Graphicaly, by clicking the "create" button
2. Using the adress bar, by typing `/add` just after the adress

There it is ! Congratulation, you've created your first page using W.


#### Edit your page content

Now you should be in front of the [edit interface](#editor) of your page. If you check the adress bar, you will now see `/edit` written after your page adress.

You can type a few words in the main text area. Once you're happy with what you wrote, it's time to save your work : click on the <kbd>update</kbd> button in the top left corner. You can use the shortcut <kbd>CTRL</kbd> + <kbd>S</kbd> too if you prefer. After you've done this, you sould have notice that the asterix `*` next to your page_id have left, it's the sign that saving was sucesfull.

Let's come back to the page reading view to see the result of our editing. There are different approaches for doing so :

1. By removing the `/edit` command after your page adress
2. Pressing the "display" button (next to "update"), or using <kbd>CTRL</kbd> + <kbd>D</kbd> shortcut.

The first method will use the same tab while the second will create a new one, wich is usefull if you want to keep your [edit interface](#editor) open to come back quickly.

You should now see something that look like what you've typed.


#### Create a hyperlink


The [markdown synthax](), this is a formating standard well known accross the web. It's main goal is to be a intermediate between an easy to read prose text, and the internet standard tag language, the HTML. One of the main interest of Markdown, is it's ability to be mixed with HTML. That way, people can type more easily basic text, and switch to HTML at any time, to achieve a more complex layout. You can use the official Markdonw synthax with __W__ alongside with HTML to build your pages.

[Official website describing Markdown synthax](https://daringfireball.net/projects/markdown/syntax)

One of the most interesting things to do when you use internet publishing, is to create HYPERLINKS. __W__ encourage you creating those links between pages using a very light synthax. There are multiple ways to do it.

1. Using classic HTML : `<a href="<page_id>">click here</a>`
2. Using Markdown :  `[click here](<page_id>)`
3. Using W quick synthax : `[<page_id>]`

All those methods will create a link pointing to the `<page_id>` you've given.

Those kind of links are called internal links beccause they stay inside of your domain. To set a link outside of your website, simply remplace `<page_id>` by


### Navigation

Discover the differents interfaces of __W__.

#### Home

The Home interface is the main view of your project. You can access it only when you're connected. All the pages of your database are listed here.

The [readers](#reader) and [invite editors](#invite-editor) can't access this interface.

##### Home menu

In the home menu [super editors and above](#super-editor) can :

- File : import pages as JSON file. (usefull for transfering pages from a W instance to another)
- Edit : 

The Home view is divided in two main parts :

##### Options

 where you can apply filters and sorting

##### Pages

the list of pages after filtering

#### Edition

The edition interface is accessible when typing `/edit` after an existing [page_id](#page-id) in the adress bar. Or from the [home](#home), by clicking on the pencil button.

[Invite editors](#invite-editor) and [editors](#editor) users can only access edition of pages where they are listed as [author](#authors).

The Edition view is composed of a main content edition area, surounded by two lateral panels that you can deploy or hide.

##### left panel

This is the 

- Meta infos : edit [title](#title), [description](#description) and more infos about your page.
- Templating
- Advanced
- Help : a quick synthax reference for markdown and W synthax

##### contents area

Each tab allow you to edit a different type of content :

[main](#main), [nav](#nav), [aside](#aside)

Note that the MAIN tab is the default opened tab, this is meant to be the most spontaneous place to store information when you are using __W__ in *note taking style*.

##### right panel

#### Media manager

When you need to use images, sound or videos in your pages, or any other type of files, you can use the media manager to host them before including them in your pages.

[Invite editors](#invite-editor) don't have access to this place.

##### Media menu

The media menu allow you to do more powerfull function like moving medias or delete folders.

#### Admin

#### User manager









Page Editing
------------

### Synthax

#### Markdown

- [Daring Fireballs](https://daringfireball.net/projects/markdown/syntax) Official reference of Markdown Synthax
- [Michel Fortin's Markdown extra](https://michelf.ca/projects/php-markdown/extra/) The library used in W.


#### W specific synthax

##### Auto url

When you use an adress starting with `http://` or `https://`, W will automaitcaly create a link pointing to a new tab.

    <a href="<url>" target="_blank" class="external"><url></a>

Note that the `.external` class have been added.

##### Quick links

    [<page_id>]

Will generate :

    <a href="<page_id>" title="PAGE_DESCRIPTION" class="internal">PAGE_TITLE</a>

Where [PAGE_DESCRIPTION](#description) and [PAGE_TITLE](page-title) are the [page_id](#page-id)'s meta infos.

Note that the `.internal` class have been added to the `a` html link, allowing you to differenciate internal link styling from `.external`.

### Inclusions

#### Basic inclusions

##### Title inclusion

    %TITLE%

This will include the page [title](#page-title).

##### Description inclusion

    %DESCRIPTION%

This will include the page [description](#description).

##### Date inclusion

    %DATE%


#### Page list

The page list

#### Media list

### Templating

### Content insertion

#### Advanced BODY synthax






References
---------


### Page structure


Technicaly, each page is stored as a JSON object in yout fatabase folder.

A page consist of meta informations and contents.

#### Meta infos

##### Page ID

The unique identifier of a page.

##### Page title

The page title is also very important. It's like the official name of a page.

##### Description

##### Tag

Tags are very powerfull to help you organize your pages. As in __W__, there is no hierarchy between pages, this is the only tool to create groups of pages.

Tags can be set in the [left panel of the editor interface](#left-panel) using comma to separate tags.

##### Date & time

##### Thumbnail

The thumbnail have two use cases :

- When you share a link to a page on a social network
- When you generate a [list of page](#page-list) and activated the `thumbail` option. 

##### Authors

List of [users](#user-levels) that have edited the page, or that can do it. You have to be at least [super-editor](#super-editor) to add or remove authors.

#### Content

##### Main, Nav, Aside, Header, Footer

##### CSS

##### BODY

##### Javascript





### User levels

#### Visitor

> level : 0

#### Reader

> level : 1

#### Invite Editor

> level : 2

Invite Editors are the lowest editor status possible. They can only access the [Edition interface](#edition). They can't create page but only edit page when listed as [author](#authors).

#### Editor

> level : 3

- Can create pages and edit them.
- Can only edit pages when listed as [author](#authors)

#### Super Editor

> level : 4

- Can edit any pages they like (they will be added as [author](#authors) of the page).
- Can use the home menu and media menu to access powerfull features.

#### Administrator

> level : 10

- Can access the [admin interface](#admin).
- Can create new users.

### URL based command interface

#### Pages commands

Type thoses commands after a __page_id__ 

`<page_id>/COMMAND`

##### /add

Command used to add a page in the database.

##### /edit

Command used to edit a page. If you're not logged in, it will ask for your credentials.

##### /delete

Command used to delete a page from the database. This will ask you for a confirmation.

##### /render

Force the rendering of a page.

##### /log

Show a `var_dump` of the page object. This could be usefull for debbuging.


