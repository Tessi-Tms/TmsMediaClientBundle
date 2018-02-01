<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\StorageProvider;

use Da\ApiClientBundle\Exception\ApiHttpResponseException;
use Tms\Bundle\MediaClientBundle\Model\Media;

class TmsMediaStorageProvider extends AbstractStorageProvider
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
     * Constructor.
     *
     * @param RestApiClientInterface $mediaApiClient
     * @param string                 $sourceName
     */
    public function __construct($mediaApiClient, $sourceName)
    {
        $this->mediaApiClient = $mediaApiClient;
        $this->sourceName = $sourceName;
    }

    /**
     * Get MediaClient.
     *
     * @return RestApiClientInterface
     */
    public function getMediaApiClient()
    {
        return $this->mediaApiClient;
    }

    /**
     * Get SourceName.
     *
     * @return string
     */
    public function getSourceName()
    {
        return $this->sourceName;
    }

    /**
     * {@inheritdoc}
     */
    public function doAdd(Media &$media)
    {
        // Update case
        if ($media->getProviderReference()) {
            if (null === $media->getUploadedFile()) {
                if (!empty($media->getMetadata())) {
                    $this
                        ->getMediaApiClient()
                        ->put(
                            sprintf('/media/%s', $media->getProviderReference()),
                            array('metadata' => $media->getMetadata())
                        )
                    ;

                    return true;
                }

                return false;
            } else {
                // Reupload case, remove the previous associated media
                $this->remove($media->getProviderReference());
            }
        }

        if (null !== $media->getUploadedFile()) {
            $response = $this
                ->getMediaApiClient()
                ->post('/media', array(
                    'source' => $this->getSourceName(),
                    'name' => $media->getUploadedFile()->getClientOriginalName(),
                    'metadata' => $media->getMetadata(),
                    'media' => curl_file_create(
                        $media->getUploadedFile()->getPathName(),
                        $media->getUploadedFile()->getMimeType(),
                        $media->getUploadedFile()->getClientOriginalName()
                    ),
                ))
            ;

            $apiMedia = json_decode($response->getContent(), true);

            $media->setProviderData($apiMedia);
            $media->setMimeType($apiMedia['mimeType']);
            $media->setProviderReference($apiMedia['reference']);
            $media->setExtension($apiMedia['extension']);
            $media->setPublicUri($apiMedia['publicUri']);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($reference)
    {
        try {
            $this
                ->getMediaApiClient()
                ->delete('/media/'.$reference)
            ;
        } catch (ApiHttpResponseException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl($reference)
    {
        try {
            $raw = $this->getMediaApiClient()->get('/endpoint')->getContent();
            $data = json_decode($raw, true);

            return sprintf('%s/media/%s',
                $data['publicEndpoint'],
                $reference
            );
        } catch (ApiHttpResponseException $e) {
            return false;
        }
    }
}
