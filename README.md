TmsMediaClientBundle
====================

Symfony2 bundle client for TmsMediaBundle


Installation
------------

To install this bundle please follow the next steps:

First add the dependency in your `composer.json` file:

```json
"repositories": [
    ...,
    {
        "type": "vcs",
        "url": "https://github.com/Tessi-Tms/TmsMediaClientBundle.git"
    }
],
"require": {
        ...,
        "tms/media-client-bundle": "dev-master"
    },
```

Then install the bundle with the command:

```sh
php composer update
```

Enable the bundle in your application kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        ...
        new Tms\Bundle\MediaClientBundle\TmsMediaClientBundle(),
    );
}
```

Now import the bundle configuration in your `app/config.yml`

```yml
imports:
    ...
    - { resource: @TmsMediaClientBundle/Resources/config/config.yml }
```

Now the Bundle is installed and configured.
