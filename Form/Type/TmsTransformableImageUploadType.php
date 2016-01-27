<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TmsTransformableImageUploadType extends TmsMediaUploadType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('cropper_x', 'hidden', array(
                'required' => false,
                'data'     => 0,
            ))
            ->add('cropper_y', 'hidden', array(
                'required' => false,
                'data'     => 0,
            ))
            ->add('cropper_width', 'hidden', array(
                'required' => false,
                'data'     => 0,
            ))
            ->add('cropper_height', 'hidden', array(
                'required' => false,
                'data'     => 0,
            ))
            ->add('cropper_zoom', 'extra_form_range', array(
                'required' => false,
                'label'    => $options['zoom_label'],
                'data'     => 0,
                'attr'     => array(
                    'min'  => 0,
                    'max'  => 10,
                    'step' => 0.1,
                )
            ))
            ->add('cropper_rotate', 'extra_form_range', array(
                'required' => false,
                'label'    => $options['rotate_label'],
                'data'     => 0,
                'attr'     => array(
                    'min'  => -180,
                    'max'  => 180,
                    'step' => 5,
                )
            ))
            ->add('reset', 'button', array(
                'label'  => $options['reset_label'],
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['container_width']  = $options['container_width'];
        $view->vars['container_height'] = $options['container_height'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'container_width'  => 200,
                'container_height' => 200,
                'zoom_label'       => 'Zoom',
                'rotate_label'     => 'Rotate',
                'reset_label'      => 'Reset',
            ))
            ->setAllowedTypes(array(
                'container_width'  => array('integer'),
                'container_height' => array('integer'),
                'zoom_label'       => array('string'),
                'rotate_label'     => array('string'),
                'reset_label'      => array('string'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tms_transformable_image_upload';
    }
}
