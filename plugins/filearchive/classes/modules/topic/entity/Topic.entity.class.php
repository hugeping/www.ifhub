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

class PluginFilearchive_ModuleTopic_EntityTopic extends PluginFilearchive_Inherit_ModuleTopic_EntityTopic {

    /**
     * Определяем правила валидации
     */
    public function Init() {
        parent::Init();
        $this->aValidateRules[]=array('topic_title','string','max'=>200,'min'=>2,'allowEmpty'=>false,'label'=>$this->Lang_Get('topic_create_title'),'on'=>array('file'));
        $this->aValidateRules[]=array('topic_text_source','string','max'=>Config::Get('plugin.filearchive.text_max_length'),'min'=>10,'allowEmpty'=>false,'label'=>$this->Lang_Get('topic_create_text'),'on'=>array('file'));
        $this->aValidateRules[]=array('topic_tags','tags','count'=>15,'label'=>$this->Lang_Get('topic_create_tags'),'allowEmpty'=>Config::Get('module.topic.allow_empty_tags'),'on'=>array('file'));
        $this->aValidateRules[]=array('blog_id','blog_id','on'=>array('file'));
        $this->aValidateRules[]=array('topic_text_source','topic_unique','on'=>array('file'));
        $this->aValidateRules[]=array('topic_type','topic_type','on'=>array('file'));
    }

    /**
     * Возвращает URL для файла
     *
     * @return null|string
     */
    public function getFileUrl() {
        if (!$this->isFile()) {
            return null;
        }
        $sPath = $this->getFilePath();
        return Config::Get('path.root.web') . ($sPath ? $sPath : '');
    }

    /**
     * Возвращает URL для скачивания
     *
     * @return null|string
     */
    public function getDownloadUrl() {
        return Config::Get('path.root.web') . '/file/go/' . $this->getId() . '/';
    }

    /**
     * Возвращает путь к файлу
     *
     * @return null|string
     */
    public function getFilePath() {
        if (!$this->isFile()) {
            return null;
        }
        return $this->getExtraValue('file_path');
    }

    /**
     * Возвращает полный путь к файлу
     *
     * @return null|string
     */
    public function getFilePathFull() {
        if (!$this->isFile()) {
            return null;
        }
        return Config::Get('path.root.server') . DIRECTORY_SEPARATOR . $this->getFilePath();
    }

    /**
     * Устанавливает путь к файлу
     *
     * @param string $data
     */
    public function setFilePath($data) {
        if (!$this->isFile()) {
            return;
        }
        $this->setExtraValue('file_path',$data);
    }

    /**
     * Возвращает оригинальное имя файла
     *
     * @return null|string
     */
    public function getFileName() {
        if (!$this->isFile()) {
            return null;
        }
        return $this->getExtraValue('file_original_name');
    }

    /**
     * Устанавливает оригинальное имя файла
     *
     * @param string $data
     */
    public function setFileName($data) {
        if (!$this->isFile()) {
            return null;
        }
        $this->setExtraValue('file_original_name', $data);
    }

    /**
     * Возвращает количество скачиваний файла
     *
     * @return int|null
     */
    public function getFileDownloads() {
        if (!$this->isFile()) {
            return null;
        }
        return (int)$this->getExtraValue('file_downloads');
    }

    /**
     * Устанавливает количество скачиваний файла
     *
     * @param int $data
     */
    public function setFileDownloads($data) {
        if (!$this->isFile()) {
            return;
        }
        $this->setExtraValue('file_downloads',$data);
    }

    /**
     * Возвращает размер файла
     *
     * @return int|null
     */
    public function getFileSize() {
        if (!$this->isFile()) {
            return null;
        }
        $iSize = $this->getExtraValue('file_size');
        if (!$iSize) {
            $sFilename = $this->getFilePathFull();
            if (file_exists($sFilename)) {
                $iSize = @filesize($sFilename);
            }
        }
        return (int)$iSize;
    }

    /**
     * Устанавливает размер файла
     *
     * @param int $data
     */
    public function setFileSize($data) {
        if (!$this->isFile()) {
            return;
        }
        $this->setExtraValue('file_size',$data);
    }

    /**
     * Проверяет, является ли топик файлом
     *
     * @return bool
     */
    public function isFile() {
        return $this->getType()=='file';
    }

}
?>