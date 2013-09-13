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
use Tms\Bundle\MediaClientBundle\Manager\MediaClientManager;

class FileToMediaReferenceTransformer implements DataTransformerInterface
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

    /**
     * Transforms a string (Media reference) to another (URL).
     *
     * @param  string $mediaReference
     * @return File|null
     */
    public function transform($mediaReference)
    {
        if (null === $mediaReference) {
            return null;
        }

        return $this->mediaClient->getFile($mediaReference);
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
            $reference = $this->mediaClient->send($file);
        } catch (\Exception $e) {
            throw new TransformationFailedException(sprintf(
                'An error occured during File to media transformation: %s',
                $e->getMessage()
            ));
        }

        return $reference;
    }
}
