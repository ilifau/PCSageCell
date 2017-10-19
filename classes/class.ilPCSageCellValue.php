<?php
/**
 * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

/**
 * Page Component Sage Cell plugin
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @author Jesus Copado <jesus.copado@ili.fau.de>
 * @version $Id$
 */
class ilPCSageCellValue
{
	/**
	 * @var string code language type
	 */
	private $code_language;

	/**
	 * @var string code
	 */
	private $code;

	/**
	 * @var boolean enable evaluate button if not forced
	 */
	private $enable_evaluate_button;

	/**
	 * ilPCSageCellValue constructor.
	 * @param string $code_language
	 * @param string $code
	 * @param bool $enable_evaluate_button
	 */
	public function __construct($code_language, $code, $enable_evaluate_button = FALSE)
	{
		$this->setCodeLanguage($code_language);
		$this->setCode($code);
		$this->setEnableEvaluateButton($enable_evaluate_button);
	}



	/*
	 * GETTERS AND SETTERS
	 */

	/**
	 * @return string
	 */
	public function getCodeLanguage()
	{
		return $this->code_language;
	}

	/**
	 * @param string $code_language
	 */
	public function setCodeLanguage($code_language)
	{
		$this->code_language = $code_language;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * @return bool
	 */
	public function isEnableEvaluateButton()
	{
		return $this->enable_evaluate_button;
	}

	/**
	 * @param bool $enable_evaluate_button
	 */
	public function setEnableEvaluateButton($enable_evaluate_button)
	{
		$this->enable_evaluate_button = $enable_evaluate_button;
	}
}
