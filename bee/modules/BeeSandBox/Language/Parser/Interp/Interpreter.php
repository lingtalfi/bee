<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Interp;



/**
 * Interpreter
 * @author Lingtalfi
 * 2015-06-30
 * 
 */
class Interpreter {

    /**
     * @var InterpreterListenerInterface
     */
    public $listener;
    public $globals;
    public $tables;

    public function __construct()
    {
        $this->listener = new InterpreterListener(); 
        $this->globals = [];
        $this->tables = [];
    }

    public function interp(){
        
    }

}
