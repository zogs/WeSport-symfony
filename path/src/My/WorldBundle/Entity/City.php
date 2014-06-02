<?php


namespace My\WorldBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 * 
 * @ORM\Table(name="world_cities", indexes={
 * 											@ORM\Index(name="CityUNI", columns={"UNI"}),
 *											@ORM\Index(name="CityName", columns={"FULLNAMEND"}),
 *											@ORM\Index(name="FindCities", columns={"CC1","ADM1","ADM2","ADM3","ADM4"})
 *											})
 * @ORM\Entity(repositoryClass="My\WorldBundle\Entity\CityRepository")
 */
class City 
{
	/**
	 * @ORM\Id
	 * @ORM\Column(name="id", type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(name="CHAR_CODE", type="smallint")
	 */
	private $char_code;

	/**
	 * @ORM\Column(name="UFI", type="integer")
	 */
	private $ufi;

	/**
	 * @ORM\Column(name="UNI", type="integer")
	 */
	private $uni;

	/**
	 * @ORM\Column(name="CC1", type="string", length=2)
	 */
	private $cc1;

	/**
	 * @ORM\Column(name="DSG", type="string", length=5)
	 */
	private $dsg;

	/**
	 * @ORM\Column(name="ADM1", type="string", length=3)
	 */
	private $adm1;

	/**
	 * @ORM\Column(name="ADM2", type="string", length=3)
	 */
	private $adm2;

	/**
	 * @ORM\Column(name="ADM3", type="string", length=3)
	 */
	private $adm3;

	/**
	 * @ORM\Column(name="ADM4", type="string", length=3)
	 */
	private $adm4;

	/**
	 * @ORM\Column(name="NT", type="string", length=1)
	 */
	private $nt;

	/**
	 * @ORM\Column(name="LC", type="string", length=3)
	 */
	private $lc;

	/**
	 * @ORM\Column(name="SHORTFORM", type="string", length=128)
	 */
	private $shortform;

	/**
	 * @ORM\Column(name="FULLNAME", type="string", length=200)
	 */
	private $fullname;

	/**
	 * @ORM\Column(name="FULLNAMEND", type="string", length=200)
	 */
	private $fullnamed;

	/**
	 * @ORM\Column(name="CHARACTERS", type="string", length=24)
	 */
	private $characters;

	/**
	 * @ORM\Column(name="LATITUDE", type="float")
	 */
	private $latitude;

	/**
	 * @ORM\Column(name="LONGITUDE", type="float")
	 */
	private $longitude;

	/**
	 * @ORM\Column(name="DMSLAT", type="integer")
	 */
	private $dmslat;

	/**
	 * @ORM\Column(name="DMSLONG", type="integer")
	 */
	private $dmslong;

    /**
     * @ORM\Column(name="SOUNDEX", type="string", length=20, nullable=true)
     */
    private $soundex;

    /**
     * @ORM\Column(name="METAPHONE", type="string", length=22, nullable=true)
     */
    private $metaphone;

    /**
     * @ORM\Column(name="CP", type="string", length=255, nullable=true)
     */
    private $cp;

    /**
     * @ORM\Column(name="POP", type="integer", nullable=true)
     */
    private $pop;

    /**
     * @ORM\Column(name="POP_ORDER", type="integer", nullable=true)
     */
    private $pop_order;

    /**
     * @ORM\Column(name="SFC", type="integer", nullable=true)
     */
    private $sfc;

    /**
     * @ORM\Column(name="SFC_ORDER", type="integer", nullable=true)
     */
    private $sfc_order;

    private $level = 'city';

    public function getLevel()
    {
        return $this->level;
    }

    public function getLastAdm()
    {
        if(!empty($this->adm4)) return $this->adm4;
        if(!empty($this->adm3)) return $this->adm3;
        if(!empty($this->adm2)) return $this->adm2;
        if(!empty($this->adm1)) return $this->adm1;
        if(!empty($this->cc1)) return $this->cc1;
    }

    public function getLastAdmLevel()
    {
        if(!empty($this->adm4)) return 'adm4';
        if(!empty($this->adm3)) return 'adm3';
        if(!empty($this->adm2)) return 'adm2';
        if(!empty($this->adm1)) return 'adm1';
        if(!empty($this->cc1)) return 'CC1';
    }


    public function exist()
    {
        if($this->id!=NULL) return 1;
        return 0;
    }

    public function getName()
    {
        return $this->fullnamed;
    }

 


    /**
     * Set id
     *
     * @param integer $id
     * @return City
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
     * Set char_code
     *
     * @param integer $charCode
     * @return City
     */
    public function setCharCode($charCode)
    {
        $this->char_code = $charCode;

        return $this;
    }

    /**
     * Get char_code
     *
     * @return integer 
     */
    public function getCharCode()
    {
        return $this->char_code;
    }

    /**
     * Set ufi
     *
     * @param integer $ufi
     * @return City
     */
    public function setUfi($ufi)
    {
        $this->ufi = $ufi;

        return $this;
    }

    /**
     * Get ufi
     *
     * @return integer 
     */
    public function getUfi()
    {
        return $this->ufi;
    }

    /**
     * Set uni
     *
     * @param integer $uni
     * @return City
     */
    public function setUni($uni)
    {
        $this->uni = $uni;

        return $this;
    }

    /**
     * Get uni
     *
     * @return integer 
     */
    public function getUni()
    {
        return $this->uni;
    }

    /**
     * Set cc1
     *
     * @param string $cc1
     * @return City
     */
    public function setCc1($cc1)
    {
        $this->cc1 = $cc1;

        return $this;
    }

    /**
     * Get cc1
     *
     * @return string 
     */
    public function getCc1()
    {
        return $this->cc1;
    }

    /**
     * Set dsg
     *
     * @param string $dsg
     * @return City
     */
    public function setDsg($dsg)
    {
        $this->dsg = $dsg;

        return $this;
    }

    /**
     * Get dsg
     *
     * @return string 
     */
    public function getDsg()
    {
        return $this->dsg;
    }

    /**
     * Set adm1
     *
     * @param string $adm1
     * @return City
     */
    public function setAdm1($adm1)
    {
        $this->adm1 = $adm1;

        return $this;
    }

    /**
     * Get adm1
     *
     * @return string 
     */
    public function getAdm1()
    {
        return $this->adm1;
    }

    /**
     * Set adm2
     *
     * @param string $adm2
     * @return City
     */
    public function setAdm2($adm2)
    {
        $this->adm2 = $adm2;

        return $this;
    }

    /**
     * Get adm2
     *
     * @return string 
     */
    public function getAdm2()
    {
        return $this->adm2;
    }

    /**
     * Set adm3
     *
     * @param string $adm3
     * @return City
     */
    public function setAdm3($adm3)
    {
        $this->adm3 = $adm3;

        return $this;
    }

    /**
     * Get adm3
     *
     * @return string 
     */
    public function getAdm3()
    {
        return $this->adm3;
    }

    /**
     * Set adm4
     *
     * @param string $adm4
     * @return City
     */
    public function setAdm4($adm4)
    {
        $this->adm4 = $adm4;

        return $this;
    }

    /**
     * Get adm4
     *
     * @return string 
     */
    public function getAdm4()
    {
        return $this->adm4;
    }

    /**
     * Set nt
     *
     * @param string $nt
     * @return City
     */
    public function setNt($nt)
    {
        $this->nt = $nt;

        return $this;
    }

    /**
     * Get nt
     *
     * @return string 
     */
    public function getNt()
    {
        return $this->nt;
    }

    /**
     * Set lc
     *
     * @param string $lc
     * @return City
     */
    public function setLc($lc)
    {
        $this->lc = $lc;

        return $this;
    }

    /**
     * Get lc
     *
     * @return string 
     */
    public function getLc()
    {
        return $this->lc;
    }

    /**
     * Set shortform
     *
     * @param string $shortform
     * @return City
     */
    public function setShortform($shortform)
    {
        $this->shortform = $shortform;

        return $this;
    }

    /**
     * Get shortform
     *
     * @return string 
     */
    public function getShortform()
    {
        return $this->shortform;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     * @return City
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set fullnamed
     *
     * @param string $fullnamed
     * @return City
     */
    public function setFullnamed($fullnamed)
    {
        $this->fullnamed = $fullnamed;

        return $this;
    }

    /**
     * Get fullnamed
     *
     * @return string 
     */
    public function getFullnamed()
    {
        return $this->fullnamed;
    }

    /**
     * Set characters
     *
     * @param string $characters
     * @return City
     */
    public function setCharacters($characters)
    {
        $this->characters = $characters;

        return $this;
    }

    /**
     * Get characters
     *
     * @return string 
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return City
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return City
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set dmslat
     *
     * @param integer $dmslat
     * @return City
     */
    public function setDmslat($dmslat)
    {
        $this->dmslat = $dmslat;

        return $this;
    }

    /**
     * Get dmslat
     *
     * @return integer 
     */
    public function getDmslat()
    {
        return $this->dmslat;
    }

    /**
     * Set dmslong
     *
     * @param integer $dmslong
     * @return City
     */
    public function setDmslong($dmslong)
    {
        $this->dmslong = $dmslong;

        return $this;
    }

    /**
     * Get dmslong
     *
     * @return integer 
     */
    public function getDmslong()
    {
        return $this->dmslong;
    }

    /**
     * Set soundex
     *
     * @param string $soundex
     * @return City
     */
    public function setSoundex($soundex)
    {
        $this->soundex = $soundex;

        return $this;
    }

    /**
     * Get soundex
     *
     * @return string 
     */
    public function getSoundex()
    {
        return $this->soundex;
    }

    /**
     * Set metaphone
     *
     * @param string $metaphone
     * @return City
     */
    public function setMetaphone($metaphone)
    {
        $this->metaphone = $metaphone;

        return $this;
    }

    /**
     * Get metaphone
     *
     * @return string 
     */
    public function getMetaphone()
    {
        return $this->metaphone;
    }

    /**
     * Set cp
     *
     * @param string $cp
     * @return City
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string 
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set pop
     *
     * @param integer $pop
     * @return City
     */
    public function setPop($pop)
    {
        $this->pop = $pop;

        return $this;
    }

    /**
     * Get pop
     *
     * @return integer 
     */
    public function getPop()
    {
        return $this->pop;
    }

    /**
     * Set pop_order
     *
     * @param integer $popOrder
     * @return City
     */
    public function setPopOrder($popOrder)
    {
        $this->pop_order = $popOrder;

        return $this;
    }

    /**
     * Get pop_order
     *
     * @return integer 
     */
    public function getPopOrder()
    {
        return $this->pop_order;
    }

    /**
     * Set sfc
     *
     * @param integer $sfc
     * @return City
     */
    public function setSfc($sfc)
    {
        $this->sfc = $sfc;

        return $this;
    }

    /**
     * Get sfc
     *
     * @return integer 
     */
    public function getSfc()
    {
        return $this->sfc;
    }

    /**
     * Set sfc_order
     *
     * @param integer $sfcOrder
     * @return City
     */
    public function setSfcOrder($sfcOrder)
    {
        $this->sfc_order = $sfcOrder;

        return $this;
    }

    /**
     * Get sfc_order
     *
     * @return integer 
     */
    public function getSfcOrder()
    {
        return $this->sfc_order;
    }
}
