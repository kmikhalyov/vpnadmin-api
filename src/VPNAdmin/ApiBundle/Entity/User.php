<?php

namespace VPNAdmin\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})}, indexes={@ORM\Index(name="FK_users_companies", columns={"company_id"})})
 * @ORM\Entity(repositoryClass="VPNAdmin\ApiBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User
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
     * @ORM\Column(name="company_id", type="integer", nullable=false)
     * @Groups({"list", "details"})
     */
    private $companyId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Groups({"list", "details"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false, unique=true)
     * @Groups({"list", "details"})
     */
    private $email;

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
     * @var \VPNAdmin\ApiBundle\Entity\Company
     *
     * @ORM\ManyToOne(targetEntity="VPNAdmin\ApiBundle\Entity\Company")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

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
     * Set company id
     *
     * @param integer $companyId
     * @return User
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get company id
     *
     * @return integer 
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
     * @return User
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

    /**
     * Set company
     *
     * @param \VPNAdmin\ApiBundle\Entity\Company $company
     * @return User
     */
    public function setCompany(\VPNAdmin\ApiBundle\Entity\Company $company = null)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Get company
     *
     * @return \VPNAdmin\ApiBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }
}
