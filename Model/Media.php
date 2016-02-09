<?php

/**
 * @author Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 */

namespace Tms\Bundle\MediaClientBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tms\Bundle\MediaClientBundle\Exception\MediaClientException;
use Doctrine\Common\Util\Inflector;

class Media implements \Serializable
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $publicUri;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * @var string
     */
    protected $providerReference;

    /**
     * @var array
     */
    protected $providerData;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $metadata;

    /**
     * @var Datetime
     */
    protected $createdAt;

    /**
     * @var Datetime
     */
    protected $updatedAt;

    /**
     * @var UploadedFile
     */
    protected $uploadedFile;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->publicUri = null;
    }

    /**
     * toString.
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
     * Magic setter.
     *
     * @param string $name  The setter name.
     * @param mixed  $value The value to set.
     * @return Media
     */
    public function __set($name, $value)
    {
        $name = Inflector::tableize($name);
        $this->setMetadata($name, $value);

        return $this;
    }

    /**
     * Magic getter.
     *
     * @param string $name The getter name.
     * @return mixed The value.
     */
    public function __get($name)
    {
        $name = Inflector::tableize($name);

        return $this->getMetadata($name);
    }

    /**
     * Magic call.
     *
     * @param string $method    The method name.
     * @param mixed  $arguments The given arguments.
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $property  = $method;
        $subMethod = substr($method, 0, 3);

        if (in_array($subMethod, array('set', 'get'))) {
            $property = Inflector::tableize(substr($method, 3));

            if ('set' === $subMethod) {
                $this->$property = $arguments;
            }
        }

        return $this->$property;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            'publicUri'         => $this->publicUri,
            'mimeType'          => $this->mimeType,
            'providerName'      => $this->providerName,
            'providerReference' => $this->providerReference,
            'providerData'      => $this->providerData,
            'extension'         => $this->extension,
            'metadata'          => $this->metadata,
            'createdAt'         => $this->createdAt,
            'updatedAt'         => $this->updatedAt,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $unserializedData = unserialize($data);

        $this->publicUri         = $unserializedData['publicUri'];
        $this->mimeType          = $unserializedData['mimeType'];
        $this->providerName      = $unserializedData['providerName'];
        $this->providerReference = $unserializedData['providerReference'];
        $this->providerData      = $unserializedData['providerData'];
        $this->extension         = $unserializedData['extension'];
        $this->metadata          = $unserializedData['metadata'];
        $this->createdAt         = $unserializedData['createdAt'];
        $this->updatedAt         = $unserializedData['updatedAt'];
    }

    /**
     * isImageable
     */
    public function isImageable()
    {
        if (null === $this->getPublicUri()) {
            return false;
        }

        if ("application/pdf" === $this->getMimeType()) {
            return true;
        }

        return (boolean)preg_match("#^image/#", $this->getMimeType());
    }

    /**
     * Returns public data
     *
     * @return array
     */
    public function getPublicData()
    {
        return array(
            'providerName'      => $this->getProviderName(),
            'providerReference' => $this->getProviderReference(),
            'publicUri'         => $this->getPublicUri(),
            'extension'         => $this->getExtension(),
            'mimeType'          => $this->getMimeType()
        );
    }

    /**
     * Set uploaded file.
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
     * Returns uploaded file.
     *
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns public uri
     *
     * @param string $publicUri
     * @return Media
     */
    public function setPublicUri($publicUri)
    {
        $this->publicUri = $publicUri;

        return $this;
    }

    /**
     * Get public uri.
     *
     * @return string
     */
    public function getPublicUri()
    {
        return $this->publicUri;
    }

    /**
     * Returns url.
     *
     * @param string $extension
     * @return string
     */
    public function getUrl($extension = null, $query = array())
    {
        if (null === $this->getPublicUri()) {
            return '';
        }

        $countValidQueries = 0;
        foreach ($query as $k => $param) {
            if (!$param) {
                unset($query[$k]);
            } else {
                $countValidQueries++;
            }
        }

        $url = sprintf(
            '%s.%s',
            $this->getPublicUri(),
            null === $extension ? $this->getExtension() : $extension
        );

        if ($countValidQueries == 0) {
            return $url;
        }

        return sprintf(
            '%s?%s',
            $url,
            http_build_query($query)
        );
    }

    /**
     * Set mimeType.
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
     * Returns mimeType.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set providerName.
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
     * Returns providerName.
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Set providerReference.
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
     * Returns providerReference.
     *
     * @return string
     */
    public function getProviderReference()
    {
        return $this->providerReference;
    }

    /**
     * Set providerData.
     *
     * @param array $providerData
     * @return Media
     */
    public function setProviderData($providerData)
    {
        $this->providerData = $providerData;

        return $this;
    }

    /**
     * Returns providerData.
     *
     * @return array
     */
    public function getProviderData()
    {
        return $this->providerData;
    }

    /**
     * Set extension.
     *
     * @param string $extension
     * @return Media
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Returns extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set metadata.
     *
     * @param array|string $key
     * @param mixed        $value
     * @return Media
     */
    public function setMetadata($key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Returns metadata.
     *
     * @param string|null $key
     * @return mixed|null
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return $this->metadata;
        }

        return $this->hasMetadata($key) ? $this->metadata[$key] : null;
    }

    /**
     * Returns weather metadata has a key
     *
     * @param string $key
     * @return mixed
     */
    public function hasMetadata($key)
    {
        return isset($this->metadata[$key]);
    }

    /**
     * Set created at.
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
     * Returns created at.
     *
     * @return Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated at.
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
     * Returns updated at.
     *
     * @return Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
