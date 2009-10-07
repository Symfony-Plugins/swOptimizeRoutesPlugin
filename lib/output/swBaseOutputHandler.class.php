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
abstract class swBaseOutputHandler
{

  protected
    $routing = null,
    $path_prefix = '/index.php';


  /**
   * Default contructor
   *
   * @param array $routes
   *
   */
  public function __construct(sfRouting $routing)
  {
    $this->routing = $routing;
  }

  /**
   *
   * @return string the output to use in the server configuration file
   */
  abstract function generate();
}
