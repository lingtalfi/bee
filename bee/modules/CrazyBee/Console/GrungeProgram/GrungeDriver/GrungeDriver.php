<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\GrungeDriver;

use Bee\Application\ParameterBag\BdotParameterBag;
use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainerInterface;
use Bee\Application\ServiceContainer\Tool\HotServiceContainerTool;
use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;
use Bee\Notation\WrappedString\Tool\CandyResolverTool;
use CrazyBee\Console\GrungeProgram\Environment\GrungeEnvironment;
use CrazyBee\Console\GrungeProgram\NotationResolver\GrungeNotationResolver;
use CrazyBee\Console\GrungeProgram\StepAppearanceDecorator\StepAppearanceDecoratorInterface;
use CrazyBee\Console\GrungeProgram\StepProcessor\GrungeStepProcessor;
use CrazyBee\Console\StepDrivenProgram\Environment\Environment;


/**
 * GrungeDriver
 * @author Lingtalfi
 * 2015-05-20
 *
 */
class GrungeDriver
{


    /**
     * @var StepExpander
     */
    private $expander;

    /**
     * @var StepConvertor
     */
    private $convertor;

    /**
     * @var GrungeNotationResolver
     */
    private $resolver;

    /**
     * @var ServiceContainerInterface
     */
    private $serviceContainer;

    /**
     * @var StepAppearanceDecoratorInterface
     */
    private $decorator;

    private $programCore;
    private $_init;

    public function __construct()
    {
        $this->_init = false;
    }

//    public function setResolver(GrungeNotationResolver $resolver)
//    {
//        $this->resolver = $resolver;
//        return $this;
//    }

    public static function create()
    {
        return new static();
    }

    /**
     * @return GrungeStepProcessor
     */
    public function createStepProcessorByFile($f)
    {
        if (!file_exists($f)) {
            throw new \RuntimeException("File not found: $f");
        }


        /**
         * For now, let's create one environment per program
         */
        $env = GrungeEnvironment::create();

        
        
        $processor = GrungeStepProcessor::create()
            ->setEnvironment($env);
        

        $this->init();
        $conf = BabyYamlTool::parseFile($f);


        if (array_key_exists('steps', $conf)) {
            $steps = $conf['steps'];

            $bag = [];
            
            // injecting parameters in resolver
            if (array_key_exists('parameters', $conf)) {
                $parameters = $conf['parameters'];
                CandyResolverTool::selfResolve($parameters);
                $bag = BdotParameterBag::create()->setAll($parameters);
                $this->resolver->injectParameters($bag);
                $env->setParameterBag($bag);
            }


            // initializing session vars
            $this->resolver->injectEnvironment($env);


            // injecting hot container
            if (array_key_exists('services', $conf)) {
                $services = $conf['services'];
                $this->resolver->injectHotContainer(HotServiceContainerTool::createHotServiceContainer($services, $bag));
            }


            // injecting main container
            if (null !== $this->serviceContainer) {
                $this->resolver->injectMainContainer($this->serviceContainer);
            }


            // creating steps
            $steps = $this->resolveSteps($steps);

            if ($this->programCore) {
                $this->resolver->injectProgramCore($this->programCore);
            }


            // injecting things into StepProcessor
            $processor->setResolver($this->resolver);
            foreach ($steps as $name => $step) {
                $processor->registerStep($name, $step);
            }
        }
        else {
            $this->configError("steps array not found");
        }
        return $processor;
    }

    public function setServiceContainer(ServiceContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
        return $this;
    }

    public function setProgramCore($programCore)
    {
        if (!is_object($programCore)) {
            throw new \InvalidArgumentException(sprintf("programCore argument must be of type object, %s given", gettype($programCore)));
        }
        $this->programCore = $programCore;
        return $this;
    }


    public function setDecorator(StepAppearanceDecoratorInterface $decorator)
    {
        $this->decorator = $decorator;
        return $this;
    }




    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function init()
    {
        if (false === $this->_init) {
            if (null === $this->resolver) {
                $this->resolver = new GrungeNotationResolver();
            }
            $this->expander = new StepExpander();
            $this->convertor = StepConvertor::create()->setResolver($this->resolver);
            $this->_init = true;
        }
    }

    private function configError($msg)
    {
        $msg = "ConfigError: " . $msg;
        throw new \RuntimeException($msg);
    }

    private function resolveSteps(array $steps)
    {
        $ret = [];
        if (null !== $this->decorator) {
            $this->convertor->setDecorator($this->decorator);
        }
        foreach ($steps as $name => $step) {
            $step = $this->expander->expand($step);
            $ret[$name] = $this->convertor->convert($step);
        }
        return $ret;
    }
}
