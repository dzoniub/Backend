<?php

namespace AppBundle\Annotation;

/**
 * @Annotation
 */

class CheckRequest
{
    private $name;

    public function __construct($options)
    {
        if(isset($options['value'])){
            $options['name'] = $options['value'];
            unset($options['value']);
        }
        foreach ($options as $key => $value){
            if(!property_exists($this, $key)){
                throw new \InvalidArgumentException(sprintf('Property %s does not exist', $key));
            }
            $this->$key = $value;
        }
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}