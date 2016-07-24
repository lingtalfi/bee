<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\Html\MarkDown\MarkDown\Filter;

use Bee\Component\Text\TextConvertor\Filter\TextConvertorFilterInterface;
use Bee\Notation\Html\MarkDown\MarkDown\MarkDownTextConvertor;


/**
 * BaseFilter
 * @author Lingtalfi
 * 2014-08-29
 *
 */
abstract class BaseFilter implements TextConvertorFilterInterface
{

    /**
     * @var MarkDownTextConvertor
     */
    protected $convertor;
//
//    public function __construct(MarkDownTextConvertor $convertor)
//    {
//        $this->convertor = $convertor;
//        $this->util = $convertor->getUtil();
//    }


    protected function getBlockNames()
    {
        return [
            'div',
            'p',
        ];
    }
}
