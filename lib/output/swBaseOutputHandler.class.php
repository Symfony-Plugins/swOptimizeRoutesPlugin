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
    $url_prefix = '',
    $path_prefix = '/index.php';


  /**
   * Default contructor
   *
   * @param array $routes
   *
   */
  public function __construct(sfRouting $routing, $options = array())
  {
    
    $this->routing = $routing;
    $this->path_prefix = $options['path_prefix'];
    $this->url_prefix = $options['url_prefix'];
  }

  public function getMethods($methods)
  {

    if($methods === null)
    {
      $methods = array('OTHER');
    }

    if(!is_array($methods))
    {
      $methods = array($methods);
    }

    // clean the sf_format, as the sf_format
    // is just simulated
    $final_methods = array();
    $simulated_formats = array('PUT', 'DELETE', 'HEAD');
    foreach($methods as $method)
    {
      $method = strtoupper($method);

      $final_methods[] = $method;
      if( in_array($method, $simulated_formats) && !in_array('POST', $methods))
      {
        $final_methods[] = 'POST';
      }
    }


    return $final_methods;
  }

  /**
   *
   * @return string the output to use in the server configuration file
   */
  abstract function generate();
}
