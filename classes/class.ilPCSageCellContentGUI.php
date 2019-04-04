<?php
/**
 * Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

/**
 * GUI class for showing the contents in an iframe
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 */
class ilPCSageCellContentGUI
{
	/** @var ilPCSageCellPlugin */
	protected $plugin;

	/** @var ilPCSageCellConfig */
	protected $config;

	/** @var ilPCSageCellCache */
	protected  $cache;

	/** @var string  display mode */
	protected $mode;

	/** @var string  */
	protected $cache_id;

	/** @var integer */
	protected $cell_id;

	/** @var array Properties of the cell */
	protected $properties;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		require_once(__DIR__ . "/class.ilPCSageCellPlugin.php");
		require_once(__DIR__ . '/class.ilPCSageCellConfig.php');
		require_once(__DIR__ . '/class.ilPCSageCellCache.php');

		$this->plugin = new ilPCSageCellPlugin();
		$this->config = new ilPCSageCellConfig();
		$this->cache = new ilPCSageCellCache();

		$this->mode = $_GET['mode'];
		$this->cache_id = $_GET['cache_id'];
		$this->cell_id = (int) $_GET['cell_id'];

		$this->properties = unserialize($this->cache->getEntry($this->cache_id));
	}

	/**
	 * Show the page
	 */
	public function show()
	{
		/** @var ilTemplate $tpl */
		$tpl = $this->plugin->getTemplate("tpl.iframe.html");

		//Add SageCell javascript files to page
		$tpl->setVariable('SERVER_URL', $this->config->getSagemathServerAddress());

		//Add SageCell css files to page (path is relative to content.php)
		$tpl->setVariable('CSS_URL', 'templates/css/sagecell_embed.css' . $this->plugin->getUrlSuffix());

		// the cell_id is used to adjust the parent iframe
		$tpl->setVariable('CELL_ID', $this->cell_id);

		// Code
		$tpl->setVariable('CODE', $this->plugin->prepareCodePageOutput($this->properties['sage_cell_code']));

		// Available languages
		$tpl->setVariable('LANGUAGES', $this->properties["sage_cell_language"]);

		//Check evaluation button is not forced and autoevaluation is activated
		if ($this->properties['sage_cell_auto_eval'] && !$this->config->getForceEvaluateButton())
		{
			$tpl->setVariable('AUTOEVAL', 'true');
		} else
		{
			$tpl->setVariable('AUTOEVAL', 'false');
		}

		// Caption of evaluation button
		$tpl->setVariable('EVAL_BUTTON_TEXT', $this->plugin->txt("sage_cell_evaluate"));

		switch ($this->properties['sage_cell_show_code_editor'])
		{
			case 'edit':
				$tpl->setVariable('TEMPLATE', 'sagecell.templates.minimal');
				$tpl->setVariable('EDITOR_TYPE', 'codemirror');
				$tpl->setVariable('HIDE', '"language", "permalink", "fullScreen", "sessionFiles", "done"');
				break;
			case 'show':
				$tpl->setVariable('TEMPLATE', 'sagecell.templates.restricted');
				$tpl->setVariable('EDITOR_TYPE', 'codemirror-readonly');
				$tpl->setVariable('HIDE', '"language", "permalink", "fullScreen", "sessionFiles", "done"');
				break;
			case 'hide':
				$tpl->setVariable('TEMPLATE', 'sagecell.templates.restricted');
				$tpl->setVariable('EDITOR_TYPE', 'codemirror');
				$tpl->setVariable('HIDE', '"language", "permalink", "fullScreen", "sessionFiles", "done", "editor"');
				break;
			default:
				$tpl->setVariable('TEMPLATE', 'sagecell.templates.restricted');
				$tpl->setVariable('HIDE', '"language", "permalink", "fullScreen", "sessionFiles", "done", "editor"');
				break;
		}

		if ($this->mode == "preview")
		{
			$tpl->setVariable('DEBUG_MODE', ',mode: "debug"');
		}else{
			$tpl->setVariable('DEBUG_MODE', '');
		}


		// show the page
		echo $tpl->get();
	}
}