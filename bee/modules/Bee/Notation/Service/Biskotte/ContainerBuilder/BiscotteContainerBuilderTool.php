<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\Service\Biskotte\ContainerBuilder;

use Bee\Application\ServiceContainer\ServiceContainer\HotServiceContainer;
use Bee\Application\ServiceContainer\ServiceContainer\HotServiceContainerInterface;
use Bee\Application\ServiceContainer\ServicePlainCode\ServicePlainCode;
use Bee\Component\Bag\ReadOnlyBdotBag;
use Bee\Notation\Service\Biskotte\Parser\Adaptor\AppRootDirBiscotteParserAdaptor;
use Bee\Notation\Service\Biskotte\Parser\BiskotteParser;
use Bee\Notation\Service\Biskotte\ServiceContainer\BiscHotServiceContainer;
use Bee\Notation\WrappedString\Tool\CandyResolverTool;


/**
 * BiscotteContainerBuilderTool
 * @author Lingtalfi
 * 2015-05-27
 *
 */
class BiscotteContainerBuilderTool
{


    /**
     * @deprReturn BiscHotServiceContainer
     * @return HotServiceContainer
     */
    public static function getHotServiceContainer(array $biscotteServices, array $paramsRef = [], array $options = [])
    {

        $options = array_replace([
            'resolveParams' => true,
            'appRootDir' => null,
        ], $options);

//        $c = BiscHotServiceContainer::create();
        $c = HotServiceContainer::create();
        if (true === $options['resolveParams']) {
            CandyResolverTool::selfResolve($paramsRef);
        }
//        // assuming paramsRef are resolved
//        $c->setParamsOnce(new ReadOnlyBdotBag($paramsRef));

        $p = BiskotteParser::create();
        if (null !== $options['appRootDir']) {
            $p->setAdaptor(AppRootDirBiscotteParserAdaptor::create()->setAppRootDir($options['appRootDir']));
        }

        $p->parseServices($biscotteServices, function ($address, $code) use ($c) {
            $oCode = new ServicePlainCode($code);
            $c->setCode($address, $oCode);
        }, $paramsRef);
        return $c;
    }


}
