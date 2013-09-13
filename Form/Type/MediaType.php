<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tms\Bundle\MediaClientBundle\Form\DataTransformer\FileToMediaReferenceTransformer;

class MediaType extends AbstractType
{
    /**
     * @var mediaApiClient
     */
    private $mediaApiClient;

    /**
     * Constructor
     *
     * @param RestApiClientInterface $mediaApiClient
     */
    public function __construct($mediaApiClient)
    {
        $this->mediaApiClient = $mediaApiClient;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FileToMediaReferenceTransformer($this->mediaApiClient);
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
