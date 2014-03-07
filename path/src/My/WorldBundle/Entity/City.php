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
	private $UFI;

	/**
	 * @ORM\Column(name="UNI", type="integer")
	 */
	private $UNI;

	/**
	 * @ORM\Column(name="CC1", type="string", length=2)
	 */
	private $CC1;

	/**
	 * @ORM\Column(name="DSG", type="string", length=5)
	 */
	private $DSG;

	/**
	 * @ORM\Column(name="ADM1", type="string", length=3)
	 */
	private $ADM1;

	/**
	 * @ORM\Column(name="ADM2", type="string", length=3)
	 */
	private $ADM2;

	/**
	 * @ORM\Column(name="ADM3", type="string", length=3)
	 */
	private $ADM3;

	/**
	 * @ORM\Column(name="ADM4", type="string", length=3)
	 */
	private $ADM4;

	/**
	 * @ORM\Column(name="NT", type="string", length=1)
	 */
	private $NT;

	/**
	 * @ORM\Column(name="LC", type="string", length=3)
	 */
	private $LC;

	/**
	 * @ORM\Column(name="SHORTFORM", type="string", length=128)
	 */
	private $SHORTFORM;

	/**
	 * @ORM\Column(name="FULLNAME", type="string", length=200)
	 */
	private $FULLNAME;

	/**
	 * @ORM\Column(name="FULLNAMEND", type="string", length=200)
	 */
	private $FULLNAMEND;

	/**
	 * @ORM\Column(name="CHARACTERS", type="string", length=24)
	 */
	private $CHARACTERS;

	/**
	 * @ORM\Column(name="LATITUDE", type="float")
	 */
	private $LATITUDE;

	/**
	 * @ORM\Column(name="LONGITUDE", type="float")
	 */
	private $LONGITUDE;

	/**
	 * @ORM\Column(name="DMSLAT", type="integer")
	 */
	private $DMSLAT;

	/**
	 * @ORM\Column(name="DMSLONG", type="integer")
	 */
	private $DMSLONG;

    /**
     * @ORM\Column(name="SOUNDEX", type="string", length=20, nullable=true)
     */
    private $SOUNDEX;

    /**
     * @ORM\Column(name="METAPHONE", type="string", length=22, nullable=true)
     */
    private $METAPHONE;

    /**
     * @ORM\Column(name="CP", type="string", length=255, nullable=true)
     */
    private $CP;

    /**
     * @ORM\Column(name="POP", type="integer", nullable=true)
     */
    private $POP;

    /**
     * @ORM\Column(name="POP_ORDER", type="integer", nullable=true)
     */
    private $POP_ORDER;

    /**
     * @ORM\Column(name="SFC", type="integer", nullable=true)
     */
    private $SFC;

    /**
     * @ORM\Column(name="SFC_ORDER", type="integer", nullable=true)
     */
    private $SFC_ORDER;

    private $level;

    public function getLevel()
    {
        return 'city';
    }


    public function exist()
    {
        if($this->id!=NULL) return 1;
        return 0;
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
     * Set UFI
     *
     * @param integer $uFI
     * @return City
     */
    public function setUFI($uFI)
    {
        $this->UFI = $uFI;

        return $this;
    }

    /**
     * Get UFI
     *
     * @return integer 
     */
    public function getUFI()
    {
        return $this->UFI;
    }

    /**
     * Set UNI
     *
     * @param integer $uNI
     * @return City
     */
    public function setUNI($uNI)
    {
        $this->UNI = $uNI;

        return $this;
    }

    /**
     * Get UNI
     *
     * @return integer 
     */
    public function getUNI()
    {
        return $this->UNI;
    }

    /**
     * Set CC1
     *
     * @param string $cC1
     * @return City
     */
    public function setCC1($cC1)
    {
        $this->CC1 = $cC1;

        return $this;
    }

    /**
     * Get CC1
     *
     * @return string 
     */
    public function getCC1()
    {
        return $this->CC1;
    }

    /**
     * Set DSG
     *
     * @param string $dSG
     * @return City
     */
    public function setDSG($dSG)
    {
        $this->DSG = $dSG;

        return $this;
    }

    /**
     * Get DSG
     *
     * @return string 
     */
    public function getDSG()
    {
        return $this->DSG;
    }

    /**
     * Set ADM1
     *
     * @param string $aDM1
     * @return City
     */
    public function setADM1($aDM1)
    {
        $this->ADM1 = $aDM1;

        return $this;
    }

    /**
     * Get ADM1
     *
     * @return string 
     */
    public function getADM1()
    {
        return $this->ADM1;
    }

    /**
     * Set ADM2
     *
     * @param string $aDM2
     * @return City
     */
    public function setADM2($aDM2)
    {
        $this->ADM2 = $aDM2;

        return $this;
    }

    /**
     * Get ADM2
     *
     * @return string 
     */
    public function getADM2()
    {
        return $this->ADM2;
    }

    /**
     * Set ADM3
     *
     * @param string $aDM3
     * @return City
     */
    public function setADM3($aDM3)
    {
        $this->ADM3 = $aDM3;

        return $this;
    }

    /**
     * Get ADM3
     *
     * @return string 
     */
    public function getADM3()
    {
        return $this->ADM3;
    }

    /**
     * Set ADM4
     *
     * @param string $aDM4
     * @return City
     */
    public function setADM4($aDM4)
    {
        $this->ADM4 = $aDM4;

        return $this;
    }

    /**
     * Get ADM4
     *
     * @return string 
     */
    public function getADM4()
    {
        return $this->ADM4;
    }

    /**
     * Set NT
     *
     * @param string $nT
     * @return City
     */
    public function setNT($nT)
    {
        $this->NT = $nT;

        return $this;
    }

    /**
     * Get NT
     *
     * @return string 
     */
    public function getNT()
    {
        return $this->NT;
    }

    /**
     * Set LC
     *
     * @param string $lC
     * @return City
     */
    public function setLC($lC)
    {
        $this->LC = $lC;

        return $this;
    }

    /**
     * Get LC
     *
     * @return string 
     */
    public function getLC()
    {
        return $this->LC;
    }

    /**
     * Set SHORTFORM
     *
     * @param string $sHORTFORM
     * @return City
     */
    public function setSHORTFORM($sHORTFORM)
    {
        $this->SHORTFORM = $sHORTFORM;

        return $this;
    }

    /**
     * Get SHORTFORM
     *
     * @return string 
     */
    public function getSHORTFORM()
    {
        return $this->SHORTFORM;
    }

    /**
     * Set FULLNAME
     *
     * @param string $fULLNAME
     * @return City
     */
    public function setFULLNAME($fULLNAME)
    {
        $this->FULLNAME = $fULLNAME;

        return $this;
    }

    /**
     * Get FULLNAME
     *
     * @return string 
     */
    public function getFULLNAME()
    {
        return $this->FULLNAME;
    }

    /**
     * Set FULLNAMEND
     *
     * @param string $fULLNAMEND
     * @return City
     */
    public function setFULLNAMEND($fULLNAMEND)
    {
        $this->FULLNAMEND = $fULLNAMEND;

        return $this;
    }

    /**
     * Get FULLNAMEND
     *
     * @return string 
     */
    public function getFULLNAMEND()
    {
        return $this->FULLNAMEND;
    }

    /**
     * Set Name
     *
     * @param string $fULLNAMEND
     * @return City
     */
    public function setName($name)
    {
        $this->FULLNAMEND = $name;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->FULLNAMEND;
    }

    /**
     * Set CHARACTERS
     *
     * @param string $cHARACTERS
     * @return City
     */
    public function setCHARACTERS($cHARACTERS)
    {
        $this->CHARACTERS = $cHARACTERS;

        return $this;
    }

    /**
     * Get CHARACTERS
     *
     * @return string 
     */
    public function getCHARACTERS()
    {
        return $this->CHARACTERS;
    }

    /**
     * Set LATITUDE
     *
     * @param string $lATITUDE
     * @return City
     */
    public function setLATITUDE($lATITUDE)
    {
        $this->LATITUDE = $lATITUDE;

        return $this;
    }

    /**
     * Get LATITUDE
     *
     * @return string 
     */
    public function getLATITUDE()
    {
        return $this->LATITUDE;
    }

    /**
     * Set LONGITUDE
     *
     * @param string $lONGITUDE
     * @return City
     */
    public function setLONGITUDE($lONGITUDE)
    {
        $this->LONGITUDE = $lONGITUDE;

        return $this;
    }

    /**
     * Get LONGITUDE
     *
     * @return string 
     */
    public function getLONGITUDE()
    {
        return $this->LONGITUDE;
    }

    /**
     * Set DMSLAT
     *
     * @param integer $dMSLAT
     * @return City
     */
    public function setDMSLAT($dMSLAT)
    {
        $this->DMSLAT = $dMSLAT;

        return $this;
    }

    /**
     * Get DMSLAT
     *
     * @return integer 
     */
    public function getDMSLAT()
    {
        return $this->DMSLAT;
    }

    /**
     * Set DMSLONG
     *
     * @param integer $dMSLONG
     * @return City
     */
    public function setDMSLONG($dMSLONG)
    {
        $this->DMSLONG = $dMSLONG;

        return $this;
    }

    /**
     * Get DMSLONG
     *
     * @return integer 
     */
    public function getDMSLONG()
    {
        return $this->DMSLONG;
    }

    /**
     * Get LATITUDE
     *
     * @return decimal 
     */
    public function getLat()
    {
        return $this->LATITUDE;
    }

    /**
     * Get LONGITUDE
     *
     * @return decimal 
     */
    public function getLon()
    {
        return $this->LONGITUDE;
    }
}
