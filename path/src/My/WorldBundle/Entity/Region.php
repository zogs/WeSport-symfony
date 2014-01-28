<?php


namespace My\WorldBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Region
 *
 * @ORM\Table(name="world_regions")
 * @ORM\Entity(repositoryClass="My\WorldBundle\Entity\RegionRepository")
 */
class Region 
{	
	/**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

	/**
	 * @ORM\Column(name="REGION_ID", type="integer", length=3)
	 */
	private $region_id;

	/**
	 * @ORM\Column(name="REGION_PARENT", type="string", length=3)
	 */
	private $parent_id;

	/**
	 * @ORM\Column(name="REGION_NAME", type="string", length=56)
	 */
	private $name;

	/**
	 * @ORM\Column(name="LC", type="string", length=3)
	 */
	private $lang;

	/**
	 * @ORM\Column(name="CHARACTERS", type="string", length=18)
	 */
	private $characters;


    /**
     * Set id
     *
     * @param integer $id
     * @return Region
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
     * Set region_id
     *
     * @param integer $regionId
     * @return Region
     */
    public function setRegionId($regionId)
    {
        $this->region_id = $regionId;

        return $this;
    }

    /**
     * Get region_id
     *
     * @return integer 
     */
    public function getRegionId()
    {
        return $this->region_id;
    }

    /**
     * Set parent_id
     *
     * @param string $parentId
     * @return Region
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;

        return $this;
    }

    /**
     * Get parent_id
     *
     * @return string 
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Region
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
     * Set lang
     *
     * @param string $lang
     * @return Region
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
     * Set characters
     *
     * @param string $characters
     * @return Region
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
