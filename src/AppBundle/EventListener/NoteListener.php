<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Note;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class NoteListener
{
    private $imagePath;
    public function __construct($options)
    {
        $this->imagePath = $options;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if(!$object instanceof Note){
            return;
        }

        if($object->getType() === 'image'){
            $fileSystem = new Filesystem();
            $fileName = substr(strrchr($object->getContent(),'/'),1);
            $fileSystem->remove($this->imagePath.$fileName);
        }
    }
}