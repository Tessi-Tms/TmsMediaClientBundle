<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\StorageProvider;

use Tms\Bundle\MediaClientBundle\Entity\Media;

interface StorageProviderInterface
{
    /**
     * Add a media
     *
     * @param Media $media
     */
    public function add(Media & $media);

    /**
     * Get a media
     *
     * @param Media $media
     */
    public function get(Media $media);

    /**
     * Remove a media
     *
     * @param Media $media
     */
    public function remove(Media $media);

    /**
     * Get the media public url
     *
     * @param Media $media
     */
    public function getMediaPublicUrl(Media $media);
}
