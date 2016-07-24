<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\ApplicationBridge;

use WebModule\Komin\Base\Application\ApplicationBridge\DeclarationFunction\DeclarationFunctionInterface;
use Bee\Component\Log\SuperLogger\Traits\SuperLoggerTrait;


/**
 * ApplicationBridge
 * @author Lingtalfi
 * 2015-02-16
 *
 */
class ApplicationBridge implements ApplicationBridgeInterface
{
    use SuperLoggerTrait;


    protected $declarations;


    /**
     * @param array $declarations , array of identifier => DeclarationFunctionInterface
     */
    public function __construct(array $declarations)
    {
        $this->declarations = $declarations;
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS ApplicationBridgeInterface
    //------------------------------------------------------------------------------/
    public function execute($declarationIdentifier, array $params = [])
    {
        if (array_key_exists($declarationIdentifier, $this->declarations)) {
            $func = $this->declarations[$declarationIdentifier];
            if ($func instanceof DeclarationFunctionInterface) {
                return $func->execute($params);
            }
            else {
                $this->slog("invalidDeclarationFunction", sprintf("Invalid function declaration: should be of type DeclarationFunctionInterface (identifier: %s)", $declarationIdentifier));
            }
        }
        else {
            $this->slog("unknownDeclarationIdentifier", sprintf("Unknown declaration identifier: %s", $declarationIdentifier));
        }
        return false;
    }
}
