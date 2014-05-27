<?php

namespace Innobyte\TokenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Token
 *
 * @ORM\Table("token")
 * @ORM\Entity(repositoryClass="Innobyte\TokenBundle\Repository\TokenRepository")
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
     * @var string
     *
     * @ORM\Column(name="owner_type", type="string", length=20)
     */
    protected $ownerType;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer")
     */
    protected $ownerId;

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
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    protected $data;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified_at", type="datetime")
     */
    protected $modifiedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    protected $expiresAt;

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
     * Set ownerType
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
     * Get ownerType
     *
     * @return string
     */
    public function getOwnerType()
    {
        return $this->ownerType;
    }

    /**
     * Set ownerId
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
     * Get ownerId
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
     * Set usesMax
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
     * Get usesMax
     *
     * @return integer
     */
    public function getUsesMax()
    {
        return $this->usesMax;
    }

    /**
     * Set usesCount
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
     * Get usesCount
     *
     * @return integer
     */
    public function getUsesCount()
    {
        return $this->usesCount;
    }

    /**
     * Set data
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
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set active
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
     * Get active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return Token
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
