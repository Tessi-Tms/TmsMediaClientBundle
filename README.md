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


How to use
----------

### OneToOne Relation with a media:

In your entity:

```php
/**
 * @var Media
 *
 * @ORM\OneToOne(targetEntity="Tms\Bundle\MediaClientBundle\Entity\Media", cascade={"all"})
 * @ORM\JoinColumn(name="logo_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
 */
private $logo;
```

In this entity form type:

```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ...
        ->add('logo', 'related_to_one_media')
        ...
    ;
}
```

### ManyToMany Relation with a media:

In your entity:

```php
/**
 * @var array<Media>
 *
 * @ORM\ManyToMany(targetEntity="Tms\Bundle\MediaClientBundle\Entity\Media", cascade={"all"})
 * @ORM\JoinTable(name="offer_media",
 *     joinColumns={@ORM\JoinColumn(name="my_entity_id", referencedColumnName="id", onDelete="cascade")},
 *     inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", unique=true, onDelete="cascade")}
 * )
 */
private $images;
```

In this entity form type:

```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ...
        ->add('images', 'related_to_many_media')
        ...
    ;
}
```

### Without media entity relation:

In your entity:

```php

```

In this entity form type:

```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ...
        ->add('image', 'direct_link_media')
        ...
    ;
}
```

