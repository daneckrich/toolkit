<?

if(!$_REQUEST['fid'] || !is_numeric($_REQUEST['fid']) || !$_REQUEST['tkn']){
	exit;
}
require_once "toolkit/config.inc.php";
require_once 'class/getFile.class.php';
$flinfo = new getFile($_REQUEST['fid']);

if($_REQUEST['tkn'] !== $flinfo->tkn){
	echo "ERROR: security error";
	exit;
}

$dl = substr(uniqid('', true), -8).'.'.$flinfo->ext;
$fl=FILEPATH.$flinfo->name;

if(file_exists($fl)){
	header('Content-Type: '.$flinfo->mime);
	header('Content-Disposition: attachment; filename="'.$dl.'"');
	header('Content-Description: File Transfer');
	header('Content-Length: ' . filesize($fl));
	ob_clean();
	flush();
	readfile($fl);
	exit(0);
	
}
else{
	echo "ERROR: file does not exist";
}
?>
