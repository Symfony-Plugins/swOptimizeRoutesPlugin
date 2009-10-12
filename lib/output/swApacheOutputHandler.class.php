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
      $condition .= $this->renderCondition($name, $url, $sf_format);

      foreach($methods as $method)
      {

        $output[$method][] = $condition;
      }
    }

    $final_output = "";
    
    if( count($output['OTHER']) > 0)
    {
      $final_output .= implode("\n", $output['OTHER']);
      $final_output .= "\n\n";
    }

    if( count($output['PUT']) > 0)
    {
      $final_output .= 'RewriteCond %{REQUEST_METHOD} "GET"';
      $final_output .= "\n";
      $final_output .= implode("\n", $output['GET']);

      $final_output .= "\n\n";
    }
    
    if( count($output['HEAD']) > 0)
    {
      $final_output .= 'RewriteCond %{REQUEST_METHOD} "HEAD"';
      $final_output .= "\n";
      $final_output .= implode("\n", $output['HEAD']);

      $final_output .= "\n\n";
    }
    
    if( count($output['DELETE']) > 0)
    {
      $final_output .= 'RewriteCond %{REQUEST_METHOD} "DELETE"';
      $final_output .= "\n";
      $final_output .= implode("\n", $output['DELETE']);

      $final_output .= "\n\n";
    }
    
    if( count($output['GET']) > 0)
    {
      $final_output .= 'RewriteCond %{REQUEST_METHOD} "GET"';
      $final_output .= "\n";
      $final_output .= implode("\n", $output['GET']);

      $final_output .= "\n\n";
    }

    if( count($output['POST']) > 0)
    {
      $final_output .= 'RewriteCond %{REQUEST_METHOD} "POST"';
      $final_output .= "\n";
      $final_output .= implode("\n", $output['POST']);

      $final_output .= "\n\n";
    }

    return $final_output;
  }

  public function renderCondition($name, $url, $sf_format)
  {
    
    return sprintf('RewriteRule ^%s%-60s %s?sf_route=%s [QSA,L]',
      $this->url_prefix,
      $url,
      $this->path_prefix,
      $name 
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