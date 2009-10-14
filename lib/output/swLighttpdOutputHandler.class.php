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
class swLighttpdOutputHandler extends swBaseOutputHandler
{
  
  /**
   *
   * @see swBaseOutputHandler
   */
  public function generate()
  {
    
    foreach($this->routing->getRoutes() as $name => $route)
    {
      $url = "";
      $requirements = $route->getRequirements();
      $sf_format = false;
      $pos_query_string = 1;
      
      // recompute the full ereg
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
              $url = substr($url, 0, -1);
              $url .= '(/(.+)|)';

              $pos_query_string += 2;
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
            $pos_query_string++;
            $variable = $token[3];

            if($variable == 'sf_format')
            {

              $url = substr($url, 0, -2);
              $url .= '(\.([^/\.])+|)';
              $sf_format = true;
              $pos_query_string += 2;
            }
            else
            {
              $url .= sprintf('(%s)', $requirements[$variable]);
            }

            break;
        }
      }
      
      // fix the computed url
      $options = $route->getOptions();
      $extra_parameters_as_query_string = $options['extra_parameters_as_query_string'];

      $url = '('.$this->url_prefix.$url.')';
      $pos_query_string++;
       
      if($extra_parameters_as_query_string == true)
      {
        $pos_query_string++;
        $url .= '(\?(.*)|)';
      }

      // add the url to the differents route's method
      $methods = $this->getMethods($requirements['sf_method']);
      foreach($methods as $method)
      {
        
        $condition = sprintf('"^%-70s => "%s/$1?sf_route=%s&$%d"',
          $url."\"",
          $this->path_prefix,
          $name,
          $pos_query_string
        );

        $this->output_by_methods[$method][] = $condition;
      }
    }

    $final_output = "";
    
    if( count($this->output_by_methods['OTHER']) > 0)
    {
      $final_output .= implode("\n", $this->output_by_methods['OTHER']);
      $final_output .= "\n\n";
    }

    foreach(array('PUT', 'HEAD', 'DELETE', 'GET', 'POST') as $sf_method)
    {
      if( count($this->output_by_methods[$sf_method]) == 0)
      {
        continue;
      }

      $final_output .= sprintf(
        "\$HOST['request-method'] == '%s' { \n".
        "  url.rewrite-once {\n".
        "    %s \n".
        "  }\n".
        "}\n\n"
      , $sf_method, implode("\n    ", $this->output_by_methods[$sf_method]));
    }
    
    return $final_output;
  }
}