<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tms\Bundle\MediaClientBundle\Form\DataTransformer\FileToMediaReferenceTransformer;
use Tms\Bundle\MediaClientBundle\Manager\MediaClientManager;

class MediaType extends AbstractType
{
    /**
     * @var MediaClientManager
     */
    private $mediaClientManager;

    /**
     * Constructor
     *
     * @param MediaClientManager $mediaClientManager
     */
    public function __construct(MediaClientManager $mediaClientManager)
    {
        $this->mediaClientManager = $mediaClientManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FileToMediaReferenceTransformer($this->mediaClientManager);
        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'media';
    }
}
