<?php

class PluginRetroavatar_ModuleUser extends PluginRetroavatar_Inherit_ModuleUser
{

	public function Init()
	{
		parent::Init();
	}

    public function UploadRetroAvatar($sWebPath, $oUser)
    {
        /**
         * Проверяем, является ли файл изображением
         */
        if(!@getimagesize($sWebPath)) {
            return ModuleImage::UPLOAD_IMAGE_ERROR_TYPE;
        }
        /**
         * Открываем файловый поток и считываем файл поблочно,
         * контролируя максимальный размер изображения
         */
        $oFile=fopen($sWebPath,'r');
        if(!$oFile) {
            return ModuleImage::UPLOAD_IMAGE_ERROR_READ;
        }

        $iMaxSizeKb=Config::Get('view.img_max_size_url');
        $iSizeKb=0;
        $sContent='';
        while (!feof($oFile) and $iSizeKb<$iMaxSizeKb) {
            $sContent.=fread($oFile ,1024*1);
            $iSizeKb++;
        }
        /**
         * Если конец файла не достигнут,
         * значит файл имеет недопустимый размер
         */
        if(!feof($oFile)) {
            return ModuleImage::UPLOAD_IMAGE_ERROR_SIZE;
        }
        fclose($oFile);
        /**
         * Создаем tmp-файл, для временного хранения изображения
         */
        $sFileTmp=Config::Get('sys.cache.dir').func_generator();

        $fp=fopen($sFileTmp,'w');
        fwrite($fp,$sContent);
        fclose($fp);

        $sFileWeb =  $this->UploadAvatar($sFileTmp, $oUser);
        $oUser->setProfileAvatar($sFileWeb);
        $this->Update($oUser);
        return $sFileWeb;
    }
}

?>
