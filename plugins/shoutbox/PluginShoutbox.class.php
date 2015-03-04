<?php

if (!class_exists('Plugin')) {
	die('Hacking attempt!');
}

class PluginShoutbox extends Plugin {

	public function Activate () {

		if (!$this -> isTableExists ('prefix_shout_blacklist')) {
			$this -> ExportSQL (dirname (__FILE__) . '/dump.sql');
		}

		return true;
		
	}

	public function Init() {
		$this->Viewer_AppendStyle(Plugin::GetTemplatePath(__CLASS__).'css/shoutbox.css');
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/jquery.tinyscrollbar.min.js');
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/jquery.ui.effects.min.js');
		$this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__).'js/shoutbox.js');
	}

}
?>