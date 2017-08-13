<?php

namespace AppBundle\EventListener;

use AppBundle\Annotation\CheckRequest;
use AppBundle\Exception\ControlerNotAvailableException;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerCheckRequestListener
{
    /** @var Reader */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())){
            return;
        }

        $request = $event->getRequest();
        $content = $request->getContent();
        $data = json_decode($content,true);

        $reflectionObject = new \ReflectionObject($controller[0]);
        $reflectionMethod = $reflectionObject->getMethod($controller[1]);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, CheckRequest::class);
        if(!$methodAnnotation){
            return;
        }
        $propertyName = $methodAnnotation->getName();

        if ($request->getContentType() !== 'json' ) {
            throw new ControlerNotAvailableException();
        }

        if(empty($content)){
            throw new ControlerNotAvailableException();
        }

        if(empty($data) || !array_key_exists('type', $data)) {
            throw new ControlerNotAvailableException();
        }

        foreach($reflectionMethod->getParameters() as $param){
            if($param->getName() !== $propertyName){
                continue;
            }
            if($request->attributes->has($propertyName)){
                return;
            }
            $request->attributes->set($propertyName, $data);
        }
    }
}