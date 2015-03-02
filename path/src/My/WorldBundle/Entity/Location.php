<?php


namespace My\WorldBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use My\WorldBundle\Entity\Country;
use My\WorldBundle\Entity\State;
use My\WorldBundle\Entity\City;
/**
 * Location
 *
 * @ORM\Table(name="world_location")
 * @ORM\Entity(repositoryClass="My\WorldBundle\Entity\LocationRepository")
 */
class Location
{	
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Country", fetch="EAGER")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\State", fetch="EAGER")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=true)
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\State", fetch="EAGER")
     * @ORM\JoinColumn(name="departement_id", referencedColumnName="id", nullable=true)
     */
    protected $departement;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\State", fetch="EAGER")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id", nullable=true)
     */
    protected $district;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\State", fetch="EAGER")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id", nullable=true)
     */
    protected $division;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\City", fetch="EAGER")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     */
    protected $city;    

    public $city_id; //!\
    public $city_name;//!\


    public function exist()
    {
        if(!empty($this->id)) return true;
        return false;
    }

    public function isNull()
    {
        if(empty($this->id)) return true;
        return false;
    }

    public function __toString()
    {
        return strval($this->id);
    }
    /**
     * Set Id
     *
     * @param integer $id
     * @return Location
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
        return (int) $this->id;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Location
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
            return $this->country;
    }

    /**
     * Get country code
     *
     * @return string 
     */
    public function getCountryCode()
    {
            return (isset($this->country))? $this->country->getCode() : null;
    }

    /**
     * Get country id
     *
     * @return string 
     */
    public function getCountryId()
    {
            return (isset($this->country))? $this->country->getId() : null;
    }

    /**
     * Has country
     *
     * @return boolean
     */
    public function hasCountry()
    {
        if(isset($this->country)) return true;
        return false;

    }

    /**
     * Set region
     *
     * @param string $region
     * @return Location
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region id
     *
     * @return integer 
     */
    public function getRegionId()
    {
            return (isset($this->region))? $this->region->getId() : null;
    }

    /**
     * Get region
     *
     * @return string 
     */
    public function getRegion()
    {
            return $this->region;
    }

    /**
     * Has region
     *
     * @return boolean
     */
    public function hasRegion()
    {
        if(isset($this->region)) return true;
        return false;

    }

    /**
     * Set departement
     *
     * @param string $departement
     * @return Location
     */
    public function setDepartement($departement)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return string 
     */
    public function getDepartement()
    {
            return $this->departement;
    }
    
    /**
     * Get departement id
     *
     * @return integer 
     */
    public function getDepartementId()
    {
            return (isset($this->departement))? $this->departement->getId() : null;
    }

    /**
     * Has city
     *
     * @return boolean
     */
    public function hasDepartement()
    {
        if(isset($this->departement)) return true;
        return false;

    }

    /**
     * Set district
     *
     * @param string $district
     * @return Location
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return string 
     */
    public function getDistrict()
    {
            return $this->district;
    }

    /**
     * Get district id
     *
     * @return integer 
     */
    public function getDistrictId()
    {
            return (isset($this->district))? $this->district->getId() : null;
    }

    /**
     * Has district
     *
     * @return boolean
     */
    public function hasDistrict()
    {
        if(isset($this->district)) return true;
        return false;

    }
    /**
     * Set division
     *
     * @param string $division
     * @return Location
     */
    public function setDivision($division)
    {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return string 
     */
    public function getDivision()
    {
            return $this->division;
    }

    /**
     * Get division id
     *
     * @return integer 
     */
    public function getDivisionId()
    {
            return (isset($this->division))? $this->division->getId() : null;
    }

   /**
     * Has division
     *
     * @return boolean
     */
    public function hasDivision()
    {
        if(isset($this->division)) return true;
        return false;

    }
    /**
     * Set city
     *
     * @param string $city
     * @return Location
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Has city
     *
     * @return boolean
     */
    public function hasCity()
    {
        if(isset($this->city)) return true;
        return false;

    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
            return $this->city;
    }

    /**
     * Get city id
     *
     * @return integer 
     */
    public function getCityId()
    {
            return (isset($this->city))? $this->city->getId() : null;
    }


    public function setCityId()
    {
        //return null;
    }

    public function setCityName()
    {
        //return null;
    }

    /**
     * Get city name
     *
     * @return string 
     */
    public function getCityName()
    {
        if(isset($this->city))
            return $this->city->getName();
        else
            return '';
    }

    /**
     * Get last state
     *
     * @return string 
     */
    public function getLastState()
    {
       if(!empty($this->division)) return $this->division;
       if(!empty($this->district)) return $this->district;
       if(!empty($this->departement)) return $this->departement;
       if(!empty($this->region)) return $this->region;
    }

    /**
     * Get all states
     *
     * @return array 
     */
    public function getAllRegions()
    {
        $r = array();
        if(!empty($this->country)) $r[] = $this->country;
        if(!empty($this->region)) $r[] = $this->region;
        if(!empty($this->departement)) $r[] =  $this->departement;
        if(!empty($this->district)) $r[] =  $this->district;
        if(!empty($this->division)) $r[] =  $this->division;
        if(!empty($this->city)) $r[] = $this->city;
        return $r;
    }


}
