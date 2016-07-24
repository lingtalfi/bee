<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\Html\MarkDown\MarkDown;

use Bee\Component\Text\TextConvertor\TextConvertor;
use Bee\Notation\Html\MarkDown\MarkDown\Filter\FlattenBlockElementsFilter;
use Bee\Notation\Html\MarkDown\MarkDown\Filter\ParagraphFilter;


/**
 * MarkDownTextConvertor
 * @author Lingtalfi
 * 2014-08-29
 *
 */
class MarkDownTextConvertor extends TextConvertor
{

    /**
     * @var MarkDownTextConvertorUtil
     */
    protected $util;

    public function __construct()
    {
        parent::__construct();
        $this->util = new MarkDownTextConvertorUtil();
        $this->setFilter(new FlattenBlockElementsFilter($this));
//        $this->setFilter(new ParagraphFilter($this));
    }

    /**
     * @return MarkDownTextConvertorUtil
     */
    public function getUtil()
    {
        return $this->util;
    }



}
