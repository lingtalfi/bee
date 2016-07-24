<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\Controller;

use WebModule\Bware\SymphoBee\HttpResponse\HttpResponse;


/**
 * HelloController
 * @author Lingtalfi
 * 2015-02-15
 *
 */
class HelloController implements ControllerInterface
{


    public function hi()
    {
        return new HttpResponse('<h1>Good job bee, it worked!</h1>');
    }

}
