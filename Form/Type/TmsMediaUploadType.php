<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tms\Bundle\MediaClientBundle\StorageProvider\TmsMediaStorageProvider;
use Tms\Bundle\MediaClientBundle\Model\Media;

class TmsMediaUploadType extends AbstractType
{
    /**
     * @var TmsMediaStorageProvider
     */
    private $storageProvider;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param TmsMediaStorageProvider $storageProvider
     * @param ValidatorInterface      $validator
     */
    public function __construct(TmsMediaStorageProvider $storageProvider, ValidatorInterface $validator)
    {
        $this->storageProvider = $storageProvider;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$view->vars['required']) {
            return;
        }

        $media = $view->vars['data'];

        if ($media instanceof Media && null !== $media->getPublicUri()) {
            $view->vars['required'] = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                function ($data) {
                    $decodedData = $data;
                    if (is_string($decodedData)) {
                        // Decode the data
                        $decodedData = json_decode($decodedData, true);
                    }

                    if (is_array($decodedData)) {
                        // Create a new instance of Media
                        $media = new Media();
                        if (isset($decodedData['providerName'])) {
                            $media->setProviderName($decodedData['providerName']);
                        }
                        if (isset($decodedData['providerReference'])) {
                            $media->setProviderReference($decodedData['providerReference']);
                        }
                        if (isset($decodedData['publicUri'])) {
                            $media->setPublicUri($decodedData['publicUri']);
                        }
                        if (isset($decodedData['extension'])) {
                            $media->setExtension($decodedData['extension']);
                        }
                        if (isset($decodedData['mimeType'])) {
                            $media->setMimeType($decodedData['mimeType']);
                        }

                        return $media;
                    }

                    return $data;
                },
                function ($data) {
                    return $data;
                }
            ))
            ->add('publicUri', Types\HiddenType::class, array(
                'required' => false,
            ))
            ->add('mimeType', Types\HiddenType::class, array(
                'required' => false,
            ))
            ->add('providerReference', Types\HiddenType::class, array(
                'required' => false,
            ))
            /*->add('toDelete', Types\CheckboxType::class, array(
                'label'    => 'X',
                'required' => false,
                'mapped'   => false
            ))*/
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($options) {
                $isUploadedFileRequired = $options['required'];
                $form = $event->getForm();
                if (null !== $event->getData()) {
                    $isUploadedFileRequired = false;
                }
                $form->add('uploadedFile', Types\FileType::class, array(
                    'label' => ' ',
                    'required' => $isUploadedFileRequired,
                ));

                foreach ($options['metadata'] as $key => $value) {
                    $form->add($key, Types\HiddenType::class, array(
                        'required' => false,
                        'data' => $value,
                    ));
                }
            },
            50
        );

        $provider = $this->storageProvider;
        $validator = $this->validator;
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($provider, $validator) {
                $form = $event->getForm();
                $violations = $validator->validate($form);
                if (count($violations) > 0) {
                    return false;
                }

                $media = $form->getData();
                if (null === $media) {
                    return false;
                }
                /*if ($form->get('toDelete')->getData()) {
                    $provider->remove($media);
                } else {*/
                    $provider->add($media);
                //}
            },
            500
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Tms\Bundle\MediaClientBundle\Model\Media',
                'error_bubbling' => false,
                'metadata' => array(),
            ))
            ->setAllowedTypes('metadata', array('array'))
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tms_media_upload';
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
