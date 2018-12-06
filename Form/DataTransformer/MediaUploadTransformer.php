<?php

/**
 * @author:  Rémy MENCE <remy.mence@tessi.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tms\Bundle\MediaClientBundle\Model\Media;

class MediaUploadTransformer implements DataTransformerInterface
{
    /**
     * Transforms.
     *
     * @param object|null $media
     *
     * @return object
     */
    public function transform($media)
    {
        if (null !== $media) {
            return $media;
        }

        return new Media();
    }

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
