<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	include_once "toolkit/config.inc.php";
	include_once "ajax.inc.php";
	
	$call = checkRequest(ADM);
	
	/***************************************************************
		PROCESS CALL ID	
	****************************************************************/	
	
	$arResponse = array();

	switch ($call){

		case "update_content":
		
			$stmt = $dbc->prepare("insert into rst_toolkit_content (side_id,content_body,content_active,uid) values (?,?,1,?)");
			$stmt->bindParam(1, $_POST['row_id'], PDO::PARAM_INT);
			$stmt->bindParam(2, $_POST['content_body'], PDO::PARAM_STR);
			$stmt->bindParam(3, UID, PDO::PARAM_INT);
						
			$arResponse=runSTMT($dbc,$stmt);

			if($arResponse['success']==true && $arResponse['rid']>0){
				
				$stmt = $dbc->prepare("update rst_toolkit_content set content_active=0 where side_id=? and content_id != ?");
				$stmt->bindParam(1, $row_id, PDO::PARAM_INT);
				$stmt->bindParam(2, $arResponse['rid'], PDO::PARAM_INT);
				$arResponse['archive']=runSTMT($dbc,$stmt);
			}			
			
			break;

		case "tk_search":
		
			$search = $dbc->quote($_POST['search_term']);
		
			$sql = sprintf("select tkt.tab_id,
							tkt.tab_label,
							tks.side_id,
							tks.side_label,
							tkc.content_id,
							tkc.content_body
							from rst_toolkit_tab tkt
							join rst_toolkit_side tks on tkt.tab_id=tks.tab_id and tks.side_active=1
							join rst_toolkit_content tkc on tks.side_id=tkc.side_id and tkc.content_active=1
							where tkt.tab_active=1
							and match (tkc.content_body) against (%s IN NATURAL LANGUAGE MODE)",$search);
							
			$sql = str_replace(array("\n","\r","\t")," ",$sql);
							
			$arResponse=runSQL($dbc,$sql);
		
			break;
			
		default: 
			$arResponse = array('success' => false, 'msg' => 'ERROR: Post parameter'); 
			
	}
	
	echo json_encode($arResponse);
	
?>