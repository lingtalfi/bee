<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\NotationResolver;

use Bee\Application\ParameterBag\ParameterBagInterface;
use Bee\Application\ParameterContainer\ParameterContainerInterface;
use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainer;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\CandyExpressionDiscoverer;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\VariableExpressionDiscoverer;
use CrazyBee\Console\StepDrivenProgram\Environment\Environment;
use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;
use CrazyBee\Notation\NotationResolver\NotationFinder\AeroBeeCallableNotationFinder;
use CrazyBee\Notation\NotationResolver\NotationFinder\BDotArrayNotationFinder;
use CrazyBee\Notation\NotationResolver\NotationResolver;


/**
 * GrungeConfigNotationResolver
 * @author Lingtalfi
 * 2015-05-19
 *
 * Uses:
 *
 * - §candy§ parameters
 * - $sessionVariables
 * - the @tag notation (see more in docs) with:
 *              - p: the program core
 *                      using the following notation:
 * @p->method(args)
 *
 *              - php: a native php function
 *              - service: application service container
 *              - hot: dynamically created service container
 *
 *              arguments:
 * - @step represents a step
 * - @env represents the environment
 *
 *
 */
class GrungeNotationResolver extends NotationResolver
{


    private $aeroBeeNotationFinder;
    private $aeroInit;

    public function __construct()
    {
        parent::__construct();
        $this->aeroBeeNotationFinder = AeroBeeCallableNotationFinder::create()->setStartSymbol('@');
        $this->setRecursiveMap([
            // all recursive
        ]);
        $this->aeroInit = false;
        $this->injectAero();
    }

    public static function create()
    {
        return new static();
    }

    public function injectParameters(ParameterBagInterface $p)
    {
        $this->setFinder(
            ParameterBagNotationFinder::create()
                ->setParameterBag($p)
                ->setDiscoverer(CandyExpressionDiscoverer::create()->setSymbol('§'))
                ->setStartSymbol('§'),
            'candy'
        );
        return $this;
    }

    public function injectEnvironment(EnvironmentInterface $env)
    {
        $this->setFinder(
            SessionVarNotationFinder::create()
                ->setEnvironment($env)
                ->setDiscoverer(VariableExpressionDiscoverer::create()->setSymbol('$')->setVarNamePattern('!^[a-zA-Z0-9_.]+!'))
                ->setStartSymbol('$'),
            'sessionVars'
        );
        return $this;
    }

    public function injectMainContainer(ServiceContainer $c)
    {
        $this->aeroBeeNotationFinder->setContainer('_default', $c);
        $this->injectAero();
        return $this;
    }

    public function injectHotContainer(ServiceContainer $c)
    {
        $this->aeroBeeNotationFinder->setContainer('hot', $c);
        $this->injectAero();
        return $this;
    }

    public function injectCallableSpecialVars(StepInterface $s, EnvironmentInterface $e)
    {
        $this->aeroBeeNotationFinder->setParametersAdaptor(function ($v) use ($s, $e) {
            if ('@step' === $v) {
                $v = $s;
            }
            elseif ('@env' === $v) {
                $v = $e;
            }
            return $v;
        });
        return $this;
    }

    public function injectProgramCore($programCore)
    {
        if (!is_object($programCore)) {
            throw new \InvalidArgumentException(sprintf("programCore argument must be of type object, %s given", gettype($programCore)));
        }
        $this->aeroBeeNotationFinder->setSpecialFunctionProcessor(function ($beforeOperator, $operator, $method, array $params, &$wasSpecial = false) use ($programCore) {
            if ('p' === $beforeOperator && '->' === $operator) {
                $wasSpecial = true;
                return call_user_func_array([$programCore, $method], $params);
            }
        });
        return $this;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function injectAero()
    {
        if (false === $this->aeroInit) {
            $finders = $this->getFinders();
            // aero finder has to be first (expression conflict problem)
            $this->setFinders(array_merge(['action' => $this->aeroBeeNotationFinder], $finders));
            $this->aeroInit = true;
        }
    }

}
