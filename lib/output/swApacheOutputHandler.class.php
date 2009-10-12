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
class swApacheOutputHandler extends swBaseOutputHandler
{

  /**
   *
   * @see swBaseOutputHandler
   */
  public function generate()
  {

    $output = array(
      'HEAD' => array(),
      'PUT' => array(),
      'DELETE' => array(),
      'POST' => array(),
      'GET'  => array(),
      'OTHER' => array(),
    );
    
    foreach($this->routing->getRoutes() as $name => $route)
    {
      $url = "";
      $condition = "";
      $requirements = $route->getRequirements();
      $sf_format = false;
      foreach($route->getTokens() as $val => $token)
      {
        if($val == 0) // remove starting /
        {

          continue;
        }

        switch($token[0])
        {
          case 'separator':
          case 'text':
            if($token[2] == '*')
            {
              
              $requirement = '(.+)';
            }
            else if($token[2] == '.')
            {
              $url .= '\.';
            }
            else
            {
              $url .= $token[2];
            }

            break;

          case 'variable':
            $variable = $token[3];

            if($variable == 'sf_format')
            {
              $url = substr($url, 0, -2);
              $url .= '(\.([^/\.])+$|$)';
              $sf_format = true;
            }
            else
            {
              $url .= sprintf('(%s)', $requirements[$variable]);
            }

            break;
        }
      }

      $methods = $this->getMethods($requirements['sf_method']);

      $url = $this->fixUrl($route, $url, $sf_format);
      
      foreach($methods as $method)
      {
        $output[$method][] = $this->renderCondition($name, $url, $sf_format, $method);
      }
    }

    $final_output = "";
    
    if( count($output['OTHER']) > 0)
    {
      $final_output .= implode("\n", $output['OTHER']);
      $final_output .= "\n\n";
    }

    foreach(array('PUT', 'HEAD', 'DELETE', 'GET', 'POST') as $sf_method)
    {
      if( count($output[$sf_method]) > 0)
      {
        $condition = sprintf('RewriteCond %%{REQUEST_METHOD} =%s', $sf_method);
        
        $final_output .= $condition."\n".implode("\n$condition\n", $output[$sf_method]);
        $final_output .= "\n\n";
      }
    }
    
    return $final_output;
  }

  public function renderCondition($name, $url, $sf_format, $sf_method)
  {
    
    return sprintf('RewriteRule ^%s%-60s %s?sf_route=%s&sf_method=%s [QSA,L]',
      $this->url_prefix,
      $url,
      $this->path_prefix,
      $name ,
      $sf_method
    );

  }


  // NOTE to handle .:sf_format option : (\.([^/\.])+$|$)
  public function fixUrl($route, $url, $sf_format)
  {

    $options = $this->routing->getOptions();
    $extra_parameters_as_query_string = $options['extra_parameters_as_query_string'];

    //$url = substr($url, 0); // remove starting slash

    if($extra_parameters_as_query_string == true)
    {

      return $url.($sf_format ? '' : '$');
    }

    return $url.'(.+)'.($sf_format ? '' : '$');
  }
}