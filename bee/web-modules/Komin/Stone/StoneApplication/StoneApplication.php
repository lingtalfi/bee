<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Stone\StoneApplication;

use Bee\Application\Asset\AssetDependencyResolver\Adaptor\Suzanne2HtmlAdaptor;
use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use Bee\Application\HtmlPage\HtmlHead;
use Bee\Bat\DebugTool;
use Bee\Bat\UrlTool;
use Bee\Bat\VarTool;
use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Application\Adr\Stazy\StazyAdr;
use WebModule\Komin\Base\Http\Session\Stazy\StazySession;
use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;
use WebModule\Komin\User\Tool\UserConnectionTool;
use WebModule\Komin\User\UserConnectionSystem\Stazy\StazyUserConnectionSystem;


/**
 * StoneApplication
 * @author Lingtalfi
 * 2014-11-02
 *
 */
class StoneApplication implements StoneApplicationInterface
{

    protected $config;
    protected $values;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->values = [];
    }





    //------------------------------------------------------------------------------/
    // IMPLEMENTS StoneApplicationInterface
    //------------------------------------------------------------------------------/
    public function start()
    {
        if (true === $this->getParam('powerOn', true)) {

            //------------------------------------------------------------------------------/
            // MULTI LANG?
            //------------------------------------------------------------------------------/
            if (true === $this->getParam('useMultiLang', false)) {
                $this->multiLangFeature();
            }


            //------------------------------------------------------------------------------/
            // ROUTING
            //------------------------------------------------------------------------------/
            $uri = $_SERVER['REQUEST_URI'];


            $adminUrlPrefix = $this->getParam("adminUrlPrefix", '/admin');
            $servicesUrlPrefix = $this->getParam("servicesUrlPrefix", '/services');
            $adminLen = strlen($adminUrlPrefix);
            $servicesLen = strlen($servicesUrlPrefix);
            list($url, $queryParams) = UrlTool::getPathAndQueryArgs();
            $this->values['url'] = $url;

            //------------------------------------------------------------------------------/
            // SERVICES
            //------------------------------------------------------------------------------/
            if ($servicesUrlPrefix === substr($uri, 0, $servicesLen)) {
                $url = substr($url, $servicesLen);

                $serviceDir = '../app/services';

                $pageFile = $serviceDir . '/' . $url . '.php';
                if (file_exists($pageFile)) {
                    require_once($pageFile);
                }
                else {
                    echo "Service not found";
                    $this->e404('service', sprintf("Service not found: %s", $pageFile));
                }
            }
            //------------------------------------------------------------------------------/
            // ADMIN PAGES
            //------------------------------------------------------------------------------/
            /**
             * Single admin user management
             */
            elseif ($adminUrlPrefix === substr($uri, 0, $adminLen)) {

                $url = substr($url, $adminLen);
                $components = explode('/', $url);
                $lastComponent = end($components);
                if (empty($lastComponent)) {
                    $lastComponent = $this->getParam('defaultAdminPage', 'home');
                }


                //------------------------------------------------------------------------------/
                // DISCONNECT?
                //------------------------------------------------------------------------------/
                /**
                 * This module simply disconnects the user depending on the value of the last uri component.
                 * The user is then redirected to the url specified by the [redirectToKey] key.
                 */
                if (true === $this->getParam('useAdminDisconnect', true)) {
                    $this->disconnectFeature($lastComponent, $queryParams);
                }


                /**
                 * Handling of the admin connexion form
                 */
                $loginKey = $this->getParam('loginKey', 'login');
                $passKey = $this->getParam('passKey', 'pass');
                $this->connectIfRequested($loginKey, $passKey);


                $connectIf = $this->getParam('connectIf', null);
                if (
                    (null === $connectIf && true === UserConnectionTool::isConnected()) ||
                    (is_string($connectIf) && true === UserConnectionTool::isGranted($connectIf))
                ) {
                    /**
                     * Below is the routing system of this application.
                     */
                    $adminPagesDir = '../app/backpages';
                    $pageFile = $adminPagesDir . '/' . $lastComponent . '.php';
                    if (file_exists($pageFile)) {
                        $this->displayPage($pageFile);
                    }
                    else {
                        $notFound = $adminPagesDir . '/' . $this->getParam('adminNotFoundPage', 'notfound') . '.php';
                        if (file_exists($notFound)) {
                            require_once $notFound;
                        }
                        else {
                            echo "The page you asked for does not exist";
                            $this->e404("backPage", sprintf("page not found: %s", $notFound));
                        }
                    }

                }
                else {


                    //------------------------------------------------------------------------------/
                    // ADMIN CONNEXION FORM
                    //------------------------------------------------------------------------------/
                    // those variables should be used by the admin connexion form script
                    $login = (isset($_POST[$loginKey])) ? $_POST[$loginKey] : '';
                    $pass = (isset($_POST[$passKey])) ? $_POST[$passKey] : '';
                    $rememberMe = '1';
                    $this->feedFormVariables($login, $pass, $rememberMe);

                    // this must exist
                    require_once StazyPsnResolver::getInst()->getPath($this->getParam("adminConnexionForm", "[app]/backpages/login.php"));

                }

            }
            //------------------------------------------------------------------------------/
            // FRONT PAGES
            //------------------------------------------------------------------------------/
            else {
                /**
                 * Below is the routing system for the front pages
                 */
                if ('/' === $url) {
                    $url = '/' . $this->getParam("defaultFrontPage", 'home');
                }
                $url = rtrim($url, '/');


                //------------------------------------------------------------------------------/
                // SEO
                //------------------------------------------------------------------------------/
                $this->addSeo($url);


                $frontPagesDir = '../app/frontpages';
                $pageFile = $frontPagesDir . $url . '.php';
                if (file_exists($pageFile)) {
                    $this->displayPage($pageFile);
                }
                else {
                    $this->e404("frontPage", sprintf("Url leads to 404: %s", $url));

                    // do we have a fallback page for presentation?
                    $notFound = $frontPagesDir . '/' . $this->getParam('frontNotFoundPage', 'notfound') . '.php';
                    if (file_exists($notFound)) {
                        require_once $notFound;
                    }
                    else {
                        echo sprintf("The page you asked for does not exist %s", $url);
                    }
                }
            }
        }
        else {
            // power off page, must exist
            require_once(StazyPsnResolver::getInst()->getPath($this->getParam("powerOffPage", "[app]/frontpages/powerOff.php")));
        }
    }

    /**
     * allowed keys depends on the implementation,
     * we recommend that a stone application allows at least for the following keys:
     *
     * - url
     *
     */
    public function getValue($key, $default = null)
    {
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }
        return $default;
    }


    public function getParam($key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
        return $default;
    }


    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    protected function displayPage($pageFile)
    {
        // variable to access stone app from within scripts
        $stoneApp = $this;


        ob_start();
        require_once($pageFile);
        $body = ob_get_clean();
        echo '<!DOCTYPE html>' . PHP_EOL;
        echo '<html lang="' . $this->getParam("htmlHeadLang", 'en-Us') . '">' . PHP_EOL;
        $oSuz = new Suzanne2HtmlAdaptor(StazyAdr::getInst()->resolve(AssetCalls::getInst()));
        HtmlHead::getInst()->addContent($oSuz->getHead(), 2);
        echo HtmlHead::getInst()->render() . PHP_EOL;
        echo $body;
    }


    protected function multiLangFeature()
    {
        //------------------------------------------------------------------------------/
        // HANDLING LANGUAGE AND SPREAD TO OTHER SERVICES...
        //------------------------------------------------------------------------------/
        $defaultLang = $this->getParam('defaultLang', 'eng');
        $lang = $defaultLang;
        if (isset($_GET['lang'])) {
            $lang = $_GET['lang'];
        }
        else {
            if (null !== $sLang = StazySession::getInst()->get('lang', null)) {
                $lang = $sLang;
            }
        }
        $allowedLangs = $this->getParam('allowedLangs', ['eng']);
        if (!in_array($lang, $allowedLangs)) {
            $lang = $defaultLang;
        }
        StazySession::getInst()->set('lang', $lang);
        StazyTranslator::getInst()->setDefaultLang($lang);
        $this->values['lang'] = $lang;
    }

    protected function disconnectFeature($lastComponent, $queryParams)
    {
        if ($this->getParam('disconnectWord', 'disconnect') === $lastComponent) {
            StazyUserConnectionSystem::getInst()->removeUserImage();
            $rto = $this->getParam('redirectToKey', 'url');
            $url = (array_key_exists($rto, $queryParams)) ? str_replace('"', '\"', $queryParams[$rto]) : '/';
            ?>
            <html>
        <head>
            <meta http-equiv="Refresh" content="0; url=<?php echo $url; ?>"/>
        </head>
        <body>
        <p>You will now be redirected, please wait.</p>
        </body>
            </html><?php
        }
    }

    protected function addSeo($url)
    {
        /**
         * Adding the basic seo from conf
         */
        $seo = $this->getParam("seo");
        $title = $this->getParam('metaTitle', 'Stone app');
        $desc = $this->getParam("metaDescription", "This is a stone app");

        $page = ltrim($url, '/');
        if (array_key_exists($page, $seo)) {
            $title = $seo[$page]['title'];
            $desc = $seo[$page]['description'];
        }
        HtmlHead::getInst()
            ->setTitle($title)
            ->setDescription($desc);
    }

    protected function connectIfRequested($loginKey, $passKey)
    {
        if (isset($_POST[$loginKey]) && isset($_POST[$passKey])) {
            $creds = [
                $loginKey => $_POST[$loginKey],
                $passKey => $_POST[$passKey],
            ];
            StazyUserConnectionSystem::getInst()->createUserImage($creds, $this->getParam('testedLists', null));

            //------------------------------------------------------------------------------/
            // CREATING A "REMEMBER ME" COOKIE IF REQUESTED
            //------------------------------------------------------------------------------/
            if (isset($_POST[$this->getParam('rememberMeKey', 'remember_me')])) {
                $remember = $this->getParam('remember', ['login', 'rememberMe']);
                if (is_array($remember)) {
                    $fields = [];
                    if (array_key_exists('login', $remember)) {
                        $fields[] = $remember['login'];
                    }
                    else {
                        $fields[] = '0';
                    }
                    if (array_key_exists('pass', $remember)) {
                        $fields[] = $remember['pass'];
                    }
                    else {
                        $fields[] = '0';
                    }
                    if (array_key_exists('rememberMe', $remember)) {
                        $fields[] = "1";
                    }
                    else {
                        $fields[] = '0';
                    }
                    setcookie($this->getParam('rememberMeCookieName', 'stone_connexion'), implode('===', $fields));
                }
                else {
                    $this->log("invalidRememberProperty", sprintf("remember key should have been an array, %s given", VarTool::toString($remember)));
                }
            }
        }
    }


    protected function feedFormVariables(&$name, &$password, &$rememberMe)
    {
        $key = $this->getParam('rememberMeCookieName', 'stone_connexion');
        if (isset($_COOKIE[$key])) {
            $n = $_COOKIE[$key];
            $p = explode('===', $n, 3);
            if (3 === count($p)) {
                if (empty($name)) {
                    if ($p[0]) {
                        $name = $p[0];
                    }
                    if ($p[1]) {
                        $password = $p[1];
                    }
                    $rememberMe = $p[2];
                }
                else {
                    // the form is posted, it will reuse the posted values
                }
            }
        }
    }


    protected function e404($id, $msg)
    {
        SuperLogger::getInst()->log('komin.stone.app.404.' . $id, $msg);
    }


    protected function log($id, $msg)
    {
        SuperLogger::getInst()->log('komin.stone.app.' . $id, $msg);
    }
}
