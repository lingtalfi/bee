<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\AjaxService;

use Bee\Bat\HttpTool;
use WebModule\Komin\Base\Server\AjaxService\Filter\AjaxFilterException;
use WebModule\Komin\Base\Server\AjaxService\Filter\AjaxServiceFilterInterface;
use Komin\Notation\String\ShellExpansion\ShellExpansionUtil;


/**
 * AjaxService
 * @author Lingtalfi
 *
 *
 */
abstract class AjaxService implements AjaxServiceInterface
{

    protected $nodes;
    protected $filters;
    protected $errors;

    /**
     * @var ShellExpansionUtil
     */
    protected $shellExpansionUtil;


    public function __construct(array $nodes, array $filters = [])
    {
        $this->nodes = $nodes;
        $this->filters = $filters;
        $this->errors = [];
        $this->shellExpansionUtil = $this->getShellExpander();
    }

    /**
     * @return mixed|false, false on failure, in which case errors should be set.
     *                      mixed in case of success.
     */
    abstract public function doExecute($id, array $params = []);

    //------------------------------------------------------------------------------/
    // IMPLEMENTS AjaxServiceInterface
    //------------------------------------------------------------------------------/
    public function execute($id, array $params = [])
    {
        $params = HttpTool::resolveLiteralValue($params);
        if (false !== $node = $this->match($id)) {

            try {
                foreach ($this->filters as $filter) {
                    /**
                     * @var AjaxServiceFilterInterface $filter
                     */
                    $filter->filter($node);
                }
            } catch (AjaxFilterException $e) {
                $this->error($e->getMessage());
                return false;
            }
            if (array_key_exists('params', $node) && is_array($node['params'])) {
                $params = array_replace($node['params'], $params);
            }

            return $this->doExecute($id, $params);

        }
        else {
            $this->error(sprintf("id does not match: %s", $id));
        }
        return false;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    /**
     * @return false|node
     */
    protected function match($id)
    {
        foreach ($this->nodes as $k => $node) {
            if (true === $this->shellExpansionUtil->match($k, $id)) {
                return $node;
            }
        }
        return false;
    }

    protected function getShellExpander()
    {
        return new ShellExpansionUtil([
            'sepChar' => '.',
        ]);
    }

    protected function error($msg)
    {
        $msg = 'AjaxService: ' . $msg;
        $this->errors[] = $msg;
    }
}
