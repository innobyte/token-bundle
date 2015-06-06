<?php

namespace Innobyte\TokenBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Token
 * Token representation
 * Properties are ordered by the following criteria, in order to make the resulting table faster to traverse:
 * - first, fixed-length not nullable columns (integers and dates)
 * - then, variable-length not nullable columns (varchars)
 * - after these, fixed-length nullable columns (integers and dates)
 * - then, variable-length nullable columns (varchars)
 * - last, the text fields, which are worst in terms of predicting field length
 * Please take this criteria into consideration when adding new fields.
 *
 * @ORM\Table("token")
 * @ORM\Entity(repositoryClass="Innobyte\TokenBundle\Repository\TokenRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @package Innobyte\TokenBundle\Entity
 *
 * @author  Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class Token
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer")
     */
    protected $ownerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="uses_max", type="integer")
     */
    protected $usesMax;

    /**
     * @var integer
     *
     * @ORM\Column(name="uses_count", type="integer")
     */
    protected $usesCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_at", type="datetime")
     */
    protected $modifiedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="owner_type", type="string", length=20)
     */
    protected $ownerType;

    /**
     * @var string
     *
     * @ORM\Column(name="scope", type="string", length=20)
     */
    protected $scope;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=32)
     */
    protected $hash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    protected $expiresAt;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    protected $data;

    /**
     * Initialize values
     */
    public function __construct()
    {
        $this->usesCount = 0;
        $this->active = true;
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
     * Set owner type
     *
     * @param string $ownerType
     *
     * @return Token
     */
    public function setOwnerType($ownerType)
    {
        $this->ownerType = $ownerType;

        return $this;
    }

    /**
     * Get owner type
     *
     * @return string
     */
    public function getOwnerType()
    {
        return $this->ownerType;
    }

    /**
     * Set owner ID
     *
     * @param integer $ownerId
     *
     * @return Token
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * Get owner ID
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return Token
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return Token
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set max uses
     *
     * @param integer $usesMax
     *
     * @return Token
     */
    public function setUsesMax($usesMax)
    {
        $this->usesMax = $usesMax;

        return $this;
    }

    /**
     * Get max uses
     *
     * @return integer
     */
    public function getUsesMax()
    {
        return $this->usesMax;
    }

    /**
     * Set number of uses
     *
     * @param integer $usesCount
     *
     * @return Token
     */
    public function setUsesCount($usesCount)
    {
        $this->usesCount = $usesCount;

        return $this;
    }

    /**
     * Get number of uses
     *
     * @return integer
     */
    public function getUsesCount()
    {
        return $this->usesCount;
    }

    /**
     * Set additional data
     *
     * @param string $data
     *
     * @return Token
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get additional data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set active flag
     *
     * @param boolean $active
     *
     * @return Token
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active flag
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set created at
     *
     * @param \DateTime $createdAt
     *
     * @return Token
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modified at
     *
     * @param \DateTime $modifiedAt
     *
     * @return Token
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modified at
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set expires at
     *
     * @param \DateTime $expiresAt
     *
     * @return Token
     */
    public function setExpiresAt(\DateTime $expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expires at
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Called before saving for the first time
     *
     * @ORM\PrePersist
     *
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $currentTime = new \DateTime();

        $this
            ->setCreatedAt($currentTime)
            ->setModifiedAt($currentTime);
    }

    /**
     * Called before saving (each time)
     *
     * @ORM\PreUpdate
     *
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $currentTime = new \DateTime();

        $this->setModifiedAt($currentTime);
    }
}
