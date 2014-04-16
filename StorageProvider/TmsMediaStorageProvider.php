<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\StorageProvider;

use Da\ApiClientBundle\Exception\ApiHttpResponseException;
use Tms\Bundle\MediaClientBundle\Model\Media;

class TmsMediaStorageProvider implements StorageProviderInterface
{
    /**
     * @var RestApiClientInterface
     */
    private $mediaApiClient;

    /**
     * @var string
     */
    private $sourceName;

    /**
     * Constructor
     *
     * @param RestApiClientInterface $mediaApiClient
     * @param string $sourceName
     */
    public function __construct($mediaApiClient, $sourceName)
    {
        $this->mediaApiClient = $mediaApiClient;
        $this->sourceName = $sourceName;
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
     * Get SourceName
     *
     * @return string
     */
    public function getSourceName()
    {
        return $this->sourceName;
    }

    /**
     * Get Media public url from a given reference
     *
     * @param string $reference
     * @return string
     */
    public function getMediaPublicUrlFromReference($reference)
    {
        return sprintf('%s/media/%s',
            $this->getMediaApiClient()->getEndpointRoot(),
            $reference
        );
    }

    /**
     * {@inheritdoc}
     */
    public function add(Media & $media)
    {
        if (null === $media->getUploadedFile() ||
            !$media->getUploadedFile()->getPathName()) {
            return false;
        }

        // Update case
        if ($media->getProviderReference()) {
            // Remove the previous associated media
            $this->remove($media);
        }

        try {
            $data = $this
                ->getMediaApiClient()
                ->post('/media', array(
                    'source' => $this->getSourceName(),
                    'media' => '@'.$media->getUploadedFile()->getPathName(),
                    'name' => $media->getUploadedFile()->getClientOriginalName()
                ))
            ;

            $apiMedia = json_decode($data, true);

            $media->setProviderData($apiMedia);
            $media->setMimeType($apiMedia['mimeType']);
            $media->setProviderReference($apiMedia['reference']);
            $media->setExtension($apiMedia['extension']);
            $media->setUrl($this->getMediaPublicUrl($media));

            $media->removeUploadedFile();

            return true;
        } catch(ApiHttpResponseException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Media $media)
    {
        try {
            $this
                ->getMediaApiClient()
                ->delete('/media/'.$media->getProviderReference())
            ;
        } catch(ApiHttpResponseException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl(Media $media)
    {
        return sprintf('%s/media/%s.%s',
            $this->getMediaApiClient()->getEndpointRoot(),
            $media->getProviderReference(),
            $media->getExtension()
        );
    }
}
