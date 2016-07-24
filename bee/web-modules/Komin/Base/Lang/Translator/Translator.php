<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Lang\Translator;

use Bee\Component\Lang\Translator\CatalogLoader\CatalogLoaderInterface;
use Bee\Component\Lang\Translator\Translator as BTranslator;


/**
 * Translator
 * @author Lingtalfi
 * 2014-10-22
 *
 *
 * on translation not found:
 * - try english version first
 * - Log to files if not found
 */
class Translator extends BTranslator
{


    protected $fallbackLang;

    public function __construct(CatalogLoaderInterface $catalogLoader, $options = [])
    {
        $options = array_replace([
            'fallbackLang' => 'eng',
        ], $options);
        parent::__construct($catalogLoader, $options);
    }


    protected function onTranslationNotFound(array $info)
    {
        /**
         * I use this personally because I like to use small identifiers as msgId for long texts,
         * rather than setting the long text as a key.
         * So the msgId is something like [myLongTextRef],
         * which, when not translated, is quite insignificant for the end user.
         * I usually do at least the english version (or french?), so this trick allow me to temporarily?
         * display the english version until the french? version is translated.
         *
         */
        $r = null;
        if ($info['lang'] !== $this->options['fallbackLang']) {
            $this->options['allowRecovery'] = false;
            $r = $this->translate($info['msgId'], $info['catalogInfo'], $info['tags'], $info['pluralNumber'], $this->options['fallbackLang']);
            $this->options['allowRecovery'] = true;
        }
        return $r;
    }


}
