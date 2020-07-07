<?php
namespace Tms\Bundle\MediaClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tms\Bundle\MediaClientBundle\StorageProvider\TmsMediaStorageProvider;
use Tms\Bundle\MediaClientBundle\Model\Media;

class TmsAjaxMediaUploadType extends AbstractType
{
    const sessionName = 'TmsAjaxMediaUploadTypeOptions';

    /**
     * Instance of SessionInterface.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Instance of TmsMediaStorageProvider.
     *
     * @var TmsMediaStorageProvider
     */
    protected $storageProvider;

    /**
     * Instance of TranslatorInterface.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Instance of ValidatorInterface.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param SessionInterface        $session
     * @param TmsMediaStorageProvider $storageProvider
     * @param ValidatorInterface      $validator
     */
    public function __construct(
        SessionInterface $session,
        TmsMediaStorageProvider $storageProvider,
        TranslatorInterface $translator,
        ValidatorInterface $validator
    ) {
        $this->session = $session;
        $this->storageProvider = $storageProvider;
        $this->translator = $translator;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Remove the null or complexe options
        $cleanedOptions = array();
        foreach ($options as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            if (is_object($value)) {
                continue;
            }

            $cleanedOptions[$key] = $value;
        }

        // Keep the form options in the session
        $this->session->set(self::sessionName, array_merge(
            $this->session->get(self::sessionName, array()),
            array(
                $view->vars['id'] => array(
                    'options' => $cleanedOptions,
                ),
            )
        ));

        // Generate the plugin options
        $pluginOptions = array_intersect_key($cleanedOptions, array(
            'maxSize' => null,
            'maxSizeMessage' => null,
            'mimeTypes' => null,
            'mimeTypesMessage' => null,
            'imagesMimeTypes' => null,
            'imageMaxWidth' => null,
            'imageMaxHeight' => null,
            'buttonChooseLabel' => null,
            'buttonUpdateLabel' => null,
        ));

        // Translate error messages
        $pluginOptions['maxSizeMessage'] = $this->translator->trans($pluginOptions['maxSizeMessage'], array(
            '{{ size }}' => '__SIZE__',
            '{{ suffix }}' => '__SUFFIX__',
            '{{ limit }}' => '__LIMIT__',
        ), 'validators');
        $pluginOptions['mimeTypesMessage'] = $this->translator->trans($pluginOptions['mimeTypesMessage'], array(
            '{{ type }}' => '__TYPE__',
            '{{ types }}' => '__TYPES__',
        ), 'validators');
        $pluginOptions['buttonChooseLabel'] = $this->translator->trans($pluginOptions['buttonChooseLabel']);
        $pluginOptions['buttonUpdateLabel'] = $this->translator->trans($pluginOptions['buttonUpdateLabel']);

        // Add the plugin options to the view
        $view->vars['pluginOptions'] = $pluginOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $metadata = $options['metadata'];
        $provider = $this->storageProvider;
        $session = $this->session;
        $validator = $this->validator;

        $builder
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function(FormEvent $event) use ($options) {
                    $isUploadedFileRequired = $options['required'];
                    if (null !== $event->getData()) {
                        $isUploadedFileRequired = false;
                    }

                    // Add the upload file
                    $event
                        ->getForm()
                        ->add(
                            'uploadedFile',
                            Types\FileType::class,
                            array(
                                'label' => false,
                                'required' => $isUploadedFileRequired
                            )
                        )
                    ;
                })
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) use ($metadata, $options, $provider, $session, $validator) {
                    $data = $event->getData();
                    $form = $event->getForm();

                    $media = $data;
                    if (is_array($data) && isset($data['uploadedFile'])) {
                        $uploadedFile = $data['uploadedFile'];

                        if ($uploadedFile instanceof UploadedFile) {
                            $media = new Media();
                            $media->setUploadedFile($data['uploadedFile']);
                        } elseif (is_string($uploadedFile)) {
                            $event->setData(array());

                            $ajaxUpload = $session->get(TmsAjaxMediaUploadType::sessionName, array());
                            $field = preg_replace('/^(.*)_[^_]+$/', "$1", $uploadedFile);
                            if (isset($ajaxUpload[$field]['uploads'][$uploadedFile])) {
                                $file = $ajaxUpload[$field]['uploads'][$uploadedFile];

                                $media = new Media();
                                if (isset($file['providerName'])) {
                                    $media->setProviderName($file['providerName']);
                                }
                                if (isset($file['providerReference'])) {
                                    $media->setProviderReference($file['providerReference']);
                                }
                                if (isset($file['publicUri'])) {
                                    $media->setPublicUri($file['publicUri']);
                                }
                                if (isset($file['extension'])) {
                                    $media->setExtension($file['extension']);
                                }
                                if (isset($file['mimeType'])) {
                                    $media->setMimeType($file['mimeType']);
                                }
                            }

                            // Clean the session
                            unset($ajaxUpload[$field]['uploads'][$uploadedFile]);
                            $session->set(TmsAjaxMediaUploadType::sessionName, $ajaxUpload);
                        }
                    }

                    // Upload the media
                    if ($media instanceof Media && !$media->getProviderReference()) {

                        // Validate the uploaded file
                        if (null !== $media->getUploadedFile()) {

                            $violations = $validator->validate(
                                $media->getUploadedFile(),
                                new File(array(
                                    'maxSize' => $options['maxSize'],
                                    'maxSizeMessage' => $options['maxSizeMessage'],
                                    'mimeTypes' => $options['mimeTypes'],
                                    'mimeTypesMessage' => $options['mimeTypesMessage'],
                                ))
                            );

                            if (count($violations)) {
                                foreach ($violations as $violation) {
                                    $form->addError(new FormError($violation->getMessage()));
                                }

                                return false;
                            }
                        }

                        // Add the media metadata
                        foreach ($metadata as $key => $value) {
                            $media->setMetadata($key, $value);
                        }

                        // Upload the media
                        $provider->add($media);
                    }

                    // Return only the public data
                    if (! $data instanceof Media && $media instanceof Media) {
                        $publicData = $media->getPublicData();

                        $event->setData($publicData);
                        $form->remove('uploadedFile');
                        foreach ($publicData as $key => $value) {
                            $form->add($key, Types\HiddenType::class, array(
                                'data' => $value,
                            ));
                        }
                    }
                })
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'metadata' => array(),
                'maxSize' => '5M',
                'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.',
                'mimeTypes' => null,
                'mimeTypesMessage' => 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.',
                'imagesMimeTypes' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png'
                ),
                'imageMaxWidth' => null,
                'imageMaxHeight' => null,
                'buttonChooseLabel' => 'Choose file',
                'buttonUpdateLabel' => 'Update file'

            ))
            ->setAllowedTypes('metadata', array('array'))
            ->setAllowedTypes('maxSize', array('string'))
            ->setAllowedTypes('maxSizeMessage', array('string'))
            ->setAllowedTypes('mimeTypes', array('null', 'array'))
            ->setAllowedTypes('mimeTypesMessage', array('string'))
            ->setAllowedTypes('imagesMimeTypes', array('array'))
            ->setAllowedTypes('imageMaxWidth', array('null', 'string', 'integer'))
            ->setAllowedTypes('imageMaxHeight', array('null', 'string', 'integer'))
            ->setAllowedTypes('buttonChooseLabel', array('string'))
            ->setAllowedTypes('buttonUpdateLabel', array('string'))
        ;
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
    public function getBlockPrefix()
    {
        return 'tms_ajax_media_upload';
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
