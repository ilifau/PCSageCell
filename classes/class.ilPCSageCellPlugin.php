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

}