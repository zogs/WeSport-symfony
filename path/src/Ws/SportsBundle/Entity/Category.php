<?php

namespace Ws\SportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @ORM\Table(name="sports_category")
 * @ORM\Entity(repositoryClass="Ws\SportsBundle\Entity\CategoryRepository")
 */
class Category implements Translatable
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
     * @ORM\Column(name="icon", type="string", length=25, nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(name="keywords", type="string", length=125, nullable=true)
     */
    private $keywords;

    /**
     * @ORM\OneToMany(targetEntity="Sport", mappedBy="category")
     */
    protected $sports;

    public function __construct()
    {
        $this->sports = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * Add sports
     *
     * @param \Ws\SportsBundle\Entity\Sport $sports
     * @return Category
     */
    public function addSport(\Ws\SportsBundle\Entity\Sport $sports)
    {
        $this->sports[] = $sports;

        return $this;
    }

    /**
     * Remove sports
     *
     * @param \Ws\SportsBundle\Entity\Sport $sports
     */
    public function removeSport(\Ws\SportsBundle\Entity\Sport $sports)
    {
        $this->sports->removeElement($sports);
    }

    /**
     * Get sports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Category
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
