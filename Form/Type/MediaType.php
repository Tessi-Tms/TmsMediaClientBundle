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
    private $mediaClient;

    /**
     * Constructor
     *
     * @param MediaClientManager $mediaClient
     */
    public function __construct(MediaClientManager $mediaClient)
    {
        $this->mediaClient = $mediaClient;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FileToMediaReferenceTransformer($this->mediaClient);
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
