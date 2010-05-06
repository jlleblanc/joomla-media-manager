<?php
/**
 * @version		$Id$
 * @package		mmng
 * @subpackage	mockups
 * @copyright	Copyright (C) 2010 Nicholas K. Dionysopoulos / Joseph L. LeBlanc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_mmng_mockup')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependencies
jimport('joomla.application.component.controller');

// Get the view and controller names
$view = JRequest::getCmd('view','mockup');
$controller = JRequest::getCmd('c', null);
$task = JRequest::getCmd('task', 'default');
if(empty($controller)) $controller = $view;

// Load the controller

$cmd = JRequest::getCmd('task', null);


// Set the name for the controller and instantiate it
jimport('joomla.filesystem.file');
$controllerClass = 'MmngController'.ucfirst($controller);
$controllerPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.strtolower($controller).'.php';
if (JFile::exists($controllerPath)) {
	require_once( $controllerPath );
	$controller = new $controllerClass();
} else {
	JError::raiseError(500, JText::_('JERROR_INVALID_CONTROLLER_CLASS'));
}

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();