<?php

namespace VPNAdmin\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

/**
 * Transfer
 *
 * @ORM\Table(name="transfer", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="created", columns={"created"})})
 * @ORM\Entity
 */
class Transfer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"list", "details"})
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @Groups({"list", "details"})
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     * @Groups({"list", "details"})
     * @Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="resource", type="string", length=255, nullable=false)
     * @Groups({"list", "details"})
     */
    private $resource;

    /**
     * Note: DB type bigint
     * 
     * @var integer
     * @ORM\Column(name="transferred", type="string", nullable=false)
     * @Groups({"list", "details"})
     */
    private $transferred;

    /**
     * @var \VPNAdmin\ApiBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="VPNAdmin\ApiBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * Set user id
     *
     * @param integer $userId
     * @return Transfer
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get user id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Transfer
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set resource
     *
     * @param string $resource
     * @return Transfer
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get resource
     *
     * @return string 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set transferred
     *
     * @param integer $transferred
     * @return Transfer
     */
    public function setTransferred($transferred)
    {
        $this->transferred = $transferred;
        return $this;
    }

    /**
     * Get transferred
     *
     * @return integer 
     */
    public function getTransferred()
    {
        return $this->transferred;
    }

    /**
     * Set user
     *
     * @param \VPNAdmin\ApiBundle\Entity\User $user
     * @return Transfer
     */
    public function setUser(\VPNAdmin\ApiBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \VPNAdmin\ApiBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
