<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlankValidator;
use Tms\Bundle\MediaClientBundle\Model\Media;

class TmsMediaUploadNotBlankValidator extends NotBlankValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof Media) {
            if (null === $value->getPublicUri() && null === $value->getUploadedFile()) {
                $this->context->addViolation($constraint->message, array(
                    '{{ value }}' => $this->formatValue($value),
                ));
            }
        }
    }
}