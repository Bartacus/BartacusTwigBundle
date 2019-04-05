.. _getting-started-configuration:

=============
Configuration
=============

The bundle itself doesn't need any configuration.

Configure backend layout
========================

Add a simple backend layout to your PageTs config. The naming of the backend layout (here: ``default``) is important for the next steps.

.. code-block:: text

    mod {
        web_layout {
            BackendLayouts {
                default {
                    title = Default
                    config {
                        backend_layout {
                            colCount = 1
                            rowCount = 1
                            rows {
                                1 {
                                    columns {
                                        1 {
                                            name = Content
                                            colPos = 0
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


TypoScript template
===================

In your page settings a ``TWIGTEMPLATE`` content object is available to render your twig templates as page layouts. The example loads the twig template from ``layouts/`` with the given backend layout name, e.g. ``layouts/default.html.twig``.

.. code-block:: typoscript

    page {
        10 = TWIGTEMPLATE
        10 {
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

            variables {
                page_title = TEXT
                page_title.data = page:title
            }
        }
    }

An example layout could look like the following. The ``body`` block is required.

.. code-block:: twig

    {% block body %}
        <main>
            <h1>{{ page_title }}</h1>
            <!--TYPO3SEARCH_begin-->
            {% block content %}
                {{ bartacus_cobject('lib.dynamicContent', 0) }}
            {% endblock %}
            <!--TYPO3SEARCH_end-->
        </main>
    {% endblock %}

.. _getting-started-configuration-cobject:

Render page content
===================

To render arbitrary page content like above with the ``bartacus_cobject`` function you need to add a ``lib.dynamicContent`` helper to your TypoScript:

.. code-block:: typoscript

    ###############################################
    #### DYNAMIC CONTENT LIB FOR USAGE IN TWIG ####
    ###############################################
    #
    #  EXAMPLE WITH FULL CONFIG
    #  -----------------------------
    #  {{ bartacus_cobject('lib.dynamicContent', {
    #      pageUid: data.uid,
    #      colPos: 0,
    #      slide: 0,
    #      wrap: '<div class="hero">|</div>',
    #      elementWrap: '<div class="element">|</div>',
    #  }) }}
    #
    #  EXAMPLE WITH COLPOS ONLY
    #  -----------------------------
    #  {{ bartacus_cobject('lib.dynamicContentSlide', 2) }}
    #
    #################
    lib.dynamicContent = COA
    lib.dynamicContent {
        5 = LOAD_REGISTER
        5 {
            colPos.cObject = TEXT
            colPos.cObject {
                field = colPos
                ifEmpty.cObject = TEXT
                ifEmpty.cObject {
                    value.current = 1
                    ifEmpty = 0
                }
            }

            slide.cObject = TEXT
            slide.cObject {
                override {
                    field = slide
                    if {
                        isInList.field = slide
                        value = -1, 0, 1, 2
                    }
                }

                ifEmpty = 0
            }

            pageUid.cObject = TEXT
            pageUid.cObject {
                field = pageUid
                ifEmpty.data = TSFE:id
            }

            contentFromPid.cObject = TEXT
            contentFromPid.cObject {
                data = DB:pages:{register:pageUid}:content_from_pid
                data.insertData = 1
            }

            wrap.cObject = TEXT
            wrap.cObject {
                field = wrap
            }

            elementWrap.cObject = TEXT
            elementWrap.cObject {
                field = elementWrap
            }
        }

        20 = CONTENT
        20 {
            table = tt_content
            select {
                includeRecordsWithoutDefaultTranslation = 1
                orderBy = sorting
                where = {#colPos}={register:colPos}
                where.insertData = 1
                pidInList.data = register:pageUid
                pidInList.override.data = register:contentFromPid
            }

            slide = {register:slide}
            slide.insertData = 1
            renderObj {
                stdWrap {
                    dataWrap = {register:elementWrap}
                    required = 1
                }
            }

            stdWrap {
                dataWrap = {register:wrap}
                required = 1
            }
        }

        90 = RESTORE_REGISTER
    }

    lib.dynamicContentSlide =< lib.dynamicContent
    lib.dynamicContentSlide.20.slide = -1
