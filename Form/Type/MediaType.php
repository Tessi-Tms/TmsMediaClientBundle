<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
            function(FormEvent $event) use ($handler) {
                $media = $event->getForm()->getData();
                $provider = $handler->getStorageProvider($media->getProviderName());
                if (!$provider->add($media)) {
                    throw new MediaClientException(sprintf('The media "%s" was not created', $media));
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'media';
    }
}
