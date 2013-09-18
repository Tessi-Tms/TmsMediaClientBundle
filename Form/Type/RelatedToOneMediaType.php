<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

class RelatedToOneMediaType extends MediaType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'related_to_one_media';
    }
}
