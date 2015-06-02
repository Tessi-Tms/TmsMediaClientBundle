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
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Tms\Bundle\MediaClientBundle\StorageProvider\TmsMediaStorageProvider;

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
     * Constructor
     *
     * @param TmsMediaStorageProvider $mediaClientManager
     * @param ValidatorInterface $validator
     */
    public function __construct(TmsMediaStorageProvider $storageProvider, ValidatorInterface $validator)
    {
        $this->storageProvider = $storageProvider;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publicUri', 'hidden', array(
                'required' => false
            ))
            ->add('mimeType', 'hidden', array(
                'required' => false
            ))
            ->add('providerReference', 'hidden', array(
                'required' => false
            ))
            /*->add('toDelete', 'checkbox', array(
                'label'    => 'X',
                'required' => false,
                'mapped'   => false
            ))*/
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($options) {
                $isUploadedFileRequired = $options['required'];
                $form = $event->getForm();
                if (null !== $event->getData()) {
                    $isUploadedFileRequired = false;
                }
                $form->add('uploadedFile', 'file', array(
                    'label'    => ' ',
                    'required' => $isUploadedFileRequired
                ));
            }
        );

        $provider = $this->storageProvider;
        $validator = $this->validator;
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($provider, $validator) {
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'     => 'Tms\Bundle\MediaClientBundle\Model\Media',
            'error_bubbling' => false,
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
