<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Lang\ApplicationLang;


/**
 * ApplicationLangServerInterface
 * @author Lingtalfi
 *
 *
 */
interface ApplicationLangServerInterface
{

    public function getLang();

    public function setLang($iso610Lang);


}
