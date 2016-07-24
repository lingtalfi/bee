<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beast\Beauty\TestFinder;

use Bee\Bat\FileTool;
use Bee\Bat\StringTool;


/**
 * DirMapTestFinder
 * @author Lingtalfi
 * 2015-01-25
 *
 */
class DirMapTestFinder extends TestFinder
{

    private $dir;
    protected $options;
    private $extensions2Length;

    public function __construct($dir, array $options = [])
    {
        $this->dir = $dir;
        $this->options = array_replace([
            'extensions' => ['php', 'bst'],
            'fileToUrl' => function ($file, $relPath) {
                return $file;
            },
        ], $options);
        
        
        
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS TestFinderInterface
    //------------------------------------------------------------------------------/
    /**
     * @return array:
     *
     * - $groupName:
     * ----- $url
     */
    public function getTests()
    {
        $ret = [];
        $this->collectTests($this->dir, $ret);
        return $ret;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function collectTests($dir, array &$container, $relPath = null)
    {
        $files = scandir($dir);
        $nextRelPath = '';
        foreach ($files as $name) {
            $file = $dir . '/' . $name;
            if ('.' !== $name && '..' !== $name) {
                if (null === $relPath) {
                    $nextRelPath = $name;
                }
                else {
                    $nextRelPath = $relPath . '/' . $name;
                }

                if (is_dir($file)) {
                    $cont = [];
                    $this->collectTests($file, $cont, $nextRelPath);
                    $container[$name] = $cont;
                }
                else {
                    
                    $extMatch = false;
                    foreach($this->options['extensions'] as $ext){
                        if(true === StringTool::endsWith($file, $ext)){
                            $extMatch = true;
                            break;
                        }
                    }
                    
                    if (true === $extMatch) {
                        $container[] = $this->options['fileToUrl']($file, $nextRelPath);
                    }
                }
            }
        }
    }

}
