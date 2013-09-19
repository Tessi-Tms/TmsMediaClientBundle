<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('providerName', 'provider_choices')
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
                'required' => false
            ))
            ->add('uploadedAt', 'datetime', array(
                'data'       => $builder->getData() ? new \DateTime('now') : null,
                'attr'       => array('style'=>'display:none;'),
                'label_attr' => array('style'=>'display:none;'),
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tms\Bundle\MediaClientBundle\Entity\Media'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tms_bundle_mediaclientbundle_mediatype';
    }
}
