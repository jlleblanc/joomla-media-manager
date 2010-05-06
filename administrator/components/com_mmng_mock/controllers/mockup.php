<?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Media Manager Next Generation Mockups Controller
 */
class MmngControllerMockup extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$viewName = JRequest::getCmd('view', 'mockup');

		$document = &JFactory::getDocument();
		$viewType		= $document->getType();

		// Get/Create the view
		$view = &$this->getView($viewName, $viewType);

		// Set the layout
		$vLayout = JRequest::getCmd('layout', 'default');
		$view->setLayout($vLayout);

		// Display the view
		$view->display();
	}
}
