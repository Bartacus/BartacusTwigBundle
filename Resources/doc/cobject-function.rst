=====================
Twig cObject function
=====================

The twig ``bartacus_cobject`` function allows you to render arbitrary TypoScript objects like you have seen in :ref:`getting-started-configuration-cobject` in the getting started section.

Properties
==========

.. container:: ts-properties

   ======================= ======================= =========================
   Property                Data Type               Default
   ======================= ======================= =========================
   `typoScriptObjectPath`_ string
   `data`_                 array / object / string empty array
   `currentValKey`_        string                  empty string
   `table`_                string                  empty string
   ======================= ======================= =========================

.. ### BEGIN~OF~TABLE ###

.. cobject-typoScriptObjectPath:

typoScriptObjectPath
--------------------

.. container:: table-row

   Property
         typoScriptObjectPath

   Data type
         string

   Description
         The TypoScript setup path of the TypoScript object to render.

   Examples
         Render the page content with default ``colPos = 0``:

         .. code-block:: twig

              {{ bartacus_cobject('lib.dynamicContent') }}

.. cobject-data:

data
----

.. container:: table-row

   Property
         data

   Data type
         array / object / string

   Description
         The data to be used for rendering the cObject. Can be an object, array or string.

         If it's a simple string or numeric value, the data is injected as current value too.

   Examples
         Shorthand definition to render the page content of a given column:

         .. code-block:: twig

              {{ bartacus_cobject('lib.dynamicContent', 2) }}

         Render the page content from a subpage with and wrap it:

         .. code-block:: twig

              {{ bartacus_cobject('lib.dynamicContent', {
                  pageUid: subpage.uid,
                  colPos: 1,
                  slide: 0,
                  wrap: '<div class="hero">|</div>',
                  elementWrap: '<div class="element">|</div>',
              }) }}

.. cobject-currentValKey:

currentValKey
-------------

.. container:: table-row

   Property
         currentValKey

   Data type
         string

   Description
         The key of the value mapped as current in the "data" argument. It will be used when using current=1 in the TypoScript object.


.. cobject-table:

table
-----

.. container:: table-row

   Property
         table

   Data type
         string

   Description
         The table name associated with "data" argument. Typically tt_content or one of your custom tables. This argument should be set if rendering a FILES cObject where file references are used, or if the data argument is a database record.

.. ###### END~OF~TABLE ######
