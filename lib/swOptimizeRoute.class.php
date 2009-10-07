<?php
/*
 * This file is part of the swOptimizeRoutesPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    swOptimizeRoutesPlugin
 * @subpackage lib
 * @author     Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * @version    SVN: $Id$
 */
class swOptimizeRoute extends sfRoute
{


  protected
    $route;

  public function __construct(sfRoute $route)
  {
    $this->route = $route;
  }


  protected function compile()
  {
    if ($this->compiled)
    {
      return;
    }

    $this->compiled = true;
  }

  public function __call($method, $arguments)
  {
    return call_user_func_array(array($this->route, $method), $arguments);
  }

  public function generate($params, $context = array(), $absolute = false)
  {

    return $this->route->generate($params, $context, true);
  }

  public function isBound()
  {

    return $this->route->isBound();
  }

  public function bind($context, $parameters)
  {

    return $this->route->bind($context, $parameters);
  }

  public function matchesUrl($url, $context = array())
  {

    return $this->route->matchesUrl($url, $context);
  }

  public function matchesParameters($params, $context = array())
  {
    // always return false to not match current application routes

    return false;
  }

  public function getPattern()
  {

    return $this->route->getPattern();
  }

  public function getRegex()
  {

    return $this->route->getRegex();
  }

  public function getTokens()
  {

    return $this->route->getTokens();
  }

  public function getOptions()
  {

    return $this->route->getOptions();
  }

  public function getVariables()
  {

    return $this->route->getVariables();
  }

  public function getDefaults()
  {

    return $this->route->getDefaults();
  }


  public function getRequirements()
  {

    return $this->route->getRequirements();
  }

  public function getDefaultParameters()
  {

    return $this->route->getDefaultParameters();
  }

  public function setDefaultParameters($parameters)
  {

    return $this->route->setDefaultParameters($parameters);
  }

  public function getDefaultOptions()
  {

    return $this->route->getDefaultOptions();
  }

  public function setDefaultOptions($options)
  {

    return $this->route->setDefaultOptions($options);
  }

  public function getSerializedData()
  {
    
    $route_serialization = $this->route->serialize();

    /*
     * $route_serialization array information : 
     *   0 : tokens
     *   1 : defaultParameters
     *   2 : defaultOptions
     *   3 : compiled
     *   4 : options
     *   5 : pattern
     *   6 : regex
     *   7 : variables
     *   8 : defaults
     *   9 : requirements
     */

    // remove unless variables ?
//    $route_serialization[0] = null;
//    $route_serialization[3] = null;
//    $route_serialization[9] = null;

    return array(
      'route_class'         => get_class($this->route),
      'route_serialization' => unserialize($route_serialization),
    );
  }

  public function setSerializedData($data)
  {
//    $data[0] = array();
//    $data[3] = true;
//    $data[9] = array();

    // TODO : test sf version, sf1.3 does not required serialization ...
    $this->route->unserialize2($data);
  }
}