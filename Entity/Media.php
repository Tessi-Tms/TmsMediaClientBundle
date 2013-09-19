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
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="mimeType", type="string")
     */
    private $mimeType;

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
     * @ORM\Column(name="metadata", type="json_array", nullable=true)
     */
    private $metadata;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="uploaded_at", type="datetime", nullable=true)
     */
    private $uploadedAt;

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
            $this->setUploadedAt($this->getUpdatedAt());

            return;
        }

        $this->getUploadedFile()->move(
            $this->getUploadRootDir(),
            $this->getUploadedFile()->getClientOriginalName()
        );

        $this->setUploadedFilePath(sprintf('%s/%s',
            $this->getUploadRootDir(),
            $this->getUploadedFile()->getClientOriginalName()
       ));

        // To get the right uploaded date time
        $this->setUploadedAt(new \DateTime('now'));

       $this->uploadedFile = null;
    }

    /**
     * onCreate
     *
     * @ORM\PrePersist()
     */
    public function onCreate()
    {
        $date = new \DateTime('now');
        $this->setCreatedAt($date);
        $this->setUpdatedAt($date);
    }

    /**
     * onUpdate
     *
     * @ORM\PreUpdate()
     */
    public function onUpdate()
    {
        $date = new \DateTime('now');
        $this->setUpdatedAt($date);
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
     * Set url
     *
     * @param string $url
     * @return Media
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return Media
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    
        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
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

    /**
     * Set created at
     *
     * @param Datetime $createdAt
     * @return Media
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get created at
     *
     * @return Datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated at
     *
     * @param Datetime $updatedAt
     * @return Media
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated at
     *
     * @return Datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set uploaded at
     *
     * @param Datetime $uploadedAt
     * @return Media
     */
    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;
    
        return $this;
    }

    /**
     * Get uploaded at
     *
     * @return Datetime 
     */
    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }
}
