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


$path = dirname(__FILE__).'/../data/optimize-routing.yml';

$t->diag('Registering the swOptimizeRoutingConfigHandler config handler');
$configuration->getConfigCache()->registerConfigHandler($path, 'swOptimizeRoutingConfigHandler');

$config_file = $configuration->getConfigCache()->checkConfig($path, true);

if(is_file($config_file))
{
  $t->diag('Delete the current configuration file');
  unlink($config_file);
}

$t->diag('Create the configuration file');
$config_file = $configuration->getConfigCache()->checkConfig($path, true);

$routes = include($config_file);

$t->cmp_ok(count($routes) , '===', 13, 'count() routes ok');
