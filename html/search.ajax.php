<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	include_once "toolkit/config.inc.php";
	include_once "ajax.inc.php";
	
	$call = checkRequest();
	
	/***************************************************************
		PROCESS CALL ID	
	****************************************************************/	
	
	$arResponse = array();

	switch ($call){

		case "tk_search":			
			
			if(strlen($_POST['search_term']) === 3){
				
				$search = $dbc->quote('%'.$_POST['search_term'].'%');
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
							and tkc.content_body like %s LIMIT 0,12",$search);
			}
			else{
				
				$search = split(' ',$_POST['search_term']);
				$search = implode('* ',$search) . '*';
				$search = $dbc->quote($search);
				
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
							and match (tkc.content_body) against (%s IN BOOLEAN MODE) LIMIT 0,12",$search);
			
			}		
							
			$sql = str_replace(array("\n","\r","\t")," ",$sql);
							
			$arResponse=runSQL($dbc,$sql);
			$arResponse['sql'] = $sql;
			$arResponse['len'] = strlen($search);
			$arResponse['search'] = $search;
		
			break;
			
		default: 
			$arResponse = array('success' => false, 'msg' => 'ERROR: Post parameter'); 
			
	}
	
	echo json_encode($arResponse);
	
?>