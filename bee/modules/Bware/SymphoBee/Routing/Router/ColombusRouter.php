<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\Routing\Router;

use Bee\Bat\NodeTool;
use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use WebModule\Bware\SymphoBee\Routing\Tool\ColombusRouteNodeMatcherTool;
use WebModule\Bware\SymphoBee\Routing\UriMatcher\BananaUriMatcher;
use WebModule\Bware\SymphoBee\Routing\UriMatcher\UriMatcherInterface;


/**
 * ColombusRouter
 * @author Lingtalfi
 * 2015-03-10
 *
 */
class ColombusRouter
{

    protected $routeNodes;

    /**
     * @var UriMatcherInterface
     */
    protected $matcher;
    private $ordered;

    public function __construct($routeNodes = [])
    {
        $this->routeNodes = $routeNodes;
        $this->matcher = new BananaUriMatcher();
        $this->ordered = false;
    }


    /**
     * @param HttpRequestInterface $request
     * @return false|array,
     *                  return the matching routeNode, with its vars updated
     */
    public function match(HttpRequestInterface $request)
    {

        $this->orderRouteNodes();
        foreach ($this->routeNodes as $routeNode) {
            if (false !== $vars = ColombusRouteNodeMatcherTool::match($routeNode, $request, $this->matcher)) {
                $routeNode['vars'] = $vars;
                return $routeNode;
            }
        }
        return false;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function orderRouteNodes()
    {
        if (false === $this->ordered) {
            $this->ordered = true;
            NodeTool::completeKeys($this->routeNodes, [
                'priority' => 0, // that's the priority for user defined routes
            ]);
            NodeTool::sortBy($this->routeNodes, 'priority', false);
        }
    }
}
