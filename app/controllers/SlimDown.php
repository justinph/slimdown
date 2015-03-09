<?php

namespace SlimDown;
use \Slim\Slim;

class SlimdownBase {

    function __construct() {
        $this->app = Slim::getInstance();
        $this->parser =  new \Mni\FrontYAML\Parser();
    }

    function staticPage($slug=''){

        $flatStruct = $this->getFileStruct(APP_ROOT .'/pages');


        $flatStruct = $this->processTreeInformation($flatStruct);

        echo "<!-- \n";
        var_dump($flatStruct);
        echo "\n-->\n";


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


        return $pages;
    }


    private function getFileInfo($path){
        $pageContents = file_get_contents($path);
        $document = $this->parser->parse($pageContents);
        $info = $document->getYAML();
        return $info;
    }


    private function processTreeInformation($flatStruct){

        //var_dump($flatStruct);

        foreach ($flatStruct as $url => $info) {
            $pathParts = explode( '/', trim($url,"/"));
            //var_dump($paths);
            array_pop($pathParts);
            $parentPath = implode( "/",$pathParts);
            $parentPath = str_replace('//','/','/'.$parentPath."/");

            $myDepth = substr_count($url, '/');
            $parentDepth = substr_count($parentPath, '/');

            // echo "crrent: ".$url."\n";
            // echo 'parent: '.$parentPath."\n";

            // echo "crrent depth: ".$myDepth."\n";
            // echo 'parent depth: '.$parentDepth."\n";

            //figure out the parent object
            if (isset( $flatStruct[$parentPath] ) && $url !== '/' ){
                $parent = clone($flatStruct[$parentPath]);
                //make sure we don't end up with too much unneeded depth
                unset($parent->parent, $parent->siblings, $parent->children);
                $parent->path = $parentPath;
                $flatStruct[$url]->parent = $parent;
            }

            $children = array();
            $siblings = array();

            //loop through the page struct and find sub-paths to calculate siblings and children
            foreach($flatStruct as $structPath => $structInfo){
          
    
                $tempDepth = substr_count($structPath, '/');
        
                $potentialParentPath = substr($structPath, 0, strlen($parentPath));


                // var_dump($potentialParentPath);
                // var_dump($tempDepth);

                if ($potentialParentPath === $parentPath 
                    && $tempDepth === $myDepth 
                    //&& 
                        ){
                    $sib = clone($structInfo);
                    unset($sib->parent, $sib->siblings, $sib->children);
                    if ($structPath === $url){
                        $sib->current = true;
                    }
                    $siblings[] = $sib;
                }


                //figure out if the parent path matches the potential parent path and has the correct depth

                // echo 'potential: '.$potentialParentPath."\n";
                // echo "actual   : ".$parentPath."\n";
                // echo 'depth    : '.$myDepth."\n";
                // echo 'pdepth   : '.$parentDepth."\n";


                /*
                TODO: Need to figure out children
                 */

            }


            // if (!empty($children)){
            //     $flatStruct[$url]->children = $children;
            // }
            if (!empty($siblings)){
                $flatStruct[$url]->siblings = $siblings;
            }


        }
        return $flatStruct;
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