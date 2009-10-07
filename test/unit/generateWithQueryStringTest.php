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

$routing = create_routing(array(
  'cache' => $cache,
  'dispatcher' => $dispatcher,
  'tester' => $t,
  'routing_options' => $routing_options,
  'configuration' => $configuration
));

$_GET = array(
  'sf_route' => 'users_export',
);

$route_information = $routing->findRoute('/users/10/export/ert');
$expected_information = array (
  'name' => 'users_export',
  'pattern' => '/users/:id/export/:document',
  'parameters' => array (
    'module' => 'sf_guard_user',
    'action' => 'export',
    'id' => '10',
    'document' => 'ert',
  ),
);

$t->cmp_ok($route_information, '===', $expected_information, '::findRoute() sfRoute ok');


$_GET = array(
  'sf_route' => 'address_show',
);

$route_information = $routing->findRoute('/address/12');
$expected_information = array (
  'name' => 'address_show',
  'pattern' => '/address/:id.:sf_format',
  'parameters' =>   array (
    'module' => 'mbAddress',
    'action' => 'show',
    'sf_format' => 'html',
    'id' => '12',
  ),
);

$t->cmp_ok($route_information, '===', $expected_information, '::findRoute() sfCollectionRoute ok');

$t->diag('Generate routes with extra_parameters_as_query_string = true');

$url = $routing->generate('generate_rtf', array(
  'type' => 'toto',
  'firstname' => 'thomas',
  'lastname'  => 'lastname',
  'extraparams' => 'test'
));
$expected = '/index.php/generate-rtf/toto/thomas/lastname?extraparams=test';
$t->cmp_ok($url, '===', $expected, '::generate() generate_rtf (class: sfRoute) ok');


$object = new sfGuardUser;
$object->id = 10;
$url = $routing->generate('users_export', array(
  'document'    => 'new-registered',
  'sf_subject'  => $object,
  'extraparams' => 'test'
));

$expected = '/index.php/users/10/export/new-registered?extraparams=test';

$t->cmp_ok($url, '===', $expected, '::generate() users_export (class: sfDoctrineRoute) ok');


$url = $routing->generate('simple_post_route', array(
  'extraparams' => 'test'
));

$expected = '/index.php/trustee/edit?extraparams=test';

$t->cmp_ok($url, '===', $expected, '::generate() simple_post_route (class: sfRoute) ok');

$url = $routing->generate('star_route', array(
  'param1' => 'toto',
  'param2' => 'thomas',
  'extraparams' => 'test'
));

$expected = '/index.php/address_add_building/toto/thomas/extraparams/test';

$t->cmp_ok($url, '===', $expected, '::generate() star_route (class: sfRoute) ok');

$url = $routing->generate('address_show', array(
  'id' => '12',
  'extraparams' => 'test'
));

$expected = '/index.php/address/12?extraparams=test';

$t->cmp_ok($url, '===', $expected, '::generate() address_show (class: sfCollectionRoute) ok');
