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
	 * @ORM\Column(name="DSG", type="string", length=4)
	 */
	private $dsg;

	/**
	 * @ORM\Column(name="ADM_PARENT", type="string", length=3)
	 */
	private $adm_parent;

	/**
	 * @ORM\Column(name="ADM_CODE", type="string", length=3)
	 */
	private $adm_code;

	/**
	 * @ORM\Column(name="NT", type="smallint")
	 */
	private $nt;

	/**
	 * @ORM\Column(name="LC", type="string", length=3)
	 */
	private $lang;

	/**
	 * @ORM\Column(name="SHORTFORM", type="string", length=56)
	 */
	private $shortform;

	/**
	 * @ORM\Column(name="FULLNAME", type="string", length=83)
	 */
	private $fullname;

	/**
	 * @ORM\Column(name="FULLNAMEND", type="string", length=79)
	 */
	private $name;

	/**
	 * @ORM\Column(name="CHARACTERS", type="string", length=18)
	 */
	private $characters;

    private $level;

    public function getLevel()
    {
        if($this->dsg=='ADM1') return 'region';
        if($this->dsg=='ADM2') return 'department';
        if($this->dsg=='ADM3') return 'district';
        if($this->dsg=='ADM4') return 'division';
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
     * Set ufi
     *
     * @param integer $ufi
     * @return State
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
     * @return State
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
     * @return State
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
     * @return State
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
     * Set adm_parent
     *
     * @param string $admParent
     * @return State
     */
    public function setAdmParent($admParent)
    {
        $this->adm_parent = $admParent;

        return $this;
    }

    /**
     * Get adm_parent
     *
     * @return string 
     */
    public function getAdmParent()
    {
        return $this->adm_parent;
    }

    /**
     * Set adm_code
     *
     * @param string $admCode
     * @return State
     */
    public function setAdmCode($admCode)
    {
        $this->adm_code = $admCode;

        return $this;
    }

    /**
     * Get adm_code
     *
     * @return string 
     */
    public function getAdmCode()
    {
        return $this->adm_code;
    }

    /**
     * Set nt
     *
     * @param integer $nt
     * @return State
     */
    public function setNt($nt)
    {
        $this->nt = $nt;

        return $this;
    }

    /**
     * Get nt
     *
     * @return integer 
     */
    public function getNt()
    {
        return $this->nt;
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
     * Set shortform
     *
     * @param string $shortform
     * @return State
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
     * @return State
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
     * Set characters
     *
     * @param string $characters
     * @return State
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
}
