<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @author:  Nabil Mansouri <nabil.mansouri@tessi.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\StorageProvider;

use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    /**
     * Clone media
     *
     * @param Media the media to clone
     *
     * @return Media the media cloned
     */
    public function cloneMedia(Media $media)
    {
        $mediaCloned = clone $media;
        $tmpMediaName = uniqid().'_copy_'.$media->providerData()->name;
        $tmpMediaPath = sprintf('%s/%s.%s',
            sys_get_temp_dir(),
            $tmpMediaName,
            $media->getExtension()
        );

        $tmpMediaContent = file_get_contents('http:'.$media->getPublicUri());
        file_put_contents($tmpMediaPath, $tmpMediaContent);

        $uploadedFile = new UploadedFile(
            $tmpMediaPath,
            $tmpMediaName,
            $media->getProviderData()->size,
            $media->getMimeType()
        );
        $mediaCloned->setUploadedFile($uploadedFile);

        $this->add($mediaCloned);
        unlink($tmpMediaPath);

        return $mediaCloned;
    }
}
