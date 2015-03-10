<?php

namespace SlimDown;
use \Slim\Slim;

class SlimdownBase {

    function __construct() {
        
        $this->flatStruct = array();
        $markdownDir = APP_ROOT .'/pages';

        $this->app = Slim::getInstance();
        $this->parser =  new \Mni\FrontYAML\Parser();

        //do actual file tree processing
        $this->getFileStruct($markdownDir);
        $this->processTreeInformation();
    }

    function staticPage($slug=array()){

        $slug = "/".implode( "/", $slug); //TODO: Need to resolve dupe slashes, probably

        if (!array_key_exists($slug, $this->flatStruct)){
            $this->app->notFound();
            return;
        }

        $this->data = (array) $this->flatStruct[$slug];   
        $fileContents = file_get_contents($this->data['fullpath']);

        $document = $this->parser->parse($fileContents);
        $html = $document->getContent();
        $this->data['html'] = $html;
        $this->setTemplate();

        $this->app->render($this->template, $this->data);
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
        if (isset($this->data->template) && !empty($this->data->template)){
            $template = APP_ROOT.'/app/templates/'.trim($this->data->template).'.tpl';
            if (file_exists($template)){
                $this->template = $template;
                return;
            }
        }
        //default
        $this->template = APP_ROOT.'/app/templates/default.tpl';
    }

    private function getFileStruct($dir){

        //should replace this at some point
        $command = "find ". $dir . " -name '*.md'";
        $list = array();

        exec ( $command, $list );

        $output = new \StdClass;
        $list = array_reverse($list);


        $pages = array();



        foreach ($list as $path) {
            $relpath = str_replace($dir, '', $path);
            $info = $this->getFileInfo($path);
            $info['fullpath'] = $path;

            $part = explode('/', $relpath);
            $relfile = str_replace('.md', '', $part[count($part)-1]);


            $slug = $relpath;
            if (substr($slug, -9) == '/index.md'){
                $slug = str_replace('index.md','', $slug);
            } else {
                $slug = str_replace('.md','/', $slug);
            }
            $pages[$slug] = (object) $info;

        }


        $this->flatStruct = $pages;
    }

    /**
     * Parse the front matter for each file and return an array
     * TODO: return false on invalid or corrupt data
     * @param  string $path absolute filepath to a markdown file to be parsed
     * @return array       Front matter data
     */
    private function getFileInfo($path){
        $pageContents = file_get_contents($path);
        $document = $this->parser->parse($pageContents);
        $info = $document->getYAML();
        return $info;
    }


    private function processTreeInformation(){

        //$flatStruct = clone($this->flatStruct);

        foreach ($this->flatStruct as $url => $info) {
            $pathParts = explode( '/', trim($url,"/"));
            //var_dump($paths);
            array_pop($pathParts);
            $parentPath = implode( "/",$pathParts);
            $parentPath = str_replace('//','/','/'.$parentPath."/");

            $myDepth = substr_count($url, '/');
            $parentDepth = substr_count($parentPath, '/');


            //figure out the parent object
            if (isset( $this->flatStruct[$parentPath] ) && $url !== '/' ){
                $parent = clone($this->flatStruct[$parentPath]);
                //make sure we don't end up with too much unneeded depth
                unset($parent->parent, $parent->siblings, $parent->children);
                $parent->url_path = $parentPath;
                $this->flatStruct[$url]->parent = $parent;
            }

            $children = array();
            $siblings = array();

            //loop through the page struct and find sub-paths to calculate siblings and children
            foreach($this->flatStruct as $potentialMatchPath => $potentialMatchInfo){
          
                $tempDepth = substr_count($potentialMatchPath, '/');  //current depth of potential match

                //siblings
                $potentialParentPath = substr($potentialMatchPath, 0, strlen($parentPath));
                if ($potentialParentPath === $parentPath 
                    && $tempDepth === $myDepth
                    && $url !== $potentialMatchPath){
                    $sib = clone($potentialMatchInfo);
                    unset($sib->parent, $sib->siblings, $sib->children);
                    $sib->url_path = $potentialMatchPath;
                    $siblings[] = $sib;
                }

                //figure out children
                $potentialChildrenPath = substr($potentialMatchPath, 0, strlen($url));
                if ($potentialChildrenPath === $url 
                    && ($myDepth+1) === $tempDepth){
                    $child = clone($potentialMatchInfo);
                    unset($child->parent, $child->siblings, $child->children);
                    $child->url_path = $potentialMatchPath;
                    $children[] = $child;
                } 
            }
            if (!empty($children)){
                $this->flatStruct[$url]->children = $children;
            }
            if (!empty($siblings)){
                $this->flatStruct[$url]->siblings = $siblings;
            }
        }
    }


    //TODO: Might be useful for globbing files

    // private function genFileStruct($dir){

    //     $ritit = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST); 
    //     $r = array(); 
    //     foreach ($ritit as $splFileInfo) { 

    //         $filename = $splFileInfo->getFilename();
    //         if ( substr($filename,0,1) !== '.'){
    //             $path = $splFileInfo->isDir() ? array($filename => array()) : array($filename); 
    //             for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) { 
    //                $path = array($ritit->getSubIterator($depth)->current()->getFilename() => $path); 
    //             } 
    //             $r = array_merge_recursive($r, $path);
    //         }
            
    //     }   
    //     return $r;
    // }





}