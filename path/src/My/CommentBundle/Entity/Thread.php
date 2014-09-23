<?php

namespace My\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Thread extends BaseThread
{
    /**
     * @var string $id
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    public function getContext()
    {
    	$r = explode('-',$this->id);
    	return $r[0];
    }

    public function getUid(){
    	$r = explode('-',$this->id);
    	return $r[1];
    }
}

?>