<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class FileToMediaReferenceTransformer implements DataTransformerInterface
{
    /**
     * @var RestApiClientInterface
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

    /**
     * Transforms a string (Media reference) to another (URL).
     *
     * @param  string $mediaReference
     * @return string
     */
    public function transform($mediaReference)
    {
        var_dump($mediaReference); die();
        if (null === $mediaReference) {
            return "";
        }

        return 'todo';
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
        var_dump($file); die();
        if (!$file) {
            return null;
        }

        try {
            $reference = $this->mediaApiClient
                ->post('/media')
            ;
        } catch (\Exception $e) {
            throw new TransformationFailedException(sprintf(
                'An error occured during File to media transformation: %s',
                $e->getMessage()
            ));
        }

        return $reference;
    }
}
