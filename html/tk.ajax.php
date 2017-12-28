<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	chdir($_SERVER['DOCUMENT_ROOT'].'/snap/');
	global $base_url;
	$base_url = 'http://'.$_SERVER['HTTP_HOST'];
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);

	global $user;
	$ck = array_intersect(array('administrator','Toolkit admin'),$user->roles);
	$tkadm = !empty($ck) ? true : false;
	$uid = $user->uid;

	if(!$_POST['call_id']){
		$arData=array();
		$arData['success']=false;
		$arData['msg']="Error: there was no post identifier";
		echo json_encode($arData);
		exit();
	}
	
	/*************************************************************************
		INCLUDES
	**************************************************************************/
  
	// ajax.inc.php also includes the following files
	//		"db.class.php";   	
	//		"var.inc.php";   	
	//		"func.inc.php";
  
	require_once "ajax.inc.php";  	
	
	$dbc = new dbConnect($_SERVER['DVP_STAGE']);  	// from db.class.php,  $_SERVER['DVP_STAGE'] set in .htaccess file
	
	/***************************************************************
		Functions		
	****************************************************************/	
	
	switch ($_POST['call_id']){

		case "update_content":
			
			$content_id = (int)$_POST['content_id'];
			$row_id = (int)$_POST['row_id'];
			$content_body = $_POST['content_body'];
			
			$stmt = $dbc->dbconn->prepare("insert into rst_toolkit_content (side_id,content_body,content_active,uid) values (?,?,1,?)");
			$stmt->bind_param("isi",$row_id,$content_body,$uid);
			
			//$sql = sprintf("insert into rst_toolkit_content (side_id,content_body,content_active,uid) values (%d,'%s',1,%d)",$row_id,$content_body,$uid);
						
			$arResponse=runSTMT($dbc,$stmt);

			if($arResponse['success']==true && $arResponse['rid']>0){
				
				$stmt = $dbc->dbconn->prepare("update rst_toolkit_content set content_active=0 where side_id=? and content_id != ?");
				$stmt->bind_param("ii",$row_id,$arResponse['rid']);
				$arResponse['archive']=runSTMT($dbc,$stmt);
			}
			
			echo json_encode($arResponse);
			break;
			
			
		default: 
			echo json_encode(array('success' => false, 'msg' => 'ERROR: Post parameter')); 
			
	}
	
	$dbc->dbconn->close();
?>