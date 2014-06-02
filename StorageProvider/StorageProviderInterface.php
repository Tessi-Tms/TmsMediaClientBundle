<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\StorageProvider;

use Tms\Bundle\MediaClientBundle\Model\Media;

interface StorageProviderInterface
{
    /**
     * Add a media
     *
     * @param Media $media
     * @return boolean
     */
    public function add(Media & $media);

    /**
     * Remove a media
     *
     * @param Media $media
     * @return boolean
     */
    public function remove(Media $media);
}
