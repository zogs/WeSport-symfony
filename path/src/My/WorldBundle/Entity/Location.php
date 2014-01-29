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
     * @ORM\Column(name="country_id", type="integer", nullable=true)
     */
    protected $country;

    /**
     * @ORM\Column(name="region_id", type="integer", nullable=true)
     */
    protected $region;

    /**
     * @ORM\Column(name="departement_id", type="integer", nullable=true)
     */
    protected $departement;

    /**
     * @ORM\Column(name="district_id", type="integer", nullable=true)
     */
    protected $district;

    /**
     * @ORM\Column(name="division_id", type="integer", nullable=true)
     */
    protected $division;

    /**
     * @ORM\Column(name="city_id", type="integer", nullable=true)
     */
    protected $city;


    /**
     * Set id
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
        return $this->id;
    }

    /**
     * Set country
     *
     * @param integer $country
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
     * @return integer 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set region
     *
     * @param integer $region
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
     * @return integer 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set departement
     *
     * @param integer $departement
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
     * @return integer 
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set district
     *
     * @param integer $district
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
     * @return integer 
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set division
     *
     * @param integer $division
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
     * @return integer 
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set city
     *
     * @param integer $city
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
     * @return integer 
     */
    public function getCity()
    {
        return $this->city;
    }
}
