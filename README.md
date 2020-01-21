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

[User manual](MANUAL.md)


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

# Development informations

If you want to contribute to the project.

## Prerequisites

- PHP >=7.2.0
- PHP extensions : curl mbstring
- [Composer](https://getcomposer.org/)
- _Optionally_ [NPM](https://www.npmjs.com/get-npm)

### Debian and derivatives

    sudo apt install php php-curl php-mbstring composer nodejs

## Install from sources

1.  Clone the git repository.
2.  Install PHP dependencies.

        make vendor

3.  _Optionnally_ install and build JS dependencies to get UI enhancements.

        make build

The last two commands can be run at once using only `make`.

There are 3 different build environments that `make` can use:

-   **`dev`** _when developing the application._  
    It installs every dependencies and builds big but easy-to-debug js bundles.

-   **`prod`** _when installing from sources a deployed production application._  
    It installs every dependencies and builds minified js bundles.

-   **`dist`** _to create the releases' distribution zip._  
    It strips all the development dependencies and the error reporting module and the sourcemaps of the js bundles are hidden. It can also be used for a production environment if the error reports are not used.

The build environment can be set either for each `make` command by changing it in the `.env` file or on a per command basis by adding it at the end of the command (e.g. `make build ENV=prod`).

## JS development

While developing the JS sources it is useful to run webpack in watch mode so that the bundles get built at each file change. To do so, use the following command:

    make watch

## Publish a new release

The release process uses [release-it](https://github.com/release-it/release-it) and uploads sourcemaps to [Sentry](https://sentry.io/). So to create and publish a new release you will need two access tokens:
-   a [GitHub personnal access token](https://github.com/settings/tokens) with `repository` access
-   a [Sentry authentification token](https://sentry.io/settings/account/api/auth-tokens/) with `project:read`, `project:releases` and `org:read` access

saved in a `.env` file like so:

```bash
# .env
GITHUB_TOKEN='<github token value>'
SENTRY_AUTH_TOKEN='<sentry token value>'
```

Then, to make the release, run the following command:

    make release

To only build the release zip, simply run `make dist`. This will create a zip file in `dist/` of the current version.


To Do
=====


- Locking page system by super editor to prevent editing by the wrong person
