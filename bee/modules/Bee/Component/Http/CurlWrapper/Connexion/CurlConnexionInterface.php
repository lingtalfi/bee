<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Http\CurlWrapper\Connexion;


/**
 * CurlConnexionInterface
 * @author Lingtalfi
 * 2015-06-10
 *
 *
 *
 * CurlWrapperException is thrown when something wrong occurs.
 *
 *
 */
interface CurlConnexionInterface
{

    /**
     * @return CurlConnexionInterface
     */
    public function open();

    /**
     * @return CurlConnexionInterface
     */
    public function close();

    public function getCurlHandle();

    /**
     * @return CurlConnexionInterface
     */
    public function setOption($name, $value);

    public function getOptions($showKeyAsLiteral = false);
}
