<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Spot
 *
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\SpotRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("slug")
 * @ORM\Table(name="events_spots", indexes={
 *                                          @ORM\Index(name="country_index", columns={"countryCode"}),
 *                                          @ORM\Index(name="slug_index", columns={"slug"}),
 *                                          })
 */
class Spot 
{
	/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $name = '';

    /**
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address = '';

    /**
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug = '';


    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Location", fetch="EAGER")     
     */
    private $location;

    /**
     * @ORM\Column(name="countryCode", type="string", length=5)
     */
    private $countryCode = '';

    /**
     * @ORM\PrePersist
     */
    public function createSlug()
    {        
        if($this->getLocation() != null) $this->slug = $this->getLocation()->getCity()->getName().' ';
        if($this->hasName()) $this->slug .= $this->getName().' ';
        if($this->getAddress() != '') $this->slug .= $this->getAddress();        

        return $this->slug;
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
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set cityName
     *
     * @param string $cityName
     * @return Spot
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Get cityName
     *
     * @return string 
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Spot
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
     * Has name
     *
     * @return bool 
     */
    public function hasName()
    {
        return (empty($this->name))? false : true;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Spot
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }


    /**
     * Get address
     *
     * @return string 
     */
    public function getFullAddress()
    {
        return $this->name.', '.$this->address.', '.$this->location->getCity()->getName().', '.$this->location->getCountry()->getName();
    }

    /**
     * Get short adress
     *
     * @return string
     */
    public function shortAddress()
    {
        return substr($this->address,0,8).'...';
    }


    /**
     * Set slug
     *
     * @param string $slug
     * @return Spot
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
        if(!empty($this->slug)) return $this->slug;
        else return $this->createSlug();        
    }

    /**
     * Set location
     *
     * @param \My\WorldBundle\Entity\Location $location
     * @return Spot
     */
    public function setLocation(\My\WorldBundle\Entity\Location $location)
    {
        $this->location = $location;
        //set country code
        $this->countryCode = $location->getCountry()->getCode();

        return $this;
    }

    /**
     * Get location
     *
     * @return \My\WorldBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }


    /**
     * Set countryCode
     *
     * @param string $countryCode
     * @return Spot
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

}
