<?php


namespace My\WorldBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 * 
 * @ORM\Table(name="world_country", indexes={@ORM\Index(name="CC1", columns={"CC1"})})
 * @ORM\Entity(repositoryClass="My\WorldBundle\Entity\CountryRepository")
 */
class Country 
{
	/**
	 * @ORM\Id
	 * @ORM\Column(name="id", type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(name="CC1", type="string", length=2)
	 */
	private $code;

	/**
	 * @ORM\Column(name="LO", type="string", length=3)
	 */
	private $lang;

	/**
	 * @ORM\Column(name="FULLNAME", type="string", length=200)
	 */
	private $name;

	/**
	 * @ORM\Column(name="REGION_ID", type="integer")
	 */
	private $region;

	/**
	 * @ORM\Column(name="ISO_ALPHA2", type="string", length=2)
	 */
	private $iso_2;

	/**
	 * @ORM\Column(name="ISO_ALPHA3", type="string", length=3)
	 */
	private $iso_3;

	/**
	 * @ORM\Column(name="ISO_4217", type="string", length=3)
	 */
	private $iso_4;

	/**
	 * @ORM\Column(name="ISO_NUMERIC", type="smallint")
	 */
	private $iso_numeric;

	/**
	 * @ORM\Column(name="INTERNET", type="string", length=2)
	 */
	private $internet_domain;

	/**
	 * @ORM\Column(name="PHONE_CC", type="string", length=11)
	 */
	private $phone_code;

	/**
	 * @ORM\Column(name="CC2", type="string", length=2)
	 */
	private $included_in_country;

	/**
	 * @ORM\Column(name="COMMENT", type="string", length=255)
	 */
	private $description;



    /**
     * Set id
     *
     * @param integer $id
     * @return Country
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
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return Country
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
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
     * Set region
     *
     * @param integer $region
     * @return Country
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
     * Set iso_2
     *
     * @param string $iso2
     * @return Country
     */
    public function setIso2($iso2)
    {
        $this->iso_2 = $iso2;

        return $this;
    }

    /**
     * Get iso_2
     *
     * @return string 
     */
    public function getIso2()
    {
        return $this->iso_2;
    }

    /**
     * Set iso_3
     *
     * @param string $iso3
     * @return Country
     */
    public function setIso3($iso3)
    {
        $this->iso_3 = $iso3;

        return $this;
    }

    /**
     * Get iso_3
     *
     * @return string 
     */
    public function getIso3()
    {
        return $this->iso_3;
    }

    /**
     * Set iso_4
     *
     * @param string $iso4
     * @return Country
     */
    public function setIso4($iso4)
    {
        $this->iso_4 = $iso4;

        return $this;
    }

    /**
     * Get iso_4
     *
     * @return string 
     */
    public function getIso4()
    {
        return $this->iso_4;
    }

    /**
     * Set iso_numeric
     *
     * @param integer $isoNumeric
     * @return Country
     */
    public function setIsoNumeric($isoNumeric)
    {
        $this->iso_numeric = $isoNumeric;

        return $this;
    }

    /**
     * Get iso_numeric
     *
     * @return integer 
     */
    public function getIsoNumeric()
    {
        return $this->iso_numeric;
    }

    /**
     * Set internet_domain
     *
     * @param string $internetDomain
     * @return Country
     */
    public function setInternetDomain($internetDomain)
    {
        $this->internet_domain = $internetDomain;

        return $this;
    }

    /**
     * Get internet_domain
     *
     * @return string 
     */
    public function getInternetDomain()
    {
        return $this->internet_domain;
    }

    /**
     * Set phone_code
     *
     * @param string $phoneCode
     * @return Country
     */
    public function setPhoneCode($phoneCode)
    {
        $this->phone_code = $phoneCode;

        return $this;
    }

    /**
     * Get phone_code
     *
     * @return string 
     */
    public function getPhoneCode()
    {
        return $this->phone_code;
    }

    /**
     * Set included_in_country
     *
     * @param string $includedInCountry
     * @return Country
     */
    public function setIncludedInCountry($includedInCountry)
    {
        $this->included_in_country = $includedInCountry;

        return $this;
    }

    /**
     * Get included_in_country
     *
     * @return string 
     */
    public function getIncludedInCountry()
    {
        return $this->included_in_country;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Country
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
