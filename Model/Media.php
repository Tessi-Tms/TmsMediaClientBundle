<?php

/**
 * @author Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 */

namespace Tms\Bundle\MediaClientBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Media
{
    /*
     * @var string
     */
    const REMOVE_ACTION = 'remove';

    /*
     * @var string
     */
    const CREATE_ACTION = 'create';

    /*
     * @var string
     */
    const UPDATE_ACTION = 'update';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $url;

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
     * @var array synchronizedActions
     */
    protected $synchronizedActions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->url = null;
        $this->initSynchronizedActionsValues();
    }

    /**
     * initialize synchronized actions values
     */
    protected function initSynchronizedActionsValues(){
        $this->synchronizedActions = array();
        $this->synchronizedActions[self::REMOVE_ACTION] = true;
        $this->synchronizedActions[self::CREATE_ACTION] = true;
        $this->synchronizedActions[self::UPDATE_ACTION] = true;
    }

    /**
     * enable synchronization for a given action
     * @param string $key synchornized action
     */
    public function enableSynchronizedAction($key)
    {
        $this->setSynchronizedActionValue($key, true);
    }

    /**
     * disable synchronization for a given action
     * @param string $key synchornized action
     */
    public function disableSynchronizedAction($key)
    {
        $this->setSynchronizedActionValue($key, false);
    }

    /**
     * check if a given action is synchronized or not
     * @param string $key synchornized action
     */
    public function isSynchronizedAction($key)
    {
        return $this->getSynchronizedActionValue($key);
    }

    /**
     * check if a given action exists
     * @throw Exception when action does'nt exist
     * @param string synchornized action
     */
    private function existsSynchronizedAction($key)
    {

        if(array_key_exists($key, $this->synchronizedActions)) {
            return true;
        } else {
            //TODO create specific exception
            $throw new \Exception($key . " is not an existing action.");
        }
    }

    /**
     * set synchronism's value of a given action
     * @param string $key synchornized action
     * @param string $value synchornized action
     */
    protected function setSynchronizedActionValue($key, $value)
    {
        if(existsSynchronizedAction($key)) {
            $this->synchronizedActions[$key] = $value;
        }
    }

    /**
     * get synchronism's value of a given action
     * @param string $key synchornized action
     */
    protected function getSynchronizedActionValue($key)
    {
        if(existsSynchronizedAction($key)) {
            return $this->synchronizedActions[$key];
        }
    }

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
     * Remove uploaded file
     *
     * @return Media
     */
    public function removeUploadedFile()
    {
        unlink($this->uploadedFile->getPathName());
        $this->uploadedFile = null;

        return $this;
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
     * @param string $extension
     * @return string
     */
    public function getUrl($extension = null, $query = array())
    {
        if (null === $this->url) {
            return null;
        }

        if (empty($query) && (null === $extension || $extension == $this->getExtension())) {
            return $this->url;
        }

        if (null === $extension) {
            $extension = $this->getExtension();
        }

        foreach ($query as $k => $param) {
            if (!$param) {
                unset($query[$k]);
            }
        }
        $query = http_build_query($query);

        $parsedUrl = parse_url($this->url);
        $scheme   = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host     = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user     = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass     = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsedUrl['path']) ? str_replace($this->getExtension(), $extension, $parsedUrl['path']) : '';
        $query    = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'].'&'.$query : '?'.$query;
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
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
     * Set providerData
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
     * Get providerData
     *
     * @return array 
     */
    public function getProviderData()
    {
        return $this->providerData;
    }

    /**
     * Set extension
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
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
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
     * Set safe
     *
     * @param boolean safe
     * @return Media
     */
    public function setSafe($safe)
    {
        $this->safe = $safe;
        return $this;
    }

    /**
     * Get Safe value
     *
     * @return boolean Safe
     */
    public function getSafe()
    {
        return $this->safe;
    }
}
