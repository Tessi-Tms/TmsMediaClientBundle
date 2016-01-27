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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tms\Bundle\MediaClientBundle\Model\Media;

class TmsTransformableImageUploadType extends TmsMediaUploadType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($options) {
                $form = $event->getForm();
                $data = $event->getData();
                $metadata = array(
                    'cropper_x'      => 0,
                    'cropper_y'      => 0,
                    'cropper_width'  => null,
                    'cropper_height' => null,
                    'cropper_zoom'   => 1,
                    'cropper_rotate' => 0,
                );

                if ($data instanceof Media) {
                    $metadata = $data->getMetadata();
                }

                $form
                    ->add('cropper_x', 'hidden', array(
                        'required' => false,
                        'data'     => $metadata['cropper_x'],
                    ))
                    ->add('cropper_y', 'hidden', array(
                        'required' => false,
                        'data'     => $metadata['cropper_y'],
                    ))
                    ->add('cropper_width', 'hidden', array(
                        'required' => false,
                        'data'     => $metadata['cropper_width'],
                    ))
                    ->add('cropper_height', 'hidden', array(
                        'required' => false,
                        'data'     => $metadata['cropper_height'],
                    ))
                    ->add('cropper_zoom', 'hidden', array(
                        'required' => false,
                        'data'     => $metadata['cropper_zoom'],
                    ))
                    ->add('cropper_rotate', 'hidden', array(
                        'required' => false,
                        'data'     => $metadata['cropper_rotate'],
                    ))
                ;
            },
            100
        );
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
