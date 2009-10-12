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
 * @subpackage output
 * @author     Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * @version    SVN: $Id$
 */
class swOptimizeRoutingConfigHandler extends sfRoutingConfigHandler
{

  public function appendData(&$data, $route, $name)
  {

    $arguments = array();

    if($route instanceof sfRoute)
    {
      $route_instance = $route;
    }
    else
    {
      $r = new ReflectionClass($route[0]);
      $route_instance = $r->newInstanceArgs($route[1]);
    }

    $export = array(
      'route_class' => get_class($route_instance),
      'route_serialization' => $route_instance->serialize()
    );

    $data[] = sprintf('\'%s\' => %s,', $name, var_export($export, 1));
  }


  public function execute($configFiles)
  {
    $routes = $this->parse($configFiles);

    $data = array();

    foreach ($routes as $name => $route)
    {
      $r = new ReflectionClass($route[0]);
      if($r->isSubclassOf('sfRouteCollection'))
      {
        $collection_route = $r->newInstanceArgs($route[1]);

        foreach($collection_route->getRoutes() as $name => $route)
        {
          $this->appendData($data, $route, $name);
        }
      }
      else
      {
        $this->appendData($data, $route, $name);
      }
    }

    return sprintf("<?php\n".
                   "// auto-generated by swOptimizeRoutingConfigHandler\n".
                   "// date: %s\nreturn array(\n%s\n);\n", date('Y/m/d H:i:s'), implode("\n", $data)
    );
  }
  
//  public function evaluate($configFiles)
//  {
//
//    $routeDefinitions = $this->parse($configFiles);
//    foreach ($routeDefinitions as $name => $route)
//    {
//      $r = new ReflectionClass($route[0]);
//
//      if($r->isSubclassOf('sfRouteCollection'))
//      {
//       $collection_route = $r->newInstanceArgs($route[1]);
//
//        foreach($collection_route->getRoutes() as $name => $route)
//        {
//          $routes[$name] = new swOptimizeRoute($route);
//        }
//      }
//      else
//      {
//        $routes[$name] = new swOptimizeRoute($r->newInstanceArgs($route[1]));
//      }
//    }
//
//    return $routes;
//  }
}
