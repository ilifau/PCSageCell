<?php
/**
 * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

include_once("./Services/COPage/classes/class.ilPageComponentPlugin.php");
 
/**
 * Page Component Sage Cell plugin
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @author Jesus Copado <jesus.copado@ili.fau.de>
 * @version $Id$
 */
class ilPCSageCellPlugin extends ilPageComponentPlugin
{
	/**
	 * Get plugin name
	 *
	 * @return string
	 */
	function getPluginName()
	{
		return "PCSageCell";
	}

	/**
	 * Get plugin name
	 *
	 * @return string
	 */
	function isValidParentType($a_parent_type)
	{
		if (in_array($a_parent_type, array("lm")))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get Javascript files
	 * @param    string $a_mode
	 * @return    array
	 */
	function getJavascriptFiles($a_mode = '')
	{
		return array();
	}

	/**
	 * Get css files
	 * @param    string $a_mode
	 * @return    array
	 */
	function getCssFiles($a_mode = '')
	{
		return array();
	}


	/**
	 * Get the URL path of the plugin
	 * @return    string    URL base path for including special javascript and css files
	 */
	public function getUrlPath()
	{
		return './Customizing/global/plugins/Services/COPage/PageComponent/PCSageCell/';
	}

	/**
	 * @const    string    URL suffix to prevent caching of css files (increase with every version change)
	 *                    Note: this does not yet work with $tpl->addJavascript()
	 */
	public function getUrlSuffix()
	{
		return "?css_version=" . $this->getVersion();
	}


	/**
	 * Prepare the code for being shown in the properties form
	 * @param string $a_code
	 * @return string
	 */
	public function prepareCodeFormOutput($a_code)
	{
		$a_code = str_replace('{', '&#123;', $a_code);
		$a_code = str_replace('}', '&#125;', $a_code);

		return $a_code;
	}

	/**
	 * Prepare the code for being shown on the page presentation
	 * @param string $a_code
	 * @return string
	 */
	public function prepareCodePageOutput($a_code)
	{
		//We have to replace carriage return ascii &#13; with \r in order to get a proper display of the code
		$a_code = str_replace('&#13;', "\r", $a_code);

		// braces may be used in code, so don't replace them
		return $a_code;
	}
}