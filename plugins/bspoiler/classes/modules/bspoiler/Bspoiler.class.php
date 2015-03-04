<?php

/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
*---------------------------------------------------------
*
*	Plugin Spoiler
*	Shpinev Konstantin
*	Contact e-mail: thedoublekey@gmail.com
*
*---------------------------------------------------------
*
*	Spoiler :: Plugin
*	Modified by fedorov mich © 2014
*	[ LS :: 1.0.3 | Habra Style ]
*
*/

class PluginBspoiler_ModuleBspoiler extends PluginBspoiler_Inherit_ModuleText
{
	protected function JevixConfig()
	{
		parent::JevixConfig();
		
		$aTags = array_keys($this->oJevix->tagsRules);
		$aTags[] = 'spoiler';
		$this->oJevix->cfgAllowTags($aTags);
		$this->oJevix->cfgAllowTagParams('spoiler', array('title'));
	}
	
	private function SpoilerParser($sText)
	{
		$aMatches = array();
		while (preg_match('/<spoiler title="(.+?)">/', $sText, $aMatches) !== false && count($aMatches) > 1) {
			$sTitle = $aMatches[1];
			$sText = str_replace("<spoiler title=\"$sTitle\">",
								 '<div><b class="spoiler-title">'.$sTitle.'</b><div class="spoiler-body">',
								 $sText);
			$sText = str_replace("</spoiler>", '</div></div>', $sText);
		}
		return $sText;
	}

	public function Parser($sText)
	{
		$sResult = parent::Parser($sText);
		$sResult = $this->SpoilerParser($sResult);
		return $sResult;
	}
}

?>