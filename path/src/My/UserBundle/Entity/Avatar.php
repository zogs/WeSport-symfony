<?php

namespace My\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="users_avatar")
 */
class Avatar
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(name="updated_date", type="datetime")
     * @Assert\NotBlank()
     */
    private $updated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    public $file;

    //filename
    public $filename;

    // propriété utilisé temporairement pour la suppression
    private $filenameForRemove = null;


    public function __construct()
    {
        $this->updated = new \Datetime();
        $this->path = $this->getDefaultAvatar(rand(1,10));

    }    

    public function getDefaultAvatar($i = 1)
    {
        return 'bundles/myuser/images/avatars/defaults/default_'.$i.'.gif';
    }

    public function isDefaultAvatar()
    {
        return is_numeric($this->path) == true ? true : false;
    }

    public function getWebPath()
    {        
        if(null === $this->path) return $this->getDefaultAvatar();
        if(is_readable($this->getUploadDir().'/'.$this->path)) return  $this->getUploadDir().'/'.$this->path;
        if($this->isDefaultAvatar()) return $this->getDefaultAvatar($this->path);
        return $this->getDefaultAvatar();
    }


    public function getAbsolutePath()
    {
        return null === $this->path ? null : __DIR__.'/../../../../web/'.$this->getWebPath();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'media/users/avatar';
    }

    protected function getAbsoluteUploadDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getSavingFilename()
    {
        if(isset($this->filename)) return $this->filename;

        return $this->id;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        
        if (null !== $this->file) {
            $this->path = $this->getSavingFilename().'.'.$this->file->guessExtension();
        }

        if(isset($this->updated) && is_string($this->updated)) {
            $this->updated = new \DateTime($this->updated);
        } 

    }



    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // vous devez lancer une exception ici si le fichier ne peut pas
        // être déplacé afin que l'entité ne soit pas persistée dans la
        // base de données comme le fait la méthode move() de UploadedFile
        $this->file->move($this->getAbsoluteUploadDir(), $this->getSavingFilename().'.'.$this->file->guessExtension());

        unset($this->file);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        //delete avatar file, except if its a default avatar
        if (!$this->isDefaultAvatar() && $this->filenameForRemove) {
            unlink($this->filenameForRemove);
        }
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Avatar
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Avatar
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set filename
     *
     * @param string $path
     * @return Avatar
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
