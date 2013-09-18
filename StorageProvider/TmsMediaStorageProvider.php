<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\StorageProvider;

use Tms\Bundle\MediaClientBundle\Entity\Media;

class TmsMediaStorageProvider implements StorageProviderInterface
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
        $reference = $this
            ->getMediaApiClient()
            ->post('/media', array(
                'media' => '@'.$media->getUploadedFilePath()
            ))
        ;

        $media->setProviderReference($reference);
        unlink($media->getUploadedFilePath());
        $media->setUploadedFilePath(null);
    }

    /**
     * {@inheritdoc}
     */
    public function get(Media $media)
    {
        $this
            ->getMediaApiClient()
            ->get('/media/'.$media->getProviderReference())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Media $media)
    {
        $this
            ->getMediaApiClient()
            ->delete('/media/'.$media->getProviderReference())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl(Media $media)
    {
        return sprintf('%s/media/%s',
            $this->getMediaApiClient()->getEndpointRoot(),
            $media->getProviderReference()
        );
    }
}
