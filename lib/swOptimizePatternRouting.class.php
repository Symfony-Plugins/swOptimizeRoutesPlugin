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
class swOptimizePatternRouting extends sfPatternRouting
{

  protected
    $raw_routes = array(),
    $configuration = null;
    
  /**
   * @see sfRouting
   */
  public function setRoutes($routes)
  {
    $this->raw_routes = $routes;
  }
  
  public function findRoute($url)
  {

    // sometime old school PHP just make sense ;)
    if(isset($_GET['sf_route']))
    {
      
      return $this->getRouteInformation($_GET['sf_route'], $url);
    }

    throw new sfException('The route cannot be match by the server');
    
    return parent::findRoute($url);
  }

  protected function getRouteInformation($name, $url)
  {

    if(!isset($this->raw_routes[$name]))
    {
      
      return false;
    }

    $route = $this->loadRoute($name);

    // the server routing system match the route, but
    // the current url does not match internal requirement (like star option)
    if(!($parameters = $this->routes[$name]->matchesUrl($url, $this->options['context'])))
    {
      
      return false;
    }
    
    $information = array('name' => $name, 'pattern' => $route->getPattern(), 'parameters' => $parameters);

    return $information;
  }

  public function loadRoute($name)
  {

    if(!isset($this->routes[$name]))
    {
      $route_information = $this->raw_routes[$name];

      $class = $route_information['route_class'];
      $data  = $route_information['route_serialization'];

      // set array in a config file
      // or add options to call the event dispatcher
      $options = array();
      if(in_array($class, array('sfDoctrineRoute', 'sfPropelRoute')))
      {
        $options['model'] = 'foo';
        $options['type'] = 'object';
      }

      $route = new $class('/foo', array(), array(), $options);
      $route->unserialize($data);
      
      $this->routes[$name] = $route;

      if ($this->options['logging'])
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Load route on demand "%s" (%s)', get_class($route), $name, $route->getPattern()))));
      }
    }

    return $this->routes[$name];
  }

  public function generate($name, $params = array(), $absolute = false)
  {
    
    if (!isset($this->raw_routes[$name]))
    {
      throw new sfConfigurationException(sprintf('The route "%s" does not exist. You must use a named route', $name));
    }

    $route = $this->loadRoute($name);

    $route->setDefaultParameters($this->defaultParameters);
    
    $url = $route->generate($params, $this->options['context'], $absolute);

    return $this->fixGeneratedUrl($url, $absolute);
  }

  /**
   *
   * The parameter should be used only for testing purpose, it also
   * try to remove depency to the sfContext
   *
   * @param sfApplicationConfiguration $configuration
   *
   */
  public function setConfiguration(sfApplicationConfiguration $configuration)
  {

    $this->configuration = $configuration;
  }

  /**
   * return the sfApplicationConfiguration linked to the sfRouting
   *
   * @return sfApplicationConfiguration related to the sfRouting
   */
  public function getConfiguration()
  {

    return $this->configuration instanceof sfApplicationConfiguration ?   
      $this->configuration :
      sfContext::getInstance()->getConfiguration();
  }

  public function loadConfiguration()
  {
    if ($this->options['load_configuration']
      && $config =  $this->getConfiguration()->getConfigCache()->checkConfig('config/routing.yml', true))
    {
      $this->setRoutes(include($config));

      $this->dispatcher->notify(new sfEvent($this, 'routing.load_configuration'));
    }
  }
}

