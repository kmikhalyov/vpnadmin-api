<?php

namespace VPNAdmin\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Company
 *
 * @ORM\Table(name="company", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="quota", columns={"quota"})})
 * @ORM\Entity(repositoryClass="VPNAdmin\ApiBundle\Repository\CompanyRepository")
 * @UniqueEntity("name")
 */
class Company
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @Groups({"list", "details"})
     */
    private $name;

    /**
     * Note: DB type bigint
     * @var integer
     * @ORM\Column(name="quota", type="string", nullable=false)
     * @Groups({"list", "details"})
     */
    private $quota;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     * @Groups({"details"})
     * @Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     * @Groups({"details"})
     * @Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $modified;

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
     * Set name
     *
     * @param string $name
     * @return Company
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set quota
     *
     * @param integer $quota
     * @return Company
     */
    public function setQuota($quota)
    {
        $this->quota = $quota;
        return $this;
    }

    /**
     * Get quota
     *
     * @return integer 
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Company
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
     * Set modified
     *
     * @param \DateTime $modified
     * @return Company
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }
}
