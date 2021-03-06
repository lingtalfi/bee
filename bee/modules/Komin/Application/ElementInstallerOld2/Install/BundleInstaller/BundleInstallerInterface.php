<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komin\Application\ElementInstaller\Install\BundleInstaller;

use Komin\Application\ElementInstaller\Install\InstallVars\InstallVarsInterface;


/**
 * BundleInstallerInterface
 * @author Lingtalfi
 * 2015-05-22
 *
 *
 */
interface BundleInstallerInterface 
{

    public function install($bundlePath, InstallVarsInterface $installVars);
}
