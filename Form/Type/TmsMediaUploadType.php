<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Null;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tms\Bundle\MediaClientBundle\StorageProvider\TmsMediaStorageProvider;

class TmsMediaUploadType extends AbstractType
{
    /**
     * @var TmsMediaStorageProvider
     */
    private $storageProvider;

    /**
     * Constructor
     *
     * @param TmsMediaStorageProvider $mediaClientManager
     */
    public function __construct(TmsMediaStorageProvider $storageProvider)
    {
        $this->storageProvider = $storageProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isUploadedFileRequired = $options['required'];

        if (null !== $builder->getData() && $builder->getData()->getProviderReference()) {
            $isUploadedFileRequired = false;
        }

        $builder
            ->add('url', 'hidden', array(
                'required' => false
            ))
            ->add('mimeType', 'hidden', array(
                'required' => false
            ))
            ->add('providerReference', 'hidden', array(
                'required' => false
            ))
            ->add('uploadedFile', 'file', array(
                'label'    => ' ',
                'required' => $isUploadedFileRequired
            ))
        ;

        $provider = $this->storageProvider;
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($provider) {
                $media = $event->getForm()->getData();
                if (null === $media) {
                    return false;
                };
                $provider->add($media);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_merge($view->vars, array(
            'resize'     => $options['resize'],
            'scale'      => $options['scale'],
            'grayscale'  => $options['grayscale'],
            'rotate'     => $options['rotate'],
            'width'      => $options['width'],
            'height'     => $options['height'],
            'maxwidth'   => $options['maxwidth'],
            'maxheight'  => $options['maxheight'],
            'minwidth'   => $options['minwidth'],
            'minheight'  => $options['minheight'],
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tms\Bundle\MediaClientBundle\Model\Media',
            'resize'     => false,
            'scale'      => null,
            'grayscale'  => null,
            'rotate'     => null,
            'width'      => null,
            'height'     => null,
            'maxwidth'   => null,
            'maxheight'  => null,
            'minwidth'   => null,
            'minheight'  => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tms_media_upload';
    }
}
