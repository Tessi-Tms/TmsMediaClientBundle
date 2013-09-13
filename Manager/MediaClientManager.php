<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Manager;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaClientManager
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
     * Get MediaClient
     *
     * @return RestApiClientInterface
     */
    public function getMediaApiClient()
    {
        return $this->mediaApiClient;
    }

    /**
     * Get a file from the media manager
     *
     * @param string $mediaReference
     * @return File|null
     */
    public function getFile($mediaReference)
    {
        $tmpMediaDir = sys_get_temp_dir();
        $tmpMediaPath = sprintf('%s/%s', $tmpMediaDir, $mediaReference);

        try {
            $fp = fopen($tmpMediaPath, 'w');
            fwrite($fp, $this->getMediaApiClient()->get('/media/'.$mediaReference));
            fclose($fp);
        } catch(\Exception $e) {
            unlink($tmpMediaPath);

            return null;
        }

        return new File($tmpMediaPath);
    }

    /**
     * Send a file to the media manager
     *
     * @param UploadedFile $file
     * @return string The media reference
     */
    public function send(UploadedFile $file)
    {
        $tmpMediaDir = sys_get_temp_dir();
        $tmpMediaPath = sprintf('%s/%s', $tmpMediaDir, $file->getClientOriginalName());
        $file->move($tmpMediaDir, $file->getClientOriginalName());

        $reference = $this
            ->getMediaApiClient()
            ->post('/media', array('media' => '@'.$tmpMediaPath))
        ;

        unlink($tmpMediaPath);

        return $reference;
    }
    
    /**
     * Get the media url
     *
     * @param string $mediaReference
     */
    public function getMediaUrl($mediaReference)
    {
        return sprintf('%s/media/%s',
            $this->getMediaApiClient()->getEndpointRoot(),
            $mediaReference
        );
    }
}
