USER MANUAL
===========

Introduction
------------

Welcome to __W__, this manual is here to help you get the best of this tool.

If it's you're first time using it, you should learn how to [create your first page](#create-your-first-page).

If you already know the basics, you may want to check the references :

- Discover the differents [interfaces](#interfaces).
- Get to know the [structure of a page]() to edit meta content.
- Learn more about the [commands](#commands) you can type in the adress bar.
- Master the [render engine]() to release the full potential of __W__.



### Create your first page

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


Interfaces
----------

### Home

### Editor

### Media

### Admin

### User


References
---------


### Structure of a page


Technicaly, each page is stored as a JSON object in yout fatabase folder.

A page consist of meta informations and contents.

#### Page ID

The unique identifier of a page.

#### Page title

The page title is also very important. It's like the official name of a page.

#### Description

#### Tag

#### Date & time

#### Thumbnail



### Formating



Controls
--------




### Pages commands

Type thoses commands after a __page_id__ 

`<page_id>/COMMAND`

#### /add

Command used to add a page in the database.

#### /edit

Command used to edit a page. If you're not logged in, it will ask for your credentials.

#### /delete

Command used to delete a page from the database. This will ask you for a confirmation.

#### /render

Force the rendering of a page.

#### /log

Show a `var_dump` of the page object. This could be usefull for debbuging.


### Home commands
