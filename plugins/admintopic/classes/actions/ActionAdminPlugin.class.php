<?php
include(Plugin::GetPath(__CLASS__)."admintopic/includes/pr.php");

function sort_by_vals($a,$b){
	return $a < $b;
}

class PluginAdmintopic_ActionAdminPlugin extends ActionAdminPlugin {

    private function parsePage($params){
		$iPage = 1;
		if(count($params)!=0) {
			if(preg_match("/^page(\d+)$/i",$params[0],$match)){
				$iPage = intval($match[1]);
			}
		}
		return $iPage;    	
    }

    private function parseOrder(){
		if(!$sort = htmlspecialchars(getRequest('sort'))){
			$sort = Config::Get('plugin.admintopic.default_sort');
		};
		if($rev = getRequest('rev')){
			$rev = intval(getRequest('rev'));
		} else {
			$rev = Config::Get('plugin.admintopic.default_sort_direction');
		}
		$sRev = ($rev==0) ? 'DESC' : 'ASC';

	    return array(
	    	'raw' => array('sort'=>$sort, 'rev'=>$rev),
	    	'sql' => ' t.'.$sort.' '.$sRev,
	    );	    	
    }
    private function parseDate(){
    	$result = array();

    	if($from_date = getRequest('from_date')){    		
    		dump('fromdate not null');
    		if(preg_match("/\d\d\d\d-\d\d-\d\d/i",$from_date)){
	    		dump('fromdate match');
				$this->Session_Set('admintopic_fromdate',$from_date);   		
    		} else {
	    		dump('fromdate not match');
				$this->Session_Drop('admintopic_fromdate');   		
				$from_date = null;
    		}
    	} else {
    		dump('fromdate is null');
			$from_date = $this->Session_Get('admintopic_fromdate');   		
    	}

    	if($to_date = getRequest('to_date')){
    		dump('todate not null');
    		if(preg_match("/\d\d\d\d-\d\d-\d\d/i",$to_date)){
	    		dump('todate match');
				$this->Session_Set('admintopic_todate',$to_date);   		
    		} else {
	    		dump('todate not match');
				$this->Session_Drop('admintopic_todate');   		
				$to_date = null;
    		}
    	} else {
    		dump('todate isnull');
			$to_date = $this->Session_Get('admintopic_todate');   		
    	}

    	return array(
    		'from_date' => $from_date,
    		'to_date' => $to_date
    	);
    }

    protected function getPR($topic_id){
        $oTopic = $this->Topic_GetTopicById($topic_id);        
        $pr = new PR();
        return $pr->get_google_pagerank($oTopic->getUrl());
    }
    public function Admin(){
            $aFilter = array();
            if($topic_id = getRequest('topicid')){
                $this->Viewer_SetResponseAjax('json');
                $this->Viewer_AssignAjax('pr',$this->getPR($topic_id));
                return;
            }
            $params = $this->getParams();
	    $iPage = $this->parsePage($params);
	    $aOrder = $this->parseOrder();
	    $aFilter['order'] = $aOrder['sql'];
	    $aDateFilter = $this->parseDate();
	    if(isset($aDateFilter['from_date']) && isset($aDateFilter['to_date']) ){
	    	$aFilter['date_between'] = array(
	    		'from' => $aDateFilter['from_date'],
	    		'to' => $aDateFilter['to_date'],
	    	);
	    } elseif(isset($aDateFilter['from_date']) && !isset($aDateFilter['to_date'])){
	    	$aFilter['date_between'] = array(
	    		'from' => $aDateFilter['from_date'],
	    	);
	    } else {
	    	$aFilter['date_between'] = array(
	    		'to' => $aDateFilter['to_date'],
	    	);
	    }
            $aResult=$this->Topic_GetTopicsByFilter($aFilter,$iPage,Config::Get('plugin.admintopic.per_page'));
            $aPaging=$this->Viewer_MakePaging(
   		$aResult['count'],$iPage,
		Config::Get('plugin.admintopic.per_page'),
		Config::Get('pagination.pages.count'),
		Router::GetPath('admin').'plugins/admintopic',
		$aOrder['raw']
            );
            $this->Viewer_Assign('aPaging',$aPaging);
            $this->Viewer_Assign('aDateFilter',$aDateFilter);
            $this->Viewer_Assign('aAllTopics',$aResult['collection']);
            $this->SetTemplateInclude('topics');
	}

}
?>