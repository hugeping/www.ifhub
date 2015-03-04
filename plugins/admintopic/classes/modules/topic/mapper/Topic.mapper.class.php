<?php
/* -------------------------------------------------------
 *
 *   LiveStreet (v1.0)
 *   Plugin Events for liveStreet 1.0.1
 *   Copyright © 2012 1099511627776@mail.ru
 *
 * --------------------------------------------------------
 *
 *   Contact e-mail: 1099511627776@mail.ru
 *
  ---------------------------------------------------------
*/

class PluginAdmintopic_ModuleTopic_MapperTopic extends PluginAdmintopic_Inherit_ModuleTopic_MapperTopic
{

	protected function buildFilter($aFilter){
		$sWhere = '';
		$sWhere = parent::buildFilter($aFilter);
		if(isset($aFilter['date_between'])){
			if(isset($aFilter['date_between']['from']) && isset($aFilter['date_between']['to'])) {
				$sWhere .= " AND t.topic_date_add BETWEEN '".$aFilter['date_between']['from']."' AND '".$aFilter['date_between']['to']."' ";
			} elseif(isset($aFilter['date_between']['from']) && !isset($aFilter['date_between']['to'])){
				$sWhere .= " AND t.topic_date_add >= '".$aFilter['date_between']['from']."' ";				
			} elseif(!isset($aFilter['date_between']['from']) && isset($aFilter['date_between']['to'])) {
				$sWhere .= " AND t.topic_date_add <= '".$aFilter['date_between']['from']."' ";				
			} else {
				$sWhere .= " ";
			}
		}
		dump($sWhere);
		return $sWhere;
	}

}
?>