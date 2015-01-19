<?php

namespace Ws\SportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Sport
 *
 * @ORM\Table(name="sports_sport")
 * @ORM\Entity(repositoryClass="Ws\SportsBundle\Entity\SportRepository")
 */
class Sport implements Translatable
{
	/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @Gedmo\Translatable
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(name="keywords", type="string", length=125, nullable=true)
     */
    private $keywords;

    /**
     * @ORM\Column(name="icon", type="string", length=25, nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(name="action", type="string", length=15, nullable=true)
     */
    private $action;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="sports")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\ManyToMany(targetEntity="Ws\EventsBundle\Entity\Search", inversedBy="sports")
     */
    protected $searchs;


    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Sport 
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * Set name
     *
     * @param string $name
     * @return Sport
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
     * Set slug
     *
     * @param string $slug
     * @return Sport
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return Sport
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Sport
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set category
     *
     * @param \Ws\SportsBundle\Entity\Category $category
     * @return Sport
     */
    public function setCategory(\Ws\SportsBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Ws\SportsBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Sport
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}
