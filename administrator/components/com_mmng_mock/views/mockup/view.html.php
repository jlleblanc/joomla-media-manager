<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class MmngViewMockup extends JView
{
	function display($tpl = null)
	{
		JHtml::_('behavior.framework', true);
		JHTML::_('script','system/mootree.js', false, true);
		JHTML::_('stylesheet','system/mootree.css', array(), true);
		JHTML::_('stylesheet',JURI::base().'components/com_mmng_mock/views/mockup/tmpl/mockup.css', array(), false);
		
		parent::display($tpl);
	}
}