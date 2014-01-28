<?php

namespace My\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="My\PageBundle\Entity\PageRepository")
 */
class Page implements Translatable
{
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=128)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @ORM\Column(name="content", type="text")
     * @Gedmo\Translatable     
     */
    private $content;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     *  @ORM\Column(name="isOnline", type="boolean")
     */
    private $isOnline;

    /**
     * @ORM\Column(name="menu", type="string", length=15)
     */
    private $menu;
    /**
     *  @ORM\Column(name="pos", type="smallint", nullable=true)
     */
    private $pos;

    /**
     *  @ORM\Column(name="icon", type="string", length=20, nullable=true)
     */
    private $icon;

    /**
     *  @ORM\Column(name="datetime", nullable=true)
     */
    private $date;


    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->date = new \DateTime();
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
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set isOnline
     *
     * @param boolean $isOnline
     * @return Page
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    /**
     * Get isOnline
     *
     * @return boolean 
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Page
     */
    public function setPos($position)
    {
        $this->pos = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return Page
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
     * Set date
     *
     * @param string $date
     * @return Page
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set menu
     *
     * @param string $menu
     * @return Page
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return string 
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
