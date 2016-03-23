QPautratWoohoolabsYinBundle
==========================

Implements `woohoolabs/yin`_ framework into Symfony.

Installation
------------

.. code-block:: bash

    $ composer require qpautrat/woohoolabs-yin-bundle

Then, like for any other bundle, include it in your Kernel class:

.. code-block:: php

    public function registerBundles()
    {
        $bundles = array(
            // ...

            new QPautrat\WoohoolabsYinBundle\QPautratWoohoolabsYinBundle(),
        );

        // ...
    }

Usage
-----

Use ``qpautrat_woohoolabs_yin.json_api`` service:

.. code-block:: php

    namespace AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class DefaultController extends Controller
    {
        public function helloAction()
        {
            $jsonApi = $this->container->get('qpautrat_woohoolabs_yin.json_api');

            return $response = $jsonApi->respond()->ok(new HelloDocument(), 'hello');
        }
    }

If you installed `sensio/framework-extra-bundle`_ you can use ``ParamConverter``:

.. code-block:: php

    namespace AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use WoohooLabs\Yin\JsonApi\JsonApi;

    class DefaultController extends Controller
    {
        public function helloAction(JsonApi $jsonApi)
        {
            return $response = $jsonApi->respond()->ok(new HelloDocument(), 'hello');
        }
    }

.. _`woohoolabs/yin`: https://github.com/woohoolabs/yin
.. _`sensio/framework-extra-bundle`: https://github.com/sensiolabs/SensioFrameworkExtraBundle