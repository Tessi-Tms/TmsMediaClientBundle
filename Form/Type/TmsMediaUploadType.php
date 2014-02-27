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
use Symfony\Component\Validator\Constraints\Null;
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
                'label'          => ' ',
                'required'       => $isUploadedFileRequired,
                'error_bubbling' => true,
                'constraints'    => $options['constraints']
            ))
        ;

        $provider = $this->storageProvider;
        $validator = $this->validator;
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($provider, $validator) {
                $form = $event->getForm();
                $violations = $validator->validate($form);
                foreach ($violations as $violation) {
                    if($violation->getPropertyPath() == "children[uploadedFile].data") {
                        return false;
                    }
                }
                $media = $form->getData();
                if (null === $media) {
                    return false;
                };
                $provider->add($media);
            },
            500
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
