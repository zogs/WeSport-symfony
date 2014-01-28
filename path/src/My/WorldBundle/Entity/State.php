<?php


namespace My\WorldBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * State
 * 
 * @ORM\Table(name="world_states", indexes={
 * 											@ORM\Index(name="FindStates", columns={"CC1","ADM_PARENT","DSG"}),
 *											@ORM\Index(name="findAState", columns={"ADM_CODE"})
 *											})
 * @ORM\Entity(repositoryClass="My\WorldBundle\Entity\StateRepository")
 */
class State 
{
	/**
	 * @ORM\Id
	 * @ORM\Column(name="id", type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(name="CHAR_CODE", type="string", length=1)
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
	 * @ORM\Column(name="DSG", type="string", length=4)
	 */
	private $DSG;

	/**
	 * @ORM\Column(name="ADM_PARENT", type="string", length=3)
	 */
	private $ADM_PARENT;

	/**
	 * @ORM\Column(name="ADM_CODE", type="string", length=3)
	 */
	private $ADM_CODE;

	/**
	 * @ORM\Column(name="NT", type="smallint")
	 */
	private $NT;

	/**
	 * @ORM\Column(name="LC", type="string", length=3)
	 */
	private $lang;

	/**
	 * @ORM\Column(name="SHORTFORM", type="string", length=56)
	 */
	private $SHOTFORM;

	/**
	 * @ORM\Column(name="FULLNAME", type="string", length=83)
	 */
	private $FULLNAME;

	/**
	 * @ORM\Column(name="FULLNAMEND", type="string", length=79)
	 */
	private $name;

	/**
	 * @ORM\Column(name="CHARACTERS", type="string", length=18)
	 */
	private $CHARACTERS;



    /**
     * Set id
     *
     * @param integer $id
     * @return State
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
     * @param string $charCode
     * @return State
     */
    public function setCharCode($charCode)
    {
        $this->char_code = $charCode;

        return $this;
    }

    /**
     * Get char_code
     *
     * @return string 
     */
    public function getCharCode()
    {
        return $this->char_code;
    }

    /**
     * Set UFI
     *
     * @param integer $uFI
     * @return State
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
     * @return State
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
     * @return State
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
     * @return State
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
     * Set ADM_PARENT
     *
     * @param string $aDMPARENT
     * @return State
     */
    public function setADMPARENT($aDMPARENT)
    {
        $this->ADM_PARENT = $aDMPARENT;

        return $this;
    }

    /**
     * Get ADM_PARENT
     *
     * @return string 
     */
    public function getADMPARENT()
    {
        return $this->ADM_PARENT;
    }

    /**
     * Set ADM_CODE
     *
     * @param string $aDMCODE
     * @return State
     */
    public function setADMCODE($aDMCODE)
    {
        $this->ADM_CODE = $aDMCODE;

        return $this;
    }

    /**
     * Get ADM_CODE
     *
     * @return string 
     */
    public function getADMCODE()
    {
        return $this->ADM_CODE;
    }

    /**
     * Set NT
     *
     * @param integer $nT
     * @return State
     */
    public function setNT($nT)
    {
        $this->NT = $nT;

        return $this;
    }

    /**
     * Get NT
     *
     * @return integer 
     */
    public function getNT()
    {
        return $this->NT;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return State
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
     * Set SHOTFORM
     *
     * @param string $sHOTFORM
     * @return State
     */
    public function setSHOTFORM($sHOTFORM)
    {
        $this->SHOTFORM = $sHOTFORM;

        return $this;
    }

    /**
     * Get SHOTFORM
     *
     * @return string 
     */
    public function getSHOTFORM()
    {
        return $this->SHOTFORM;
    }

    /**
     * Set FULLNAME
     *
     * @param string $fULLNAME
     * @return State
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
     * Set name
     *
     * @param string $name
     * @return State
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
     * Set CHARACTERS
     *
     * @param string $cHARACTERS
     * @return State
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
}
