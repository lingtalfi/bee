<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beef\Server\Ascp\ControlFactory;


/**
 * ControlFactoryInterface
 * @author Lingtalfi
 * 2015-01-30
 *
 */
interface ControlFactoryInterface
{

    /**
     * @return false|array:
     *                  - 0: string, html, the html code of the control
     *                  - 1: string|null, js, the js code to init the control if necessary.
     *                                  The code for the validation is not handled by the factory.
     *                  - 2: array|null, dependencies, the assets related to the html code
     */
    public function getNodeInfo($controlName, array $controlNode);
}
