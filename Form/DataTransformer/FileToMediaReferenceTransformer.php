<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tms\Bundle\MediaClientBundle\StorageProvider\TmsMediaStorageProvider;
use Tms\Bundle\MediaClientBundle\Entity\Media;

class FileToMediaReferenceTransformer implements DataTransformerInterface
{
    /**
     * @var TmsMediaStorageProvider
     */
    private $storageProvider;

    /**
     * Constructor
     *
     * @param TmsMediaStorageProvider $storageProvider
     */
    public function __construct(TmsMediaStorageProvider $storageProvider)
    {
        $this->storageProvider = $storageProvider;
    }

    /**
     * Transforms a string (Media reference) to a null object.
     *
     * @param string $mediaReference
     * @return null
     */
    public function transform($mediaReference)
    {
        return null;
    }

    /**
     * Transforms a file (UploadedFile) to a string (Media reference).
     *
     * @param  UploadedFile $file
     * @return string
     * @throws TransformationFailedException if problem occured with the media api.
     */
    public function reverseTransform($file)
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        try {
            $media = new Media();
            $media->setUploadedFile($file);
            $media->upload();
            $this->storageProvider->add($media);

            return json_encode($media->getProviderData());
        } catch (\Exception $e) {
            throw new TransformationFailedException(sprintf(
                'An error occured during File to media transformation: %s',
                $e->getMessage()
            ));
        }
    }
}
