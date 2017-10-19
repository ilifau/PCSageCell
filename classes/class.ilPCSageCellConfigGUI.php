<?php
/**
 * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

/**
 * Page Component Sage Cell  plugin config GUI
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @author Jesus Copado <jesus.copado@ili.fau.de>
 * @version $Id$
 *
 */
class ilPCSageCellConfigGUI extends ilPluginConfigGUI
{

	/**
	 * @var ilPCSageCellConfig configuration class
	 */
	protected $object;

	/**
	 * @var array array of fields to show
	 */
	protected $fields;

	/**
	 * @var ilPropertyFormGUI configuration form
	 */
	protected $form;

	/**
	 * @param $cmd
	 */
	public function performCommand($cmd)
	{
		include_once './Customizing/global/plugins/Services/COPage/PageComponent/PCSageCell/classes/class.ilPCSageCellConfig.php';
		$config = new ilPCSageCellConfig();
		$this->setObject($config);
		switch ($cmd)
		{
			case "configure":
			case "save":
				$this->$cmd();
				break;

		}
	}

	/**
	 * Show configure screen
	 */
	function configure()
	{
		global $tpl;

		$this->initConfigurationForm();
		$tpl->setContent($this->getForm()->getHTML());
	}

	/**
	 * Set configuration form object
	 */
	public function initConfigurationForm()
	{
		global $ilCtrl, $lng;
		include_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
		$config_form = new ilPropertyFormGUI();

		//Sagemath server address field
		$sagemath_server_address = new ilTextInputGUI($this->getPluginObject()->txt("config_sagemath_server_address"), "sagemath_server_address");
		$sagemath_server_address->setValue($this->getObject()->getSagemathServerAddress());
		$sagemath_server_address->setInfo($this->getPluginObject()->txt("config_sagemath_server_address_info"));
		$config_form->addItem($sagemath_server_address);

		//Force_evaluate_button field
		$force_evaluate_button = new ilSelectInputGUI($this->getPluginObject()->txt("config_force_evaluate_button"), "force_evaluate_button");
		$force_evaluate_button->setOptions(array(TRUE => $lng->txt('yes'), FALSE => $lng->txt('no')));
		$force_evaluate_button->setValue((int)$this->getObject()->getForceEvaluateButton());
		$force_evaluate_button->setInfo($this->getPluginObject()->txt("config_force_evaluate_button_info"));
		$config_form->addItem($force_evaluate_button);

		//Form settings
		$config_form->addCommandButton("save", $lng->txt("save"));
		$config_form->setTitle($this->getPluginObject()->txt("config_title"));
		$config_form->setFormAction($ilCtrl->getFormAction($this));

		$this->setForm($config_form);
	}

	/**
	 * Save form data to DB
	 */
	public function save()
	{
		global $tpl, $ilCtrl;

		$this->initConfigurationForm();
		$this->getForm()->setValuesByPost();

		if ($this->getForm()->checkInput())
		{
			$this->getObject()->setSagemathServerAddress($this->getForm()->getItemByPostVar("sagemath_server_address")->getValue());
			$this->getObject()->setForceEvaluateButton($this->getForm()->getItemByPostVar("force_evaluate_button")->getValue());
			if ($this->getObject()->save())
			{
				ilUtil::sendSuccess($this->getPluginObject()->txt("config_info_correct_saved"),TRUE);
			} else
			{
				ilUtil::sendFailure($this->getPluginObject()->txt("config_info_error_saving"),TRUE);
			}
		}
		$tpl->setContent($this->getForm()->getHtml());
	}

	/*
	 * GETTERS AND SETTERS
	 */

	/**
	 * @return ilPCSageCellConfig
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @param ilPCSageCellConfig $object
	 */
	public function setObject($object)
	{
		$this->object = $object;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @param array $fields
	 */
	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	/**
	 * @return ilPropertyFormGUI
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * @param ilPropertyFormGUI $form
	 */
	public function setForm($form)
	{
		$this->form = $form;
	}


}
