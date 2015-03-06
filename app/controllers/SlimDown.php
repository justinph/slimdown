<?php

namespace SlimDown;
use \Slim\Slim;

class SlimdownBase {

    function __construct() {
        $this->app = Slim::getInstance();
    }

    function staticPage($slug=''){

        $mdFile = $this->getTemplate($slug);

        if ($mdFile) {

            $pageContents = file_get_contents($mdFile);

            $parser = new \Mni\FrontYAML\Parser();
            $document = $parser->parse($pageContents);
            $yaml = $document->getYAML();
            $html = $document->getContent();

            $data = $yaml;
            $data['html'] = $html;

            $this->app->render(APP_ROOT.'/app/templates/default.tpl', $data);

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
    private function getTemplate($slug=''){
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




}