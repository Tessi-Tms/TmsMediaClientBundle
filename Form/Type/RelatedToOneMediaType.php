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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RelatedToOneMediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($options) {
                $isUploadedFileRequired = $options['required'];
                $form = $event->getForm();
                if (null !== $event->getData()) {
                    $isUploadedFileRequired = false;
                }
                $form->add('uploadedFile', 'file', array(
                    'label' => ' ',
                    'required' => $isUploadedFileRequired,
                ));

                foreach ($options['metadata'] as $key => $value) {
                    $form->add($key, 'hidden', array(
                        'required' => false,
                        'data' => $value,
                    ));
                }
            },
            50
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults(array(
                'cascade_validation' => true,
                'metadata' => array(),
                'attr' => array(
                    'class' => sprintf('tms_media_client__%s', $this->getName()),
                ),
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
    public function getParent()
    {
        return MediaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'related_to_one_media';
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
