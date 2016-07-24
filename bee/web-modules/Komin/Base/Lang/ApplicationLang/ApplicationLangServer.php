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
 * ApplicationLangServer
 * @author Lingtalfi
 *
 *
 */
class ApplicationLangServer
{

    protected $lang;


    public function __construct($defaultLang = 'eng')
    {
        $this->lang = $defaultLang;
    }


    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($iso610Lang)
    {
        $this->lang = $iso610Lang;
    }


}
