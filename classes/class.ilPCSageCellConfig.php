<?php
/**
 * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

/**
 * Page Component Sage Cell  plugin config
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @author Jesus Copado <jesus.copado@ili.fau.de>
 * @version $Id$
 *
 */
class ilPCSageCellConfig
{
	/**
	 * @var string
	 */
	protected $sagemath_server_address;

	/**
	 * @var boolean
	 */
	protected $force_evaluate_button;

	/**
	 * ilPCSageCellConfig constructor.
	 */
	public function __construct()
	{
		//Get config data from DB
		$this->loadFromDB();
	}

	/**
	 * Set DB values for each parameter as current value
	 */
	public function loadFromDB()
	{
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

		//Get all config data from DB
		$query = 'SELECT * FROM copg_pgcp_pcsage_config';
		$result = $db->query($query);
		while ($row = $db->fetchAssoc($result))
		{
			switch ($row["parameter_name"])
			{
				case "sagemath_server_address":
					$this->setSagemathServerAddress($row["value"]);
					break;
				case "force_evaluate_button":
					$this->setForceEvaluateButton((boolean)$row["value"]);
					break;
				default:
					break;
			}
		}
	}

	/**
	 * Save config values to DB
	 * @return bool status
	 */
	public function save()
	{

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

		$field_data_sagemath_server_address = array("value" => array("clob", $this->getSagemathServerAddress()));
		$db->update("copg_pgcp_pcsage_config", $field_data_sagemath_server_address, array("parameter_name" => array("text", "sagemath_server_address")));

		$field_data_force_evaluate_button = array("value" => array("clob", (string)$this->getForceEvaluateButton()));
		$db->update("copg_pgcp_pcsage_config", $field_data_force_evaluate_button, array("parameter_name" => array("text", "force_evaluate_button")));

		return TRUE;
	}

	/*
	 * GETTERS AND SETTERS
	 */

	/**
	 * @return string
	 */
	public function getSagemathServerAddress()
	{
		return $this->sagemath_server_address;
	}

	/**
	 * @param string $sagemath_server_address
	 */
	public function setSagemathServerAddress($sagemath_server_address)
	{
		$this->sagemath_server_address = $sagemath_server_address;
	}

	/**
	 * @return boolean
	 */
	public function getForceEvaluateButton()
	{
		return $this->force_evaluate_button;
	}

	/**
	 * @param boolean $force_evaluate_button
	 */
	public function setForceEvaluateButton($force_evaluate_button)
	{
		$this->force_evaluate_button = $force_evaluate_button;
	}


}
