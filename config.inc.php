<?php

define("DB","toolkit");
define("HOMEWEB","/toolkit/");
define("TEMPLATE","toolkit/template/");
define("SITENAME","Research Toolkit");
define("FILEPATH","/files/var/toolkitfiles/");


//************************************************************************
//	this snipped calls the drupal bootstrap session
//	to check if the user is logged in 
//	and if the user is a toolkit administrator
//************************************************************************

chdir($_SERVER['DOCUMENT_ROOT'].'/snap/');
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);

global $user;
$ck = array_intersect(array('administrator','Toolkit admin'),$user->roles);
$tkadm = !empty($ck) ? true : false;
$uid = $user->uid;

define("ADM",$tkadm);
define("UID",$uid);

?>