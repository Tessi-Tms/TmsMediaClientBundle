<?php

/**
 * @author Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 */

namespace Tms\Bundle\MediaClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="media")
 * @ORM\HasLifecycleCallbacks()
 */
class Media
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_name", type="string")
     */
    private $providerName;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_reference", type="string")
     */
    private $providerReference;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_metadata", type="json_array", nullable=true)
     */
    private $metadata;

    /**
     * @var string
     */
    private $uploadedFilePath;

    /**
     * @var UploadedFile
     */
    private $uploadedFile;

    /**
     * toString
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('[%s] %s',
            $this->getProviderName(),
            $this->getProviderReference()
        );
    }

    /**
     * Get upload directory
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'media_uploads';
    }

    /**
     * Get upload root directory
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return sprintf('%s/%s',
            sys_get_temp_dir(),
            $this->getUploadDir()
        );
    }

    /**
     * Upload
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function upload()
    {
        if (null === $this->getUploadedFile()) {
            return;
        }

        $this->getUploadedFile()->move(
            $this->getUploadRootDir(),
            $this->getUploadedFile()->getClientOriginalName()
        );

        $this->setUploadedFilePath(
            sprintf('%s/%s',
                $this->getUploadRootDir(),
                $this->getUploadedFile()->getClientOriginalName()
            )
        );

        $this->uploadedFile = null;
    }

    /**
     * Set uploaded file path
     *
     * @param string $uploadedFilePath
     * @return Media
     */
    public function setUploadedFilePath($uploadedFilePath)
    {
        $this->uploadedFilePath = $uploadedFilePath;

        return $this;
    }

    /**
     * Get uploaded file path
     *
     * @return string
     */
    public function getUploadedFilePath()
    {
        return $this->uploadedFilePath;
    }

    /**
     * Set uploaded file
     *
     * @param UploadedFile $uploadedFile
     * @return Media
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /**
     * Get uploaded file
     *
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set providerName
     *
     * @param string $providerName
     * @return Media
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    
        return $this;
    }

    /**
     * Get providerName
     *
     * @return string 
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Set providerReference
     *
     * @param string $providerReference
     * @return Media
     */
    public function setProviderReference($providerReference)
    {
        $this->providerReference = $providerReference;
    
        return $this;
    }

    /**
     * Get providerReference
     *
     * @return string 
     */
    public function getProviderReference()
    {
        return $this->providerReference;
    }

    /**
     * Set metadata
     *
     * @param array $metadata
     * @return Media
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    
        return $this;
    }

    /**
     * Get metadata
     *
     * @return array 
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
