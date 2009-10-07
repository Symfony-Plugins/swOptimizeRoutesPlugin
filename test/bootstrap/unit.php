<?php

/*
 * This file is part of the swOptimizeRoutesPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);

$manager = new sfDatabaseManager($configuration);
$manager->initialize($configuration);

include_once ($configuration->getSymfonyLibDir().'/vendor/lime/lime.php');
include_once dirname(__FILE__).'/../lib/swOptimizeHelper.php';

$dispatcher = new sfEventDispatcher;
$cache = new sfFileCache(array(
  'cache_dir' => dirname(__FILE__).'/../cache'
));

