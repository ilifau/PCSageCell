<?php
/**
 * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

include_once("./Services/COPage/classes/class.ilPageComponentPluginGUI.php");

/**
 * Page Component Sage Cell  plugin GUI
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @author Jesus Copado <jesus.copado@ili.fau.de>
 * @version $Id$
 *
 * @ilCtrl_isCalledBy ilPCSageCellPluginGUI: ilPCPluggedGUI
 * @ilCtrl_Calls ilPCSageCellPluginGUI: ilPropertyFormGUI
 *
 */
class ilPCSageCellPluginGUI extends ilPageComponentPluginGUI
{

	/**
	 * @const    string    URL base path for including special javascript and css files
	 */
	const URL_PATH = "./Customizing/global/plugins/Services/COPage/PageComponent/PCSageCell/";

	/**
	 * @const    string    URL suffix to prevent caching of css files (increase with every change)
	 *                    Note: this does not yet work with $tpl->addJavascript()
	 */
	const URL_SUFFIX = "?css_version=1.5.9";

	/**
	 * @var
	 */
	private $value;


	/**
	 * Execute command
	 *
	 */
	public function executeCommand()
	{
		global $ilCtrl;

		$next_class = $ilCtrl->getNextClass();

		switch ($next_class)
		{
			default:
				// perform valid commands
				$cmd = $ilCtrl->getCmd();
				//TODO
				if (in_array($cmd, array("create", "edit", "insert", "update")))
				{
					$this->$cmd();
				}
				break;
		}
	}

	/**
	 * insert
	 */
	public function insert()
	{
		global $tpl;

		$form = $this->initForm(TRUE);
		$tpl->setContent($form->getHTML());
	}

	/**
	 * Update
	 *
	 * @param
	 * @return
	 */
	public function update()
	{
		global $tpl, $lng, $ilCtrl;

		$form = $this->initForm(FALSE);

		//Sage cell code, and textarea texts should be taken directly by post in order to avoid lose of code after < symbol.
		$sage_cell_code = $_POST["form_sage_cell_code_editor"];

		if ($form->checkInput())
		{
			$existing_properties = $this->getProperties();
			$properties = array(
				'sage_cell_input' => $form->getInput('sage_cell_input'),
				'sage_cell_language' => $form->getInput('sage_cell_language'),
				'sage_cell_code' => $sage_cell_code,
				'sage_cell_auto_eval' => $form->getInput('sage_cell_auto_eval'),
				'sage_cell_header_text' => $form->getInput('sage_cell_header_text'),
				'sage_cell_footer_text' => $form->getInput('sage_cell_footer_text'),
				'sage_cell_show_code_editor' => $form->getInput('sage_cell_show_code_editor')
			);

			foreach ($existing_properties as $property_name => $value)
			{
				if (key_exists($property_name, $properties))
				{
					$existing_properties[$property_name] = $properties[$property_name];
				}
			}
			if ($this->updateElement($existing_properties))
			{
				ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
				$this->returnToParent();
			}
		}
		$form->setValuesByPost();
		$tpl->setContent($form->getHtml());
	}

	/**
	 * create
	 */
	public function create()
	{
		global $tpl, $lng, $ilCtrl;

		//Sage cell code, and textarea texts should be taken directly by post in order to avoid lose of code after < symbol.
		$sage_cell_code = $_POST["form_sage_cell_code_editor"];

		$form = $this->initForm(TRUE);
		if ($form->checkInput())
		{
			$properties = array(
				'sage_cell_input' => $form->getInput('sage_cell_input'),
				'sage_cell_language' => $form->getInput('sage_cell_language'),
				'sage_cell_code' => $sage_cell_code,
				'sage_cell_auto_eval' => $form->getInput('sage_cell_auto_eval'),
				'sage_cell_header_text' => $form->getInput('sage_cell_header_text'),
				'sage_cell_footer_text' => $form->getInput('sage_cell_footer_text'),
				'sage_cell_show_code_editor' => $form->getInput('sage_cell_show_code_editor')
			);
			if ($this->createElement($properties))
			{
				ilUtil::sendSuccess($lng->txt("msg_obj_modified"), TRUE);
				$this->returnToParent();
			}
		}
		$form->setValuesByPost();
		$tpl->setContent($form->getHtml());
	}

	/**
	 * edit
	 */
	public function edit()
	{
		global $tpl;

		$form = $this->initForm(FALSE);
		$tpl->setContent($form->getHTML());
	}

	/**
	 * @param $a_mode
	 * @param array $a_properties
	 * @param $a_plugin_version
	 * @return mixed
	 */
	public function getElementHTML($a_mode, array $a_properties, $a_plugin_version)
	{
		global $tpl;

		if ($a_mode == "edit")
		{
			return $this->getPageEditorHTML($a_properties);
		}

		include_once './Customizing/global/plugins/Services/COPage/PageComponent/PCSageCell/classes/class.ilPCSageCellConfig.php';
		$config = new ilPCSageCellConfig();

		//Get random ID for the current SageCell
		$sage_cell_id = rand(0, 9999999);

		//Fill content template
		$content_template = $this->getPlugin()->getTemplate("tpl.content.html");
		$content_template->setVariable('ID', $sage_cell_id);
		$content_template->setVariable('TEMPLATE', 'sagecell.templates.minimal');
		$content_template->setVariable('INPUT_LOCATION', 'div.compute');
		$content_template->setVariable('OUTPUT_LOCATION', 'div.output');
		$content_template->setVariable('CODE_LOCATION', '#codeinput');
		$content_template->setVariable('LANGUAGES', $a_properties["sage_cell_language"]);

		//Check evaluation button is not forced and autoevaluation is activated
		if ($a_properties['sage_cell_auto_eval'] AND !$config->getForceEvaluateButton())
		{
			//TODO Im not sure we should deactivate evalButton when autoevaluation is active because after modify the code its not possible to re evaluate
			$content_template->setVariable('AUTOEVAL', 'true');
			$content_template->setVariable('EVAL_BUTTON_TEXT', $this->txt("sage_cell_evaluate"));
			if ((int)$a_properties['sage_cell_show_code_editor'])
			{
				$content_template->setVariable('HIDE', '"language", "permalink", "evalButton", "fullScreen", "sessionFiles", "done"');
			} else
			{
				$content_template->setVariable('HIDE', '"language", "permalink", "evalButton", "fullScreen", "sessionFiles", "done", "editor"');
			}
		} else
		{
			$content_template->setVariable('AUTOEVAL', 'false');
			$content_template->setVariable('EVAL_BUTTON_TEXT', $this->txt("sage_cell_evaluate"));
			if ((int)$a_properties['sage_cell_show_code_editor'])
			{
				$content_template->setVariable('HIDE', '"language", "permalink", "fullScreen", "sessionFiles", "done"');
			} else
			{
				$content_template->setVariable('HIDE', '"language", "permalink", "fullScreen", "sessionFiles", "done", "editor"');
			}
		}

		//Include extra info text
		$content_template->setVariable('SAGE_TEXT', $a_properties["sage_cell_header_text"]);

		//We have to replace carriage return ascii &#13 with \r in order to get a proper display of the code
		$content_template->setVariable('CODE', str_replace('&#13;', "\r", $a_properties['sage_cell_code']));

		//Include extra info text
		$content_template->setVariable('FOOTER_TEXT', $a_properties["sage_cell_footer_text"]);

		//Add SageCell css files to page
		$tpl->addCss(self::URL_PATH . 'templates/css/sagecell_embed.css' . self::URL_SUFFIX);

		//Add SageCell javascript files to page
		$tpl->addJavaScript($config->getSagemathServerAddress());

		return $content_template->get();
	}


	public function getPageEditorHTML($a_properties)
	{
		/** @var ilTemplate $content_template */
		$content_template = $this->getPlugin()->getTemplate("tpl.page_editor.html");
		$content_template->setVariable('SAGE_TEXT', html_entity_decode($a_properties["sage_cell_header_text"]));
		$content_template->setVariable('CODE', str_replace('&#13;', "\r", $a_properties['sage_cell_code']));
		$content_template->setVariable('FOOTER_TEXT', html_entity_decode($a_properties["sage_cell_footer_text"]));
		return $content_template->get();
	}


	/**
	 * This function return the insert/edit form of a SageCell page component
	 * @param bool $a_create
	 * @return ilPropertyFormGUI
	 */
	public function initForm($a_create = false)
	{
		global $ilCtrl, $lng, $tpl;

		$this->prepareForm();
		$prop = $this->getProperties();

		include_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();

		//SageCell input
		$sage_cell_input = new ilTextInputGUI($this->txt('form_sage_cell_name'), 'sage_cell_input');
		$sage_cell_input->setMaxLength(40);
		$sage_cell_input->setSize(40);
		$sage_cell_input->setRequired(true);
		$sage_cell_input->setInfo($this->txt("form_sage_cell_name_info"));
		$sage_cell_input->setValue($prop['sage_cell_input']);
		$form->addItem($sage_cell_input);

		//SageCell code language
		$sage_cell_code_language = new ilSelectInputGUI($this->txt("form_code_language"), "sage_cell_language");
		$sage_cell_code_language->setOptions(array("sage" => "Sage", "gap" => "Gap", "gp" => "GP", "html" => "HTML", "maxima" => "Maxima", "octave" => "Octave", "python" => "Python", "r" => "R", "singular" => "Singular"));
		$sage_cell_code_language->setInfo($this->txt("form_code_language_info"));
		$sage_cell_code_language->setValue($prop['sage_cell_language']);
		$form->addItem($sage_cell_code_language);

		//Extra info textarea
		$sage_cell_extra_info_textarea = new ilTextAreaInputGUI($this->txt('form_sage_cell_header_text'), 'sage_cell_header_text');
		$sage_cell_extra_info_textarea->setInfo($this->txt("form_sage_cell_header_text_info"));
		$sage_cell_extra_info_textarea->setUseRte(1);
		$sage_cell_extra_info_textarea->setRteTagSet('extended');
		$sage_cell_extra_info_textarea->setValue($prop['sage_cell_header_text']);
		$form->addItem($sage_cell_extra_info_textarea);

		//sagecell code script
		$this->createCodeEditorFormInput($form, 'form_sage_cell_code_editor', $prop['sage_cell_code']);

		//Footer text textarea
		$sage_cell_footer_textarea = new ilTextAreaInputGUI($this->txt('form_sage_cell_footer_text'), 'sage_cell_footer_text');
		$sage_cell_footer_textarea->setInfo($this->txt("form_sage_cell_footer_text_info"));
		$sage_cell_footer_textarea->setUseRte(1);
		$sage_cell_footer_textarea->setRteTagSet('extended');
		$sage_cell_footer_textarea->setValue($prop['sage_cell_footer_text']);
		$form->addItem($sage_cell_footer_textarea);

		//Show code editor
		$sage_cell_show_code_editor = new ilSelectInputGUI($this->txt("form_sage_cell_show_code_editor"), "sage_cell_show_code_editor");
		$sage_cell_show_code_editor->setOptions(array('1' => $lng->txt('yes'), '0' => $lng->txt('no')));
		$sage_cell_show_code_editor->setInfo($this->txt("form_sage_cell_show_code_editor_info"));
		if ((int)$prop['sage_cell_show_code_editor'])
		{
			$sage_cell_show_code_editor->setValue('1');
		} else
		{
			$sage_cell_show_code_editor->setValue('0');
		}
		$form->addItem($sage_cell_show_code_editor);

		//Activate Auto Evaluation (Deactivate if evaluate button is forced in admin)
		include_once './Customizing/global/plugins/Services/COPage/PageComponent/PCSageCell/classes/class.ilPCSageCellConfig.php';
		$config = new ilPCSageCellConfig();
		$sage_cell_auto_eval = new ilSelectInputGUI($this->txt("form_auto_eval_button"), "sage_cell_auto_eval");
		$sage_cell_auto_eval->setOptions(array('1' => $lng->txt('yes'), '0' => $lng->txt('no')));
		$sage_cell_auto_eval->setInfo($this->txt("form_auto_eval_button_info"));
		if ((int)$prop['sage_cell_auto_eval'])
		{
			$sage_cell_auto_eval->setValue('1');
		} else
		{
			$sage_cell_auto_eval->setValue('0');
		}

		if ($config->getForceEvaluateButton())
		{
			$sage_cell_auto_eval->setDisabled(TRUE);
			$sage_cell_auto_eval->setValue(FALSE);
		}
		$form->addItem($sage_cell_auto_eval);

		// save and cancel commands
		if ($a_create)
		{
			$this->addCreationButton($form);
			$form->addCommandButton("cancel", $lng->txt("cancel"));
			$form->setTitle($this->txt("form_create_sage_cell"));
		} else
		{
			$form->addCommandButton("update", $lng->txt("save"));
			$form->addCommandButton("cancel", $lng->txt("cancel"));
			$form->setTitle($this->txt("form_edit_sage_cell"));
		}

		$form->setFormAction($ilCtrl->getFormAction($this));

		return $form;
	}

	/**
	 * This functions add codemirror files to the global template in order to show the code textarea
	 * as a Code input.
	 */
	public function prepareForm()
	{
		global $tpl;

		$lngData = $this->getLanguageData();
		$tpl->addCss(self::URL_PATH . 'js/codemirror/lib/codemirror.css' . self::URL_SUFFIX);
		$tpl->addCss(self::URL_PATH . 'js/codemirror/theme/solarized.css' . self::URL_SUFFIX);
		$tpl->addJavascript(self::URL_PATH . 'js/codemirror/lib/codemirror.js');
		$tpl->addJavascript(self::URL_PATH . 'js/codemirror/mode/' . $lngData['cmLanguage'] . '/' . $lngData['cmLanguage'] . '.js');
		$tpl->addJavascript(self::URL_PATH . 'js/helper.js');

		$tpl->addOnLoadCode('initSolutionBox("' . $lngData['cmMode'] . '");');
		$tpl->addOnLoadCode("hljs.configure({useBR: false});$('pre[class=" . $lngData['hljsLanguage'] . "][usebr=no]').each(function(i, block) { hljs.highlightBlock(block);});");
		$tpl->addOnLoadCode("hljs.configure({useBR: true});$('pre[class=" . $lngData['hljsLanguage'] . "][usebr=yes]').each(function(i, block) { hljs.highlightBlock(block);});");
	}

	/**
	 * Creates a code textarea and add it to the given ilPropertyFormGUI
	 * @param ilPropertyFormGUI $form
	 * @param string $name
	 * @param string $value
	 */
	public function createCodeEditorFormInput(\ilPropertyFormGUI $form, $name, $value)
	{
		$item = new ilCustomInputGUI($this->plugin->txt($name), $name);
		$item->setInfo($this->txt('form_code_editor_info'));
		$tpl = $this->plugin->getTemplate('tpl.code_editor.html');
		$tpl->setVariable("CONTENT", $value);
		$tpl->setVariable("NAME", $name);
		$item->setHTML($tpl->get());
		$form->addItem($item);
	}

	private function getLanguageData()
	{
		$language = "python";
		$hljslanguage = $language;
		$mode = $language;

		if ($language == "java")
		{
			$language = "clike";
			$mode = "text/x-java";
		} else
		{
			if ($language == "c++")
			{
				$language = "clike";
				$mode = "text/x-c++src";
			} else
			{
				if ($language == "c")
				{
					$language = "clike";
					$mode = "text/x-csrc";
				} else
				{
					if ($language == "objectivec")
					{
						$language = "clike";
						$mode = "text/x-objectivec";
					}
				}
			}
		}

		return array('cmLanguage' => $language, 'cmMode' => $mode, 'hljsLanguage' => $hljslanguage);
	}

	/**
	 * Get a plugin text
	 * @param $a_var
	 * @return mixed
	 */
	protected function txt($a_var)
	{
		return $this->getPlugin()->txt($a_var);
	}
}