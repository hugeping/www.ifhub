<?php
/**
 * File Archive - тип топика "файл"
 *
 * Версия:	1.0.3
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_filearchive
 *
 **/

class PluginFilearchive_HookFilearchive extends Hook {
    public function RegisterHook() {
        $this->AddHook('topic_delete_after', 'DeleteFile');
        $this->AddHook('template_menu_create_topic_item', 'InjectAddLink');
        if (Config::Get('plugin.filearchive.show_info')) {
            $this->AddHook('template_topic_show_info', 'InjectShowInfo');
        }
        if (Config::Get('plugin.filearchive.show_write_item')) {
            $this->AddHook('template_write_item', 'InjectWriteItem');
        }
        if (class_exists('PluginMainpreview') || class_exists('PluginNiceurl') || class_exists('PluginSkdatedit')) {
            $plugins = $this->Plugin_GetActivePlugins();
            if (in_array('skdatedit', $plugins)) {
                $this->AddHook('template_form_add_topic_file_end', 'form_insert', 'PluginSkdatedit_HookSkdatedit');
            }
            if (in_array('mainpreview', $plugins)) {
                $this->AddHook('template_form_add_topic_file_end', 'AddTopicPreviewForm', 'PluginMainpreview_HookMain');
            }
            if (in_array('niceurl', $plugins)) {
                $this->AddHook('template_form_add_topic_file_begin', 'AddToForm', 'PluginNiceurl_HookUrl');
            }
        }
    }
    public function DeleteFile($aVar) {
        if (isset($aVar) && isset($aVar['oTopic']) && $aVar['oTopic']->isFile()) {
            $sFile = $aVar['oTopic']->getFilePathFull();
            @unlink($sFile);
        }
    }

    public function InjectAddLink() {
        $sTemplatePath = Plugin::GetTemplatePath(__CLASS__) . 'inject_add_link.tpl';
        if ($this->Viewer_TemplateExists($sTemplatePath)) {
            return $this->Viewer_Fetch($sTemplatePath);
        }
    }

    public function InjectShowInfo($aVar) {
        $sTemplatePath = Plugin::GetTemplatePath(__CLASS__) . 'inject_show_info.tpl';
        if ($this->Viewer_TemplateExists($sTemplatePath)) {
            if (isset($aVar) && isset($aVar['topic']) && $aVar['topic']->isFile()) {
                $oTopic = $aVar['topic'];
                $this->Viewer_Assign('oTopic', $oTopic);
                return $this->Viewer_Fetch($sTemplatePath);
            }
        }
    }

    public function InjectWriteItem() {
        $sTemplatePath = Plugin::GetTemplatePath(__CLASS__) . 'inject_write_item.tpl';
        if ($this->Viewer_TemplateExists($sTemplatePath)) {
            return $this->Viewer_Fetch($sTemplatePath);
        }
    }
}
?>