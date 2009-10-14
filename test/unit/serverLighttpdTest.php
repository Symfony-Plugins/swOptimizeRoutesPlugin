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

$t = new lime_test(1, new lime_output_color());


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

$routing = create_routing(array(
  'cache' => $cache,
  'dispatcher' => $dispatcher,
  'tester' => $t,
  'routing_options' => $routing_options,
  'configuration' => $configuration,
  'class' => 'sfPatternRouting',
  'config_class' => 'sfRoutingConfigHandler'
));

//
// notes:
//
//   3 groups : (part1)(sf_format|)(query_string|)
//   6 groups :  per variables
//   1 group  : result[0] contains the full url
//  10 groups
//
// 10th group is the query string

// query string : true
$super_ereg = '/^(\/test\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([a-z]*)(\.([a-z]*)|))(\?(.*)|)$/';

$urls = array(
  '/test/23/120/123/123/edit.html?toto=titi' => $super_ereg,
  '/test/23/120/123/123/edit?toto=titi' => $super_ereg,
  '/test/23/120/123/123/edit' => $super_ereg,
  '/test/23/120/123/123/edit.html' => $super_ereg,
);

foreach($urls as $url => $ereg)
{
  preg_match($ereg, $url, $results);

  var_dump(sprintf('/index.php%s?sf_route=toto&%s',
    $results[1],
    $results[10]
  ));
}


//
// notes:
//
//   2 groups : (part1)(sf_format|)
//   6 groups :  per variables
//   1 group  : result[0] contains the full url
//  9 groups
//
// 9th group is the query string
//
// query string : false
$super_ereg = '/^(\/test\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([a-z]*)(\.([a-z]*)|))$/';

$urls = array(
  '/test/23/120/123/123/edit.html' => $super_ereg,
  '/test/23/120/123/123/edit' => $super_ereg,
);

foreach($urls as $url => $ereg)
{
  preg_match($ereg, $url, $results);

  var_dump(sprintf('/index.php%s?sf_route=toto&%s',
    $results[1],
    $results[9]
  ));
}

$output = new swLighttpdOutputHandler($routing);

echo $output->generate();
