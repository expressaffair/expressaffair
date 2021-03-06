<?php
if(!empty($eventids) && count($eventids)>0){

//Check if visibility configured already	
	$missingevents=$this->internalDB->queryFirstColumn("SELECT event_id FROM  event_visibility  WHERE  community_id =$communityId AND  event_id NOT IN (".implode(',',$eventids).") GROUP BY event_id");

	$updateObject=array();	
	foreach($eventids as $eventid){
		/*state releted changes */
		if(!empty($entity['selectedstate']) && count($entity['selectedstate'])>0)
		{
			$existingstate=$this->internalDB->queryFirstColumn("SELECT state_id from  event_visibility WHERE  event_id=$eventid "
				." AND community_id= $communityId  AND state_id in (".implode(',',$entity['selectedstate']).")");
			$existingstate=empty($existingstate)?array():$existingstate;
			if(count($existingstate)>0)
			{
				/* delete uselected state */		
				$deletedstate = array_diff($existingstate, $entity['selectedstate']);
				if(!empty($deletedstate) && count($deletedstate)>0)
				{
					$this->internalDB->query("DELETE FROM event_visibility WHERE  event_id=$eventid " 
						." AND community_id= $communityId  AND state_id in (".implode(',',$deletedstate).")");
				}
			}
			/* state Ids to add */
			$statestoadd = array_diff($entity['selectedstate'],$existingstate);
			if(!empty($statestoadd) && count($statestoadd)>0)
			{
				foreach($statestoadd as $state){
					$updateObject[]=array(
						"event_id"=>$eventid,
						"state_id"=>$state,
						"zone_id"=>null,
						"location_id"=>null,
						"community_id"=>$communityId
						);
				}
			}
		}
		else{
			$this->internalDB->query("DELETE FROM event_visibility WHERE  event_id=$eventid  "
				." AND community_id= $communityId AND state_id IS NOT NULL");
		}

		/*zone releted changes */
		if(!empty($entity['selectedzone']) && count($entity['selectedzone'])>0)
		{
			$existingzone=$this->internalDB->queryFirstColumn("SELECT zone_id FROM event_visibility WHERE  event_id=".$eventid
				." AND community_id= $communityId  AND zone_id in (".implode(',',$entity['selectedzone']).")");
			$existingzone=empty($existingzone)?array():$existingzone;
			if(count($existingzone)>0)
			{
				/* delete uselected zone */		
				$deletedzone = array_diff($existingzone, $entity['selectedzone']);
				if(!empty($deletedzone) && count($deletedzone)>0)
				{
					$this->internalDB->query("DELETE FROM event_visibility WHERE  event_id=$eventid " 
						." AND community_id= $communityId  AND zone_id  in (".implode(',',$deletedzone).")");
				}
			}
			/* Zone Ids to add */
			$zonetoadd = array_diff($entity['selectedzone'],$existingzone);
			if(!empty($zonetoadd) && count($zonetoadd)>0)
			{
				foreach($zonetoadd as $zone){
					$updateObject[]=array(
						"event_id"=>$eventid,
						"state_id"=>null,
						"zone_id"=>$zone,
						"location_id"=>null,
						"community_id"=>$communityId
						);
				}
			}
		}
		else{
			$this->internalDB->query("DELETE FROM event_visibility WHERE  event_id=$eventid  "
				." AND community_id= $communityId AND zone_id IS NOT NULL");
		}
		
		/*Location releted changes */
		$locationIds= !empty($entity['selectedlocations'])?$entity['selectedlocations']:array();
		if(!empty($locationIds) && count($locationIds)>0)
		{
			$existinglocation=$this->internalDB->queryFirstColumn("SELECT location_id FROM event_visibility WHERE  event_id=".$eventid 
				." AND  community_id=$communityId AND location_id  in (".implode(',',$locationIds).")");
			$existinglocation=empty($existinglocation)?array():$existinglocation;
			if(count($existinglocation)>0)
			{
				/* delete uselected location */		
				$locationtodelete = array_diff($existinglocation, $locationIds);
				if(!empty($locationtodelete) && count($locationtodelete)>0)
				{
					$this->internalDB->query("DELETE FROM event_visibility WHERE  event_id=$eventid " 
						." AND  community_id=$communityId AND location_id  in (".implode(',',$locationtodelete).")");
				}
			}
			/* Locations to add */
			$locationstoadd = array_diff($locationIds,$existinglocation);
			if(!empty($locationstoadd) && count($locationstoadd)>0)
			{
				foreach($locationstoadd as $location){
					$updateObject[]=array(
						"event_id"=>$eventid,
						"state_id"	=>null,
						"zone_id"=>null,
						"location_id"=>$location,
						"community_id"=>$communityId
						);
				}
			}
		}
		else{
			$this->internalDB->query("DELETE FROM event_visibility WHERE  event_id=$eventid  "
				." AND community_id= $communityId AND location_id IS NOT NULL");
		}
		if(count($updateObject)>0)	{
			$this->internalDB->insert('event_visibility',$updateObject);
		}
	}
	if(!empty($missingevents) && count($missingevents)>0){
		$this->internalDB->query("DELETE FROM event_visibility WHERE  community_id= $communityId  and event_id in  (".implode(',',$missingevents).") ");
	}
}
else{
	$this->internalDB->query("DELETE FROM event_visibility WHERE  community_id= $communityId ");
}	


?>