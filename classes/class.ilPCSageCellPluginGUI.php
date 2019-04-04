<?php
/**
 * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

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
				if (in_array($cmd, array("create", "edit", "insert", "update", "preview")))
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
			$properties = array('sage_cell_input' => $form->getInput('sage_cell_input'), 'sage_cell_language' => $form->getInput('sage_cell_language'), 'sage_cell_code' => $sage_cell_code, 'sage_cell_auto_eval' => $form->getInput('sage_cell_auto_eval'), 'sage_cell_header_text' => $form->getInput('sage_cell_header_text'), 'sage_cell_footer_text' => $form->getInput('sage_cell_footer_text'), 'sage_cell_show_code_editor' => $form->getInput('sage_cell_show_code_editor'));

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
				$this->edit();
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
		$this->setTabs("edit");

		//Sage cell code, and textarea texts should be taken directly by post in order to avoid lose of code after < symbol.
		$sage_cell_code = $_POST["form_sage_cell_code_editor"];

		$form = $this->initForm(TRUE);
		if ($form->checkInput())
		{
			$properties = array('sage_cell_input' => $form->getInput('sage_cell_input'), 'sage_cell_language' => $form->getInput('sage_cell_language'), 'sage_cell_code' => $sage_cell_code, 'sage_cell_auto_eval' => $form->getInput('sage_cell_auto_eval'), 'sage_cell_header_text' => $form->getInput('sage_cell_header_text'), 'sage_cell_footer_text' => $form->getInput('sage_cell_footer_text'), 'sage_cell_show_code_editor' => $form->getInput('sage_cell_show_code_editor'));
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
		$this->setTabs("edit");

		$form = $this->initForm(FALSE);
		$tpl->setContent($form->getHTML());
	}

	/**
	 * preview
	 */
	public function preview(){
		global $tpl;
		$this->setTabs("preview");

		$tpl->setContent($this->getElementHTML("preview", $this->getProperties(), $this->getPlugin()));
	}

	/**
	 * @param $a_mode
	 * @param array $a_properties
	 * @param $a_plugin_version
	 * @return mixed
	 */
	public function getElementHTML($a_mode, array $a_properties, $a_plugin_version)
	{
		if ($a_mode == "edit")
		{
			return $this->getPageEditorHTML($a_properties);
		}
		else
		{
			return $this->getPageViewHTML($a_mode, $a_properties);
		}
		}


	/**
	 * Get the HTML code for page view
	 * @param $a_mode
	 * @param $a_properties
	 * @return string
	 */
	public function getPageViewHTML($a_mode, $a_properties)
	{
		if ($a_mode == "preview")
		{
			ilUtil::sendInfo($this->txt("info_debug_mode"));
		}

		require_once(__DIR__ . '/class.ilPCSageCellCache.php');
		$cache = new ilPCSageCellCache();
		$content = serialize($a_properties);
		$cache_id = md5($content);
		$cache->storeEntry($cache_id, $content);
		$cell_id = rand(1000, 9999);

		$url = $this->getPlugin()->getUrlPath() . 'content.php?mode='.$a_mode.'&cache_id='.$cache_id.'&cell_id='.$cell_id;

		/** @var ilTemplate $tpl */
		$tpl = $this->getPlugin()->getTemplate("tpl.page_view.html");

		$tpl->setVariable('HEADER_TEXT', $a_properties["sage_cell_header_text"]);
		$tpl->setVariable('URL', $url);
		$tpl->setVariable('CELL_ID', $cell_id);
		$tpl->setVariable('FOOTER_TEXT', $a_properties["sage_cell_footer_text"]);

		return $tpl->get();
	}


	/**
	 * Get the HTML code for the page editor
	 * @param $a_properties
	 * @return string
	 */
	public function getPageEditorHTML($a_properties)
	{
		/** @var ilTemplate $tpl */
		$tpl = $this->getPlugin()->getTemplate("tpl.page_editor.html");
		$tpl->setVariable('HEADER_TEXT', html_entity_decode($a_properties["sage_cell_header_text"]));
		$tpl->setVariable('CODE', $this->getPlugin()->prepareCodePageOutput($a_properties['sage_cell_code']));
		$tpl->setVariable('FOOTER_TEXT', html_entity_decode($a_properties["sage_cell_footer_text"]));

		return $tpl->get();
	}


	/**
	 * This function return the insert/edit form of a SageCell page component
	 * @param bool $a_create
	 * @return ilPropertyFormGUI
	 */
	public function initForm($a_create = false)
	{
		global $ilCtrl, $lng;

		$this->prepareForm();
		$prop = $this->getProperties();

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
		$sage_cell_show_code_editor = new ilRadioGroupInputGUI($this->txt("form_sage_cell_show_code_editor"), "sage_cell_show_code_editor");
		$option_edit = new ilRadioOption($this->txt("form_sage_cell_show_code_edit"), 'edit', $this->txt("form_sage_cell_show_code_edit_info"));
		$sage_cell_show_code_editor->addOption($option_edit);
		$option_show = new ilRadioOption($this->txt("form_sage_cell_show_code_readonly"), 'show', $this->txt("form_sage_cell_show_code_readonly_info"));
		$sage_cell_show_code_editor->addOption($option_show);
		$option_hide = new ilRadioOption($this->txt("form_sage_cell_show_code_hide"), 'hide', $this->txt("form_sage_cell_show_code_hide_info"));
		$sage_cell_show_code_editor->addOption($option_hide);
		$sage_cell_show_code_editor->setValue($prop['sage_cell_show_code_editor']);

		$form->addItem($sage_cell_show_code_editor);

		//Activate Auto Evaluation (Deactivate if evaluate button is forced in admin)
		$this->plugin->includeClass('class.ilPCSageCellConfig.php');
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
		$tpl->addCss($this->getPlugin()->getUrlPath() . 'js/codemirror/lib/codemirror.css' . $this->getPlugin()->getUrlSuffix());
		$tpl->addCss($this->getPlugin()->getUrlPath() . 'js/codemirror/theme/solarized.css' . $this->getPlugin()->getUrlSuffix());
		$tpl->addJavascript($this->getPlugin()->getUrlPath() . 'js/codemirror/lib/codemirror.js');
		$tpl->addJavascript($this->getPlugin()->getUrlPath() . 'js/codemirror/mode/' . $lngData['cmLanguage'] . '/' . $lngData['cmLanguage'] . '.js');
		$tpl->addJavascript($this->getPlugin()->getUrlPath() . 'js/helper.js');

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
		$tpl->setVariable("CONTENT", $this->getPlugin()->prepareCodeFormOutput($value));
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
	 * Set tabs
	 *
	 * @param
	 * @return
	 */
	public function setTabs($a_active)
	{
		global $ilTabs, $ilCtrl, $lng;

		$ilTabs->addTab("edit", $lng->txt("edit"), $ilCtrl->getLinkTarget($this, "edit"));

		$ilTabs->addTab("preview", $lng->txt("preview"), $ilCtrl->getLinkTarget($this, "preview"));

		$ilTabs->activateTab($a_active);
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