<?php

class PluginAdmintopic_ModuleTopic_EntityTopic extends PluginAdmintopic_Inherit_ModuleTopic_EntityTopic
{
    public function Init(){
    	parent::Init();
    }
	public function getNausea(){
		$rawText = strip_tags($this->getTextSource());
		$rawText = preg_replace('/&amp;/','&',$rawText);
		$rawText = preg_replace('/[\'"\.\,\:\;\@\#\$\%\^\&\*\(\)\[\]\{\}\~\+\=]/','',$rawText);
		$words = preg_split('/\s+/',$rawText);
		$hz = array();
		foreach($words as $word){
			if(array_key_exists($word,$hz)){
				$hz[$word] += 1;
			} else {
				$hz[$word] = 1;
			}
		}
		$max = 0;
		foreach($hz as $word => $cnt){			
			if($cnt > $max) {
				$max = $cnt;
			}
		}
		return array(
			'classic' => round(sqrt($max),4),
			'academic' => round(100*$max/count($words),4)
		);
	}
}

?>
