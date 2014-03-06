<?php


namespace My\WorldBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


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
     * Get region
     *
     * @return string 
     */
    public function getRegion()
    {
        return $this->region;
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
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get last state
     *
     * @return string 
     */
    public function getlastState()
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
    public function getallRegions()
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
