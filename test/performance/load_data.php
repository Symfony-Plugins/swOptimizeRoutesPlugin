<?php
/*
 * This file is part of the swOptimizeRoutesPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(null, new lime_output_color());

$context = array(
  'path_info'   => '/test/route_350/350',
  'prefix'      => '/index.php',
  'method'      => 'get',
  'format'      => 'html',
  'host'        => 'rabaix.net',
  'is_secure'   => false,
  'request_uri' => '/index.php/test/route_350/350',
  'lookup_cache_dedicated_keys' => true
);

$routing_options = array(
  'auto_shutdown' => false,
  'context' => $context,
  'load_configuration' => false,
  'suffix' => '',
  'default_module' => 'default',
  'default_action' => 'index',
  'debug' => false,
  'logging' => false,
  'generate_shortest_url' => true,
  'extra_parameters_as_query_string' => true,
);

$sf_pattern_routing = create_routing(array(
  'cache' => $cache,
  'dispatcher' => $dispatcher,
  'tester' => $t,
  'routing_options' => $routing_options,
  'configuration' => $configuration,
  'class' => 'sfPatternRouting',
  'load' => false
));


$t->diag("TEST 1 : sf1.2 / sfPatternRouting - object instanciation");
$time = microtime(true);
$memory = memory_get_usage();
$sf_pattern_routing->setRoutes(include(dirname(__FILE__).'/../cache/test1.cache'));
$t->diag(sprintf("TEST 1 - size: %f, include time: %f, memory: %f",
  filesize(dirname(__FILE__).'/../cache/test2.cache'),
  microtime(true) - $time,
  memory_get_usage(true) - $memory
));

$t->diag("TEST 2 : sf1.2 / sfPatternRouting -  object deserialization as if cache is enabled");
$time = microtime(true);
$memory = memory_get_usage();
$sf_pattern_routing->setRoutes(unserialize(file_get_contents(dirname(__FILE__).'/../cache/test2.cache')));
$t->diag(sprintf("TEST 2 - size: %f, include time: %f, memory: %f",
  filesize(dirname(__FILE__).'/../cache/test2.cache'),
  microtime(true) - $time,
  memory_get_usage(true) - $memory
));

$sw_pattern_routing = create_routing(array(
  'cache' => $cache,
  'dispatcher' => $dispatcher,
  'tester' => $t,
  'routing_options' => $routing_options,
  'configuration' => $configuration,
  'class' => 'swOptimizePatternRouting',
  'load' => false
));

$t->diag("TEST 3 : sf1.2 / swOptimizePatternRouting - array with serialized route informations");
$time = microtime(true);
$memory = memory_get_usage();
$sw_pattern_routing->setRoutes(include(dirname(__FILE__).'/../cache/test3.cache'));
$t->diag(sprintf("TEST 3 - size: %f, include time: %f, memory: %f",
  filesize(dirname(__FILE__).'/../cache/test3.cache'),
  microtime(true) - $time,
  memory_get_usage(true) - $memory
));

//
//ini_set('xcache.var_size', '24M');
//
//$t->diag("TEST 3.xcache : sf1.2 - swOptimized routes - array with serialized route informations");
//xcache_set('routes', include(dirname(__FILE__).'/../cache/test3.cache'));
//$time = microtime(true);
//$memory = memory_get_usage();
//$sw_pattern_routing->setRoutes(xcache_get('routes'));
//$t->diag(sprintf("TEST 3 - size: %f, time: %f, memory: %f",
//  filesize(dirname(__FILE__).'/../cache/test3.cache'),
//  microtime(true) - $time,
//  memory_get_usage(true) - $memory
//));



$t->diag("TEST 4 : sf1.3 / swOptimizePatternRouting - full array");
$time = microtime(true);
$memory = memory_get_usage();
$sw_pattern_routing->setRoutes(include(dirname(__FILE__).'/../cache/test4.cache'));
$t->diag(sprintf("TEST 4 - size: %f, include time: %f, memory: %f",
  filesize(dirname(__FILE__).'/../cache/test4.cache'),
  microtime(true) - $time,
  memory_get_usage(true) - $memory
));

