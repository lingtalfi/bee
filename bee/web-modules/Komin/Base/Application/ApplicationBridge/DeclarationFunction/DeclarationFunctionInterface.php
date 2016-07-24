<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\ApplicationBridge\DeclarationFunction;


/**
 * DeclarationFunctionInterface
 * @author Lingtalfi
 * 2015-02-16
 *
 */
interface DeclarationFunctionInterface
{
    /**
     * @return mixed|false in case of a problem, superlog is used
     */
    public function execute(array $params);
}
