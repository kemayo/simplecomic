===========
SimpleComic
===========

Intro
-----

SimpleComic is a fairly feature-light webcomic display script. I wrote
it for a friend so she could start her comic up. I may improve it
further. We will see.

Features
--------

- Multiple comics per day
- Schedule posting of comics in advance
- Masking of comic filenames so scheduled comics can't be
  easily found
- Comic descriptions, alt text, and transcripts
- Optional chapter divisions for comics
- "Rants" as a lightweight blog, with scheduled posting
- Theming system
- Static pages
- Support for the frontpage showing the first comic from the
  most recent day with comics, to allow posting of "issues"

Installing
----------

1. Put it on your webserver. I'd do it as a ``git clone`` of this, so you
   can update it easily, but that's up to you.
2. Create a database and run all of the queries in ``install/setup.sql``
3. Copy ``include/config.default.php`` to ``include/config.php``
4. Edit ``include/config.php`` to contain appropriate values.
5. Visit ``http://url-you-installed-it-on/admin/`` and log in with the
   password from the config file to start using it.

If you want to upload comics through the admin interface, you need to
make sure that the webserver user can write to the directory you choose
for the comic files.

Customizing
-----------

The ``default`` theme is deliberately plain and utilitarian. To make a
new theme, create a new directory in the ``themes`` folder, and change
the ``theme`` setting in ``config.php`` to refer to it.

The bare minimum this folder must contain is a file called ``style.css``,
which will be used instead of the one in ``default``. Any template files
that aren't present in the custom theme will fall back to using the file
in ``default``.

Hopefully there are enough CSS hooks that adjusting ``style.css`` should
get you fairly far without you needing to meddle with the template files.

To create a static page, make a template called ``page_[name-of-page].php``
and it will be displayed when you visit ``http://[url]/[name-of-page]``.
I'd recommend using the examples from the existing page templates as a
base for static pages, as they get a lot of page-setup done for you.

Upgrading
---------

If you follow the customization advice above, upgrading is really easy.

Just copy a new version over your old install. Your config and custom
templates will be left untouched. You might need to edit your templates
to take advantage of new features.

If you've made changes to the core files or the default templates, you
are on your own. Read the changelogs, would be my recommendation.

If you followed my advice about ``git clone``, just do ``git pull``.

Todo
----

- Chapter navigation could be improved
- Rant commenting
- Calendar displays that you don't have to write yourself in themes