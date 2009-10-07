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
 * @subpackage task
 * @author     Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * @version    SVN: $Id$
 */
class swOptimizeRouteTask extends sfBaseTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('server', sfCommandArgument::REQUIRED, 'The server name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The plugin version'),
    ));

    $this->namespace = 'app';
    $this->name = 'optimize-routes';

    $this->briefDescription = 'Generate mod_write routes corresponding to the application\'s routing';

    $this->detailedDescription = <<<EOF
The [app:optimize-routes|INFO] Generate mod_write routes corresponding to the application\'s routing:

  [./symfony app:optimize-routes frontend apache|INFO]

  server options :
    - apache
    - lighttpd
    - nginx

EOF;
  }

  /**
   * @see sfTask
   */
  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // get routes
    $config = new sfRoutingConfigHandler();
    $routes = $config->evaluate($this->configuration->getConfigPaths('config/routing.yml'));

    $routing = new sfPatternRouting($this->dispatcher);
    $routing->setRoutes($routes);

    $this->dispatcher->notify(new sfEvent($routing, 'routing.load_configuration'));

    $routes = $routing->getRoutes();

    $classes = array(
      'apache' => 'swApacheOutputHandler',
    );

    if(!array_key_exists($arguments['server'], $classes))
    {
      throw new sfException(sprintf('"%s" is not a valid option, available servers : [%s]',
        $arguments['server'],
        implode('|', array_keys($classes))
      ));
    }


    $class = $classes[$arguments['server']];

    $output = new $class($routing);

    echo $output->generate();
  }
}