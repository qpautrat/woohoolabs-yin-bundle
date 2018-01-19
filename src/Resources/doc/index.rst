QPWoohoolabsYinBundle
==========================

Implements `woohoolabs/yin`_ framework into Symfony.

Installation
------------

Note: `4.x` is for Yin `3.x` and `3.x` ks for Yin `0.11`

.. code-block:: bash

    $ composer require qpautrat/woohoolabs-yin-bundle

Then, like for any other bundle, include it in your Kernel class:

.. code-block:: php

    public function registerBundles()
    {
        $bundles = array(
            // ...

            new QP\WoohoolabsYinBundle\QPWoohoolabsYinBundle(),
        );

        // ...
    }

Configuration
-------------

By default ``jsonApi`` class is intialized with Yin's `ExceptionFactory`_.
You can provide your own factory implementation.
To do that you have to define which service to use in your global configuration like this:

.. code-block:: yaml

    qp_woohoolabs_yin:
        exception_factory: my_exception_factory_service


Usage
-----

Use ``qp_woohoolabs_yin.json_api`` service:

.. code-block:: php

    namespace AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class DefaultController extends Controller
    {
        public function helloAction()
        {
            $jsonApi = $this->container->get('qp_woohoolabs_yin.json_api');

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

You can also use symfony service binding instead of adding `sensio/framework-extra-bundle`_ dependency:

.. code-block:: yaml
    services:
        _defaults:
            ...
            bind:
                $jsonApi: '@qp_woohoolabs_yin.json_api'

.. _`woohoolabs/yin`: https://github.com/woohoolabs/yin
.. _`sensio/framework-extra-bundle`: https://github.com/sensiolabs/SensioFrameworkExtraBundle
.. _`ExceptionFactory`: https://github.com/woohoolabs/yin#exceptions
