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

    $output = "";
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

      $url = $this->fixUrl($route, $url, $sf_format);
      $condition .= $this->renderCondition($name, $url, $sf_format);

      $output .= $condition."\n";
    }
    
    return $output;
  }

  public function renderCondition($name, $url, $sf_format)
  {
    
    return sprintf('RewriteRule ^%-60s %s?sf_route=%s [QSA,L]',
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