<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage Autoloader
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Resource.php 18173 2009-09-17 15:35:05Z padraic $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Loader_Autoloader_Interface */
// require_once 'Zend/Loader/Autoloader/Interface.php';

/**
 * Resource loader
 * 
 * @uses       Zend_Loader_Autoloader_Interface
 * @package    Zend_Loader
 * @subpackage Autoloader
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Loader_Autoloader_Module extends Zend_Loader_Autoloader_Resource implements Zend_Loader_Autoloader_Interface
{

    /**
     * Attempt to autoload a class
     * 
     * @param  string $class 
     * @return mixed False if not matched, otherwise result if include operation
     */
    public function autoload($class)
    {
        $segments          = explode('_', $class);
        $namespaceTopLevel = $this->getNamespace();
        $namespace         = '';

        if (!empty($namespaceTopLevel)) {
            $namespace = array_shift($segments);
            if ($namespace != $namespaceTopLevel) {
                // wrong prefix? we're done
                return false;
            }
        }

        if (count($segments) < 2) {
            // assumes all resources have a component and class name, minimum
            return false;
        }

        // Interject /controllers/ into the path for module controllers:
        if ($segments[0] == 'Module' && strpos(end($segments),'Controller')) {
            $controller_class = $segments;
            $controller_class[1] = $controller_class[1].'_controllers';
            $class = implode('_',$controller_class);
        }

        $final     = array_pop($segments);
        $component = $namespace;
        $lastMatch = false;
        do {
            $segment    = array_shift($segments);
            $component .= empty($component) ? $segment : '_' . $segment;
            if (isset($this->_components[$component])) {
                $lastMatch = $component;
            }
        } while (count($segments));

        if (!$lastMatch) {
            return false;
        }

        $final = substr($class, strlen($lastMatch));
        $path = $this->_components[$lastMatch];

        return include $path . '/' . str_replace('_', '/', $final) . '.php';
    }

}
