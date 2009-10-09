<?php
/*
 * This file is part of the swOptimizeRoutesPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function create_routing($options)
{
  $routing_options = isset($options['routing_options']) ? $options['routing_options'] : array();
  $path       = isset($options['routing_file']) ? $options['routing_file'] : dirname(__FILE__).'/../data/optimize-routing.yml';
  $tester     = isset($options['tester']) ? $options['tester'] : null;
  $dispatcher = isset($options['dispatcher']) ? $options['dispatcher'] : new sfEventDispatcher;
  $cache      = isset($options['cache']) ? $options['cache'] : null;
  $configuration  = isset($options['configuration']) ? $options['configuration'] : null;
  $load  = isset($options['load']) ? $options['load'] : true;
  $class = isset($options['class']) ? $options['class'] : 'swOptimizePatternRouting';
  

  if($tester) $tester->diag('Registering the swOptimizeRoutingConfigHandler config handler');

  if($configuration == null)
  {
    
    throw new sfException('Please provide a valid configuration object');
  }
  
  $configuration->getConfigCache()->registerConfigHandler($path, 'swOptimizeRoutingConfigHandler');

  $config_file = $configuration->getConfigCache()->checkConfig($path, true);

  if(is_file($config_file))
  {
    if($tester) $tester->diag('Delete the current configuration file');
    unlink($config_file);
  }

  if($tester) $tester->diag('Create the configuration file');
  $config_file = $configuration->getConfigCache()->checkConfig($path, true);

  $routing = new $class($dispatcher, $cache, $routing_options);
  if($load)
  {
    $routing->setRoutes(include($config_file));
  }
  
  return $routing;
}