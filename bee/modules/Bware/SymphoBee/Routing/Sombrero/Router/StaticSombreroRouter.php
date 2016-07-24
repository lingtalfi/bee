<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Sombrero\Router;

use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainerInterface;
use Bee\Bat\DateTool;
use Bee\Bat\FileSystemTool;
use Bee\Bat\SanitizerTool;
use Bee\Component\Cache\CacheMaster\CacheDisciple\ByFileMtimeCacheDisciple;
use Bee\Component\Cache\CacheMaster\FileSystemCacheMaster;
use Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use Bware\SymphoBee\Routing\Route\RouteInterface;
use Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter\BaseSombreroDumper;
use Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter\SombreroRouteToCodeAdaptor;
use Bware\SymphoBee\Routing\UriMatcher\AppleUriMatcher;
use Bware\SymphoBee\Routing\UriMatcher\UriMatcherInterface;


/**
 * StaticSombreroRouter
 * @author Lingtalfi
 * 2015-06-03
 *
 * This router uses the sombrero notation for writing routes.
 * It converts the sombrero notation to hard code and stores all code in a dumper class.
 * Every method of the dumper class corresponds to a sombrero route; when executed, it returns either false or the matching route.
 *
 * The code stored in the dumper is roughly equivalent to the code a dev would manually write, hence
 * making the StaticSombrero a good choice if performance is an issue.
 *
 * The "bad" side is that this class has to handle a cache system.
 *
 *
 *
 */
class StaticSombreroRouter extends SombreroRouter
{

    private $array;
    /**
     * @var UriMatcherInterface
     */
    private $uriMatcher;
    private $controllerDirs;
    private $serviceContainer;

    /**
     * @var ByFileMtimeCacheDisciple
     */
    private $cacheDisciple;
    private $cacheRootDir;
    /**
     * The dir where the generated class will be put into
     */
    private $dumpRootDir;
    /**
     * The name of the generated class
     */
    private $dumpClassName;
    /**
     *
     * Trigger files is an array that contain files which modification will trigger the cached dump class refreshment.
     * This is probably going to be the babyYaml files from which the sombrero routes are taken from.
     * Or it could be a unique custom file which a maintainer could use as a personal trigger.
     */
    private $dumpTriggerFiles;

    /**
     * @var SombreroRouteToCodeAdaptor
     */
    private $adaptor;


    public function __construct()
    {
        $this->array = [];
        $this->uriMatcher = new AppleUriMatcher();
        $this->controllerDirs = [];
        $this->dumpTriggerFiles = [];
        $this->dumpRootDir = sys_get_temp_dir();
        $this->dumpClassName = "SombreroDefaultRoutesDump";
    }

    public static function create()
    {
        return new static();
    }
    //------------------------------------------------------------------------------/
    // IMPLEMENTS RouterInterface
    //------------------------------------------------------------------------------/
    /**
     * Returns a matching route, or false
     *
     * @param HttpRequestInterface $r
     * @return RouteInterface|false
     */
    public function match(HttpRequestInterface $r)
    {

        $file = $this->dumpRootDir . "/" . $this->dumpClassName . '.php';
        $dataName = 'bware.symphoBee.routing.staticSombreroRouter';
        if (false !== $data = $this->getCacheDisciple()->getData($dataName)) {
            require_once $file;
            $dumper = new $this->dumpClassName($this->serviceContainer);
        }
        else {
            $dumper = $this->createDumper($file, $this->dumpClassName);
            $this->getCacheDisciple()->store($dataName, 'anyData', [
                'files' => $this->dumpTriggerFiles,
            ]);

        }
        
        $methods = get_class_methods($dumper);
        foreach ($methods as $method) {
            if ('__construct' !== $method && false !== $matchingRoute = call_user_func([$dumper, $method], $r)) {
                return $matchingRoute;
            }
        }
        return false;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/


    public function setSombreroArray(array $sombreroArray)
    {
        $this->array = $sombreroArray;
        return $this;
    }

    public function addControllerDir($dir)
    {
        $this->controllerDirs[] = $dir;
        return $this;
    }

    public function setControllerDirs(array $controllerDirs)
    {
        $this->controllerDirs = $controllerDirs;
        return $this;
    }

    public function setServiceContainer(ServiceContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
        return $this;
    }

    public function setDumpClassName($dumpClassName)
    {
        $this->dumpClassName = $dumpClassName;
        return $this;
    }

    public function setDumpRootDir($dumpRootDir)
    {
        $this->dumpRootDir = $dumpRootDir;
        return $this;
    }

    public function setDumpTriggerFiles(array $dumpTriggerFiles)
    {
        $this->dumpTriggerFiles = $dumpTriggerFiles;
        return $this;
    }

    public function setCacheRootDir($cacheRootDir)
    {
        $this->cacheRootDir = $cacheRootDir;
        return $this;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @return ByFileMtimeCacheDisciple
     */
    private function getCacheDisciple()
    {
        if (null === $this->cacheDisciple) {
            $rootDir = $this->cacheRootDir;
            if (null === $rootDir) {
                $rootDir = FileSystemTool::tempDir();
            }
            $this->cacheDisciple = ByFileMtimeCacheDisciple::create()
                ->setCacheMaster(FileSystemCacheMaster::create()->setRootDir($rootDir));
        }
        return $this->cacheDisciple;
    }


    /**
     * @return BaseSombreroDumper
     */
    private function createDumper($file, $className)
    {
        if (null === $this->adaptor) {
            $this->adaptor = SombreroRouteToCodeAdaptor::create()->setControllerDirs($this->controllerDirs);
        }

        $s = '';
        foreach ($this->array as $routeName => $info) {
            $method = "testRoute" . ucfirst(strtolower(SanitizerTool::sanitizeVariableName($routeName)));
            $code = $this->adaptor->getRouteCode($info);
            $w = <<<PPP
            
        public function $method (HttpRequestInterface \$r){
            $code
        }
        
PPP;
            $s .= $w;
        }
        $content = file_get_contents(__DIR__ . '/StaticSombreroRouter/template/SombreroDumperTemplate.php.tpl');
        $date = DateTool::getY4mdDate();
        FileSystemTool::filePutContents($file, str_replace([
            '{className}',
            '{date}',
            '{methods}',
        ], [
            $className,
            $date,
            $s,
        ], $content));
        require_once $file;
        $dumper = new $className($this->serviceContainer);
        return $dumper;
    }
}
