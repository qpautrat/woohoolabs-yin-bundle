QPWoohoolabsYinBundle
==========================

Implements `woohoolabs/yin`_ framework into Symfony.

Installation
------------

Note: `5.x` is for Yin `4.x`, `4.x` is for Yin `3.x` and `3.x` is for Yin `0.11`

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

Configure service binding:

.. code-block:: yaml

    services:
        _defaults:
            ...
            bind:
                $jsonApi: '@qp_woohoolabs_yin.json_api'

Then you can use ``qp_woohoolabs_yin.json_api`` service by injecting it in the constructor:


.. code-block:: php

    namespace App\Controller;

    use Psr\Http\Message\ResponseInterface;
    use WoohooLabs\Yin\JsonApi\JsonApi;

    class DefaultController
    {
        /**
         * @var JsonApi
         */
        private $jsonApi;

        public function __construct(JsonApi $jsonApi)
        {
            $this->jsonApi = $jsonApi;
        }

        public function index(): ResponseInterface
        {
            return $this->jsonApi->respond()->ok(new HelloDocument(), 'hello');
        }
    }


Or in the action method directly:

.. code-block:: php

    namespace App\Controller;

    use Psr\Http\Message\ResponseInterface;
    use WoohooLabs\Yin\JsonApi\JsonApi;

    class DefaultController
    {
        public function index(JsonApi $jsonApi): ResponseInterface
        {
            return $jsonApi->respond()->ok(new HelloDocument(), 'hello');
        }
    }
                

.. _`woohoolabs/yin`: https://github.com/woohoolabs/yin
.. _`ExceptionFactory`: https://github.com/woohoolabs/yin#exceptions
