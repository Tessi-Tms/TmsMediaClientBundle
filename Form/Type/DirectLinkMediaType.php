<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tms\Bundle\MediaClientBundle\StorageProvider\TmsMediaStorageProvider;
use Tms\Bundle\MediaClientBundle\Form\DataTransformer\FileToMediaReferenceTransformer;

class DirectLinkMediaType extends AbstractType
{
    /**
     * @var TmsMediaStorageProvider
     */
    private $storageProvider;

    /**
     * Constructor
     *
     * @param TmsMediaStorageProvider $mediaClientManager
     */
    public function __construct(TmsMediaStorageProvider $storageProvider)
    {
        $this->storageProvider = $storageProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FileToMediaReferenceTransformer($this->storageProvider);
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'direct_link_media';
    }
}
