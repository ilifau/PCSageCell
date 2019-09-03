<#1>
<?php
/**
 * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

/**
 * Page Component Sage Cell plugin: database update script
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @author Jesus Copado <jesus.copado@ili.fau.de>
 * @version $Id$
 */

//Version controller
$split_version = explode(".", ILIAS_VERSION_NUMERIC);
if ($split_version[0] == "5" AND $split_version[1] == "1")
{
	global $ilDB;
	$db = $ilDB;
} else
{
	global $DIC;
	$db = $DIC->database();
}

/**
 * config values
 */
if (!$db->tableExists('pcsage_config'))
{
	$fields = array('parameter_name' => array('type' => 'text', 'length' => 255, 'notnull' => true), 'value' => array('type' => 'clob'));
	$db->createTable("pcsage_config", $fields);
	$db->addPrimaryKey("pcsage_config", array("parameter_name"));
}

/*
 * Check if config entries in DB have been created, otherwise create it.
 */
$query = 'SELECT * FROM pcsage_config';
$result = $db->query($query);
if (!$db->fetchAssoc($result))
{
	//Default values for sagemath_server_address
	$db->insert("pcsage_config", array('parameter_name' => array('text', 'sagemath_server_address'), 'value' => array('clob', '')));

	//Default values for sagemath_server_address
	$db->insert("pcsage_config", array('parameter_name' => array('text', 'force_evaluate_button'), 'value' => array('clob', TRUE)));
}
?>
<#2>
<?php
global $DIC;
$db = $DIC->database();

if ($db->tableExists('pcsage_config'))
{
    $query = 'RENAME TABLE pcsage_config TO copg_pgcp_pcsage_config';
    $db->manipulate($query);
}
?>
