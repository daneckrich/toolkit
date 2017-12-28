<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	include_once "toolkit/config.inc.php";
	include_once "ajax.inc.php";
	
	if(ADM == false){
		$arData=array();
		$arData['success']=false;
		$arData['msg']="Error: you don't have permission to access this resource";
		echo json_encode($arData);
		exit();
	}
	
	$call = checkRequest(ADM);
	
	/***************************************************************
		PROCESS CALL ID	
	****************************************************************/	
	
	$arResponse = array();

	switch ($call){
	
		case "dt_0":
		
			$sql="select 
					tab_id as tk_tab,	
					tab_active,
					tab_label as Tab,
					tab_weight as Weight,
					tab_summary as Summary
					from rst_toolkit_tab
					where tab_active Is Not Null
					order by tab_active DESC, tab_weight, tab_label";
		
			$arResponse=runDatatable($dbc,$sql);
			break;

			
		case "dt_1":
		
			$tab_id=(int)$_POST['tab_id'];
			$sql=sprintf("select 
					side_id,
					rst_toolkit_side.tab_id as tk_tab,	
					side_active,					
					CONCAT(rst_toolkit_tab.tab_label,if(tab_active=0,' (Hidden)','')) as Tab,
					rst_toolkit_side.side_label as Category,
					side_weight as Weight
					from rst_toolkit_side
					join rst_toolkit_tab
					on rst_toolkit_side.tab_id=rst_toolkit_tab.tab_id
					where rst_toolkit_tab.tab_id=%d
					and side_active Is Not Null
					order by side_active DESC, side_weight, side_label",$tab_id);
		
			$arResponse=runDatatable($dbc,$sql);
			break;
			
		case "toggle_tab":
		
			$tab_id = (int)$_POST['id'];
			$active = (int)$_POST['update']==1 ? 0 : 1;
			
			if(is_numeric($tab_id) && $tab_id > 0){
				$sql = sprintf("update rst_toolkit_tab set tab_active=%d where tab_id=%d",$active,$tab_id);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: Post parameter Tab: '.$tab_id);
			}
		
			break;
			
		case "delete_side":
		
			$side_id = (int)$_POST['id'];
			if(is_numeric($side_id) && $side_id > 0){
				$sql = sprintf("update rst_toolkit_side set side_active = Null, uid=%d where side_id=%d",$uid,$side_id);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: Post parameter CAT: '.$side_id);
			}
		
			break;
			
		case "add_tab":
		
			$tab_label = preg_replace('/[^a-zA-Z0-9 _-]/i','',$_POST['tab_label']);
			$tab_weight = (int)$_POST['tab_weight'];
			$tab_summary = $_POST['tab_summary'];
			
			if(!empty($tab_label)){
				$sql=sprintf("insert into rst_toolkit_tab (tab_label,tab_active,tab_weight,uid,tab_summary) values ('%s',1,%d,%d,'%s')",$tab_label,$tab_weight,$uid,$tab_summary);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing content');
			}
			
			break;
			
		case "update_tab":
		
			$tab_id = (int)$_POST['tab_id'];
			$tab_label = preg_replace('/[^a-zA-Z0-9 _-]/i','',$_POST['tab_label']);
			$tab_weight = (int)$_POST['tab_weight'];
			$tab_summary = $_POST['tab_summary'];
			
			if($tab_id>0 && !empty($tab_label)){
				$sql=sprintf("update rst_toolkit_tab set tab_label='%s', tab_weight=%d, uid=%d, tab_summary='%s' where tab_id=%d",$tab_label,$tab_weight,$uid,$tab_summary,$tab_id);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing content');
			}

			break;
			
		case "delete_tab":
		
			$tab_id = (int)$_POST['id'];						
			if($tab_id>0){
				$sql=sprintf("update rst_toolkit_tab set tab_active = Null, uid=%d where tab_id=%d",$uid,$tab_id);
				$arResponse=runSQL($dbc,$sql);
				$arResponse['query']=$sql;
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing content');
			}
			
			break;
			
		case "toggle_side":
		
			$side_id = (int)$_POST['id'];
			$active = (int)$_POST['update']==1 ? 0 : 1;
			
			if(is_numeric($side_id) && $side_id > 0){
				$sql = sprintf("update rst_toolkit_side set side_active=%d, uid=%d where side_id=%d",$active,$uid,$side_id);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: Post parameter Category: '.$side_id);
			}
		
			break;
			
		case "add_side":
		
			$side_label = addslashes(preg_replace('#[^\w \-,:.()&\/\']#','',$_POST['side_label']));
			$tab_id = (int)$_POST['side_tab_id'];
			$side_weight = (int)$_POST['side_weight'];
			
			if(!empty($side_label) && $tab_id>0){
				$sql=sprintf("insert into rst_toolkit_side (tab_id,side_label,side_active,side_weight,uid) values (%d,'%s',1,%d,%d)",$tab_id,$side_label,$side_weight,$uid);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing content');
			}
			
			break;
			
		case "update_side":
		
			$side_id = (int)$_POST['side_id'];
			$side_label = addslashes(preg_replace('#[^\w \-,:.()&\/\']#','',$_POST['side_label']));
		
			$tab_id = (int)$_POST['side_tab_id'];
			$side_weight = (int)$_POST['side_weight'];
			
			if($side_id > 0 && !empty($side_label) && $tab_id > 0){
				$sql=sprintf("update rst_toolkit_side set tab_id=%d, side_label='%s', side_weight=%d, uid=%d where side_id=%d",$tab_id,$side_label,$side_weight,$uid,$side_id);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing content');
			}
			
			break;
			
				
		case "get_content":
		
			$side_id = (int)$_POST['side_id'];
			if($side_id > 0){
				$sql=sprintf("select content_body from rst_toolkit_content where side_id=%d and content_active=1",$side_id);
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing id');
			}
			
			break;
			
		case "update_content":
			
			$row_id = (int)$_POST['side_id'];
			$content_body = $_POST['content_body'];
			
			$stmt = $dbc->dbconn->prepare("insert into rst_toolkit_content (side_id,content_body,content_active,uid) values (?,?,1,?)");
			$stmt->bind_param("isi",$row_id,$content_body,$uid);
			
			$arResponse=runSTMT($dbc,$stmt);

			if($arResponse['success']==true && $arResponse['rid']>0){
				
				$stmt = $dbc->dbconn->prepare("update rst_toolkit_content set content_active=0 where side_id=? and content_id != ?");
				$stmt->bind_param("ii",$row_id,$arResponse['rid']);
				$arResponse['archive']=runSTMT($dbc,$stmt);
			}
			
			break;
			
		case "file_upload":  // upload the files and insert them into the db, proj_id will be set when the form is submitted
		
			require_once "class/uploader.class.php";
			
			$side_id = (int)$_POST['side_id'];
			
			$valid_extensions = array('pdf','docx','doc','xlsx','xlsx','pptx','ppt','jpg','png','gif');		
			$upload = new FileUpload('uploadfile');
			$ext = $upload->getExtension(); // Get the extension of the uploaded file
			$tkn = md5($upload->getFileName());
			$upload->newFileName = $side_id.'_'.substr(uniqid('', true), -4).'_'.gmdate('YmdHis').'.'.$ext;
			$result = $upload->handleUpload('/files/var/toolkitfiles/', $valid_extensions);
		
			if ($result) {
				$path = $upload->getSavedFile();
				$flsize = getimagesize($path); 
				$fl_name = $upload->getFileName();
				$fl_label = addslashes(preg_replace('#[^\w \-,:()\']#','',$_POST['file_label']));
				$active = (int)$_POST['file_active'];
				
				$sql=sprintf("insert into rst_toolkit_file (side_id,file_name,file_label,file_tkn,file_active,uid) values (%d,'%s','%s','%s',%d,%d)",$side_id,$fl_name,$fl_label,$tkn,$active,$uid);
				$arResponse = runSQL($dbc,$sql);	
			}
			else{
				$arResponse = array('success' => false, 'msg' => 'SERVER: '.$upload->getErrorMsg(), 'dir' => $upload_dir); 
			}	

			break;
			
		case "file_update":  // upload the files and insert them into the db, proj_id will be set when the form is submitted
		
			$file_id = (int)$_POST['file_id'];
			$file_tkn = preg_replace('#[^\w]#','',$_POST['tkn']);
			$fl_label = addslashes(preg_replace('#[^\w \-,:()\']#','',$_POST['file_label']));
			$active = (int)$_POST['file_active'];
				
			if($file_id > 0){
				$sql=sprintf("update rst_toolkit_file set 
					file_label='%s',
					file_active = %d where file_id=%d and file_tkn='%s'",$fl_label,$active,$file_id,$file_tkn); 					
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing id');
			}
	
			break;
			
		case "get_files":
		
			$side_id = (int)$_POST['side_id'];
			if($side_id > 0){
				$sql=sprintf("select 
					side_id, 
					file_id, 
					file_name, 
					file_label, 
					file_tkn, 
					SUBSTRING_INDEX(file_name,'.',-1) as ext, 
					if(file_active=0,' (Hidden)','') as active 
					from rst_toolkit_file 
					where side_id=%d 
					and file_active Is Not Null					
					order by file_label",$side_id);
					
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing id');
			}		

			break;			
			
		case "delete_file":
		
			$file_id = (int)$_POST['file_id'];
			$tkn = preg_replace('#[^\w]#','',$_POST['tkn']);
			if($file_id > 0){
				$sql=sprintf("update rst_toolkit_file set file_active = Null, uid=%d where file_id=%d and file_tkn='%s'",$uid,$file_id,$tkn); 					
				$arResponse=runSQL($dbc,$sql);
			}
			else{
				$arResponse=array('success' => false, 'msg' => 'ERROR: missing id');
			}
		
			break;
			
		default: 
			echo $arResponse = array('success' => false, 'msg' => 'ERROR: Post parameter'); 
			
	}
	
	echo json_encode($arResponse);

?>