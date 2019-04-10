# W-CMS

**W** was first a tool, based on my way of thinking, as a protesis, to write on ideas and create a self explorating text point n' click game.

It's a mix between a drive, wikipedia and a personnal blog. You can create articles very quickly and share them with other people or keep them for you and restricted people.  
There is no boundaries beetween taking notes and creating a website. When you create a article, you create a space in internet, a place, that can be public or private. Then, you decide to link it or not with the others places you've created before.
There is a lots of possibilites of creations, for people who need to create multi-aspect labyrinth websites, to use a half public/private mindmap, or even interactives fictions.

## Highlights

- The user can never see the whole map of the website : yo can create specific auto-menu for each page if you want.
- Each page, of group of pages can be like a single website, with it's own style. (You can create your mini internet on internet)
- The pages load very quickly as they can be rendered
- Editor interface, the only place where where you can see the master-plan.


Overview
--------

You can access your home following the root folder you've instaled **W**. After connecting, you can see your editor interface.

![home](https://w-cms.top/gif/home.jpg)

Quickly edit pages as if you where taking notes. You need to connect as editor, then just type `../edit` at the end of the page you want to edit ot use the menu.

![edit](https://w-cms.top/gif/edit.jpg)

Then you can se the result.

![read](https://w-cms.top/gif/read.jpg)

There is lot more you can by altering the `BODY`.

![advanced](https://w-cms.top/gif/advanced.jpg)



[Old webpage of the project (in french)](http://vincent.club1.fr/w/?id=w)


# Technology

- almost pure PHP.
- Use MarkDown to edit pages quickly.
- Easy to manage Json 'flat file' database.

using [James Moss's Flywheel Database](https://github.com/jamesmoss/flywheel), [Michel Fortin's Markdown Extra](https://github.com/michelf/php-markdown) and [Plates](https://github.com/thephpleague/plates) as lightweight template engine.



To Do
=====


- Implement Code Mirror as editing interface instead of basic text area
- Locking page system by super editor to prevent editing by the wrong person
- use AJAX request !!
