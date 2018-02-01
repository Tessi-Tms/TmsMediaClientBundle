<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tms\Bundle\MediaClientBundle\Form\Type\ProviderChoicesType;

class MediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('providerName', ProviderChoicesType::class)
            ->add('publicUri', Types\HiddenType::class, array(
                'required' => false,
            ))
            ->add('mimeType', Types\HiddenType::class, array(
                'required' => false,
            ))
            ->add('extension', Types\HiddenType::class, array(
                'required' => false,
            ))
            ->add('providerReference', Types\HiddenType::class, array(
                'required' => false,
            ))
            ->add('uploadedFile', Types\FileType::class, array(
                'required' => false,
                'constraints' => $options['constraints'],
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tms\Bundle\MediaClientBundle\Entity\Media',
        ));
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
        return 'tms_bundle_mediaclientbundle_mediatype';
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
