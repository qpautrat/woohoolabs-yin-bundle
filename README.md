# QPWoohoolabsYinBundle for Symfony

[![Latest Version on Packagist][ico-version]][link-packagist]

This bundle implements [woohoolabs/yin](https://github.com/woohoolabs/yin) library into Symfony framework.

Note: `5.x` is for Yin `4.x`, `4.x` is for Yin `3.x` and `3.x` is for Yin `0.11`

## Installation

```bash
$ composer require qpautrat/woohoolabs-yin-bundle
```

Then for Symfony 3.x and before, like for any other bundle, include it in your Kernel class:

```php
public function registerBundles()
{
    $bundles = array(
        // ...

        new QP\WoohoolabsYinBundle\QPWoohoolabsYinBundle(),
    );

    // ...
}
```

Symfony 4+ will automatically register the bundle.

## Configuration

By default `jsonApi` class is intialized with Yin's [`ExceptionFactory`](https://github.com/woohoolabs/yin#exceptions).
You can provide your own factory implementation.
To do that you have to define which service to use in your global configuration like this:

```yaml
qp_woohoolabs_yin:
    exception_factory: my_exception_factory_service
```

## Usage

Configure service binding:

```yaml
services:
    _defaults:
        #...
        bind:
            WoohooLabs\Yin\JsonApi\JsonApi: '@qp_woohoolabs_yin.json_api'
```

Then you can use `qp_woohoolabs_yin.json_api` service by injecting it in the constructor:


```php
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
```

Or in the action method directly:

```php
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
```

[ico-version]: https://img.shields.io/packagist/v/qpautrat/woohoolabs-yin-bundle.svg
[link-packagist]: https://packagist.org/packages/qpautrat/woohoolabs-yin-bundle
