<?php
namespace Framework;

abstract class BaseController
{
    /**
     * @var $container \Framework\Container
     */
    protected $container;

    public function setContainer(Container $container){
        $this->container = $container;
        return $this;
    }

    protected function render($template, $params = []){

        $twig = $this->container->get('twig');

        $folder =str_replace(['Controller'], '',get_class($this));
        $folder = trim($folder,'\\');
        $folder = str_replace('\\', DS,$folder);
        $template = $folder.DS.$template;
        if (!file_exists(VIEW_DIR.$template)){
            throw new \Exception('Template not found');
        }
        return $twig->render($template,$params);
    }
}