<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\Adr;

use Bee\Component\Log\SuperLogger\SuperLogger;


/**
 * Adr
 * @author Lingtalfi
 * 2015-01-18
 *
 */
class LingAdr extends Adr
{
    public function __construct(array $libs = [])
    {
        $libs = array_replace([
            'ajaxloader' => [
                'assets' => [
                    'http://approot0/web/libs/css/ajaxloader/1.0/ajaxloader.css',
                ],
            ],            
            'ajaxtim' => [
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/ajaxtim/ajaxtim-1.0.js',
                ],
            ],
            'array2ul' => [
                'libs' => [
                    'jquery',
                    'pea',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/btool/array2ul/array2ul.js',
                ],
            ],
            'assetloader' => [
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/assetloader/1.01/assetloader.js',
                ],
            ],
            'bdot' => [
                'assets' => [
                    'http://approot0/web/libs/js/bdot/1.01/bdot.js',
                ],
            ],
            'beast' => [
                'libs' => [
                    'jquery',
                    'beelJsTable',
                    'debugTool',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/beast/1.0/js/beastEngine.js',
                    'http://approot0/web/libs/js/jquery/addons/beast/1.0/js/testDisplayer.js',
                ],
            ],
            'beauty' => [
                'libs' => [
                    'jquery',
                    'pea',
                    'jqueryui',
                    'jutil',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/beauty/1.0/js/beauty.js',
                ],
            ],
            'beef' => [
                'libs' => [
                    'jquery',
                    'jqueryui', // simple array
                    'jutil', // simple array
                    'array2ul', 
                    'bdot',
                    'ajaxtim',
                    'uii',
                    'assetloader',
                    'pea',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/beef/js/beef-0.09.js',
                    'http://approot0/web/libs/js/jquery/addons/beef/css/beef.css',
                    'http://approot0/web/libs/js/jquery/addons/beef/js/validation/1.0/lang/beefTranslations-eng.js',
                    'http://approot0/web/libs/js/jquery/addons/beef/js/validation/1.0/beef-validation-1.0.js',
                    'http://approot0/web/libs/js/jquery/addons/beef/js/controls/simple-array-1.03.js',
                ],
            ],
            'beelJsTable' => [
                'libs' => [
                    'jquery',
                    'pea',
                    'htmlTool',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/beelJs/1.0/beelJsTable.js',
                ],
            ],
            'bjs' => [
                'libs' => [],
                'assets' => [
                    'http://approot0/web/libs/js/btool/bjs/bjs-1.0.js',
                ],
            ],
            'crud' => [
                'libs' => [
                    'jquery',
                    'beef',
                    'pea',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/crud/js/1.0/crud-autoadmin-1.0.js',
                    'http://approot0/web/libs/js/jquery/addons/crud/js/1.0/beefcrudwizard.js',
                    'http://approot0/web/libs/js/jquery/addons/crud/js/1.0/beelcrudwizard.js',
                    'http://approot0/web/libs/js/jquery/addons/crud/js/1.0/beelcrudwizardwidgets.js',
                    'http://approot0/web/libs/js/jquery/addons/crud/css/bcrud.css',                    
                ],
            ],
            'debugTool' => [
                'libs' => [
                    'pea',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/btool/debugTool/1.0/debugTool.js',
                ],
            ],
            'htmlTool' => [
                'libs' => [
                    'pea',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/btool/htmlTool/1.0/htmlTool.js',
                ],
            ],
            'jquery' => [
                'assets' => [
//                    'http://approot0/web/libs/js/jquery/lib/1.10.2/jquery.min.js',
//                    'http://approot0/web/libs/js/jquery/lib/2.1.3/jquery-2.1.3.min.js',
                    'http://approot0/web/libs/js/jquery/lib/1.11.2/jquery-1.11.2.js',
                ],
            ],
            'jqueryui' => [
                'assets' => [
                    'http://approot0/web/libs/js/jquery/plugins/ui/1.11.2/jquery-ui.min.js',
                ],
            ],
            'jutil' => [
                'libs' => [
                    'jquery',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/jquery/addons/jutil/1.0/jutil.js',
                ],
            ],
            'pea' => [
                'assets' => [
                    'http://approot0/web/libs/js/pea/lib/1.01/pea-1.01.js',
                ],
            ],
            'uii' => [
                'libs' => [
                    'jquery',
                ],
                'assets' => [
                    'http://approot0/web/libs/js/jquery/plugins/uii/dialogg/1.0/dialogg.js',
                    'http://approot0/web/libs/js/jquery/plugins/uii/dragg/1.03/dragg.min.js',
                    'http://approot0/web/libs/js/jquery/plugins/uii/resizz/1.0/resizz.min.js',
                    'http://approot0/web/libs/js/jquery/plugins/uii/positionn/1.0/positionn.min.js',
                    'http://approot0/web/libs/js/jquery/plugins/uii/dialogg/1.0/dialogg.css',
                    'http://approot0/web/libs/js/jquery/plugins/uii/resizz/1.0/resizz.css',
                ],
            ],
        ], $libs);
        parent::__construct($libs);
    }


}
