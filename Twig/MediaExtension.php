<?php

/**
 * @author Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 */

namespace Tms\Bundle\MediaClientBundle\Twig;

use Tms\Bundle\MediaClientBundle\Twig\MediaExtension;
use Tms\Bundle\MediaClientBundle\Manager\MediaClientManager;

class MediaExtension extends \Twig_Extension
{
    protected $mediaClientManager;

    /**
     * Constructor
     *
     * @param MediaClientManager $mediaClientManager
     */
    public function __construct(MediaClientManager $mediaClientManager)
    {
        $this->mediaClientManager = $mediaClientManager;
    }

    /**
     * Get MediaClientManager
     *
     * @return MediaClientManager
     */
    public function getMediaClientManager()
    {
        return $this->mediaClientManager;
    }

    public function getFunctions()
    {
        return array(
            'mediaUrl' => new \Twig_Function_Method($this, 'getMediaUrl'),
        );
    }

    public function getMediaUrl($mediaReference)
    {
        if(null === $mediaReference) {
            return null;
        }

        return $this->getMediaClientManager()->getMediaUrl($mediaReference);
    }

    public function getName()
    {
        return 'tms_media_client.twig.website_extension';
    }
}
