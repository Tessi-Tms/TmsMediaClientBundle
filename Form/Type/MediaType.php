<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Tms\Bundle\MediaClientBundle\StorageProvider\StorageProviderHandler;
use Tms\Bundle\MediaClientBundle\Exception\MediaClientException;
use Tms\Bundle\MediaClientBundle\Form\MediaType as BaseMediaType;

class MediaType extends BaseMediaType
{
    protected $storageProviderHandler;

    /**
     * {@inheritdoc}
     */
    public function __construct(StorageProviderHandler $storageProviderHandler)
    {
        $this->storageProviderHandler = $storageProviderHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $handler = $this->storageProviderHandler;
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($handler) {
                $media = $event->getForm()->getData();

                try {
                    $provider = $handler->getStorageProvider($media->getProviderName());
                    $provider->add($media);
                } catch (\Exception $e) {
                    throw new MediaClientException(sprintf(
                        'The media "%s" was not created: %s',
                        $media,
                        $e->getMessage()
                    ));
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media';
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
