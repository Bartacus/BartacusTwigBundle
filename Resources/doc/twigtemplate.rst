===============================
``TWIGTEMPLATE`` content object
===============================

The ``TWIGTEMPLATE`` content object is a new content object to use in your TypoScript. With this new content object it's possible to render arbitrary twig templates.

.. code-block:: typoscript

    page {
        10 = TWIGTEMPLATE
        10 {
            template = # ..

            settings {
                foo = {$page.foo}
                # ..
            }

            variables {
                page_title = TEXT
                page_title.data = page:title

                # ..
            }
        }
    }

Usage
=====

Your twig template must provide the following blocks:

    * ``body``: Renders the main content into the place where your ``TWIGTEMPLATE`` lives.
    * ``header`` (optional): Renders the header content into the header data of the page renderer
    * ``footer`` (optional): Renders the footer content into the footer data of the page renderer

Beside the configured settings and variables below, the following variables are available in your template:

    * ``data``: Contains the data of the parent content object, e.g. the page
    * ``current``: Contains the current value of the parent content object, defined by the ``currentValKey``

Properties
==========

.. container:: ts-properties

   ============ ================================================================== =========================
   Property     Data Type                                                          Default
   ============ ================================================================== =========================
   `template`_  :ref:`ts-reference:data-type-string` / :ref:`ts-reference:stdwrap` layouts/default.html.twig
   `settings`_  array of :ref:`ts-reference:data-type-string`
   `variables`_ array of :ref:`ts-reference:stdwrap`
   ============ ================================================================== =========================

.. ### BEGIN~OF~TABLE ###

.. _twigtemplate-template:

template
--------

.. container:: table-row

   Property
         template

   Data type
         :ref:`ts-reference:data-type-string` / :ref:`ts-reference:stdwrap`

   Description
         Path to the twig template file. This can be either a fixed string or a :ref:`ts-reference:stdwrap` returning the given template

   Examples
         Using dynamic template based on backend layout:

         .. code-block:: typoscript

              template = TEXT
              template {
                  cObject = TEXT
                  cObject {
                      data = pagelayout
                      required = 1
                      split {
                          token = pagets__
                          cObjNum = 1
                          1.current = 1
                      }
                  }

                  ifEmpty = default
                  stdWrap.wrap = layouts/|.html.twig
              }

.. _twigtemplate-settings:

settings
--------

.. container:: table-row

   Property
         settings

   Data type
         array of :ref:`ts-reference:data-type-string`

   Description
         A simple array of settings which are passed to the template.

         Can be consumed as e.g. ``{{ settings.foo }}`` in your template.

.. _twigtemplate-variables:

variables
---------

.. container:: table-row

   Property
         variables

   Data type
         array of :ref:`ts-reference:stdwrap`

   Description
         An array of variables as :ref:`ts-reference:stdwrap`

         Can be consumed as e.g. ``{{ page_title }}`` in your template.

.. ###### END~OF~TABLE ######
