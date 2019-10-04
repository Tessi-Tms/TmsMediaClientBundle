<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProviderChoicesType extends AbstractType
{
    /**
     * @var array
     */
    private $providerChoices = array();

    /**
     * Constructor.
     *
     * @param array $providerChoices
     */
    public function __construct($providerChoices)
    {
        foreach ($providerChoices as $key => $choice) {
            $this->providerChoices[$key] = isset($choice[0]['alias']) ?
                $choice[0]['alias'] :
                $key
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array_flip($this->providerChoices),
            'choices_as_values' => true,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Types\ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'provider_choices';
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
