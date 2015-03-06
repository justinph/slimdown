<?php

namespace SlimDown;
use \Slim\Slim;

class SlimdownBase {

    function __construct() {
        $this->app = Slim::getInstance();
    }

    function staticPage($slug=''){

        $mdFile = $this->getPage($slug);

        if ($mdFile) {

            $pageContents = file_get_contents($mdFile);

            $parser = new \Mni\FrontYAML\Parser();
            $document = $parser->parse($pageContents);
            $yaml = $document->getYAML();
            $html = $document->getContent();

            $this->data = $yaml;
            $this->data['html'] = $html;

            $this->setTemplate();
            $this->app->render($this->template, $this->data);
        } else {
            $this->app->notFound();
            return;
        }
    }

    /**
     * Figures out the markdown file to use
     * @param  string $slug [description]
     * @return [type]       [description]
     */
    private function getPage($slug=''){
        if (empty($slug)){
            $slug = array('index');
        }

        //try initial path
        $page =  APP_ROOT .'/pages/'. rtrim(implode('/', $slug), '/') . '.md';
        if (file_exists($page)){
            return $page;
        }
        $page =  APP_ROOT .'/pages/'. rtrim(implode('/', $slug), '/') . '/index.md';
        if (file_exists($page)){
            return $page;
        }
        return false;

    }

    /**
     * Figure out what template to use based on the front matter
     */
    private function setTemplate(){
        if (isset($this->data['template']) && !empty($this->data['template'])){
            $template = APP_ROOT.'/app/templates/'.trim($this->data['template']).'.tpl';
            if (file_exists($template)){
                $this->template = $template;
                return;
            }
        }
        //default
        $this->template = APP_ROOT.'/app/templates/default.tpl';
    }




}