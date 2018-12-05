<?php

/**
 * @author:  Rémy MENCE <remy.mence@tessi.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class MediaUploadTransformer implements DataTransformerInterface
{
    /**
     * Reverse transforms.
     *
     * @param object|null $media
     *
     * @return object|null
     */
    public function reverseTransform($media)
    {
        if ($media && null !== $media->getUploadedFile()) {
            return $media;
        }

        return null;
    }
}
