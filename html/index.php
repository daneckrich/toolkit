<?php
/******************************************************************************************************

Framework for pages/apps
	index.php	[all content]
	ajax.php	[all the db calls]
	config.js	[processing user actions and data returned from ajax calls]

include directory on dev1: /files/var/accel/

-----------------------------------------------------------------------------------------------

buildTemplate.class.php contains all of the default CSS, JS, Headers & Menus

to initialize: $obj = new buildTemplate();
to add CSS: $obj->addCSS(path_or_url_to_file);
to add JS: $obj->addJS(path_or_url_to_file);
to add conditional IE JS: $obj->addJSIE(path_or_url_to_file);
to create the page call $obj->execute();

to add Accordion Feature
	$obj->addAccordion();
	
to add DataTables
	$obj->addDataTables();
	
to add Tabs
	$obj->addTabs();
	
to add Forms (multi-select, datetimepicker)
	$obj->addForm();
	
to add all JS & CSS libraries
	$obj->addAll();


all content must be placed in a div with id="main_content"

Bootstrap grid layout

row = full width
columns are based on a full width of 12 increments
col-lg-#	≥1200px
col-md-#	992px-1199px
col-sm-#	768px-991px
col-xs-#	<767px 
Each tier of classes scales up, meaning if you plan on setting the same widths for xs and sm, you only need to specify xs.

*******************************************************************************************************/

include_once "toolkit/config.inc.php";
include_once "buildTemplate.class.php";
include_once "func.inc.php";

$pg = new buildTemplate();
$pg->addTabs();
$pg->addAccordion();
$pg->addForm();
$pg->addJS("/toolkit/js/ckeditor/ckeditor.js");
$pg->addJS("tk.config.js");
$pg->addCSS("./css/toolkit.css");
$pg->addCSS("./css/tk.css");
$pg->execute();

	$sql = "select 
		rst_toolkit_tab.tab_id,
		rst_toolkit_tab.tab_label,
		rst_toolkit_tab.tab_summary,
		rst_toolkit_side.side_id,
		rst_toolkit_side.side_label,
		rst_toolkit_content.content_id,
		rst_toolkit_content.content_body
		from rst_toolkit_tab
		left join rst_toolkit_side
		on rst_toolkit_tab.tab_id=rst_toolkit_side.tab_id
		left join rst_toolkit_content
		on rst_toolkit_side.side_id=rst_toolkit_content.side_id and rst_toolkit_content.content_active=1
		where rst_toolkit_tab.tab_active=1
		and rst_toolkit_side.side_active=1
		order by rst_toolkit_tab.tab_weight, rst_toolkit_side.side_weight";


	$qry = $dbc->prepare($sql);		

	if($qry){
		
		$qry->execute();
			
		while($row = $qry->fetch(PDO::FETCH_ASSOC)){
			$tid = $row['tab_id'];
			$sid = $row['side_id'];
			$cid = $row['content_id'];
			$tab[$tid]=$row['tab_label'];
			$tab_summary[$tid]=nl2br($row['tab_summary']);
			$side[$tid][$sid]=$row['side_label'];
			$content[$tid][$sid][$cid]=stripslashes($row['content_body']);	
		}
			
		$qry->closeCursor();		
	}

	unset($sql);

	$sql = "select file_id, side_id, file_label, file_tkn, SUBSTRING_INDEX(file_name,'.',-1) as ext from rst_toolkit_file where file_active=1 order by side_id, file_label";
	
	$qry = $dbc->prepare($sql);		

	if($qry){
		
		$qry->execute();
			
		while($row = $qry->fetch(PDO::FETCH_ASSOC)){
			$fsid = $row['side_id'];
			$fid = $row['file_id'];
			$fl[$fsid][$fid]['label']=$row['file_label'];
			$fl[$fsid][$fid]['tkn']=$row['file_tkn'];
			$fl[$fsid][$fid]['ext']=$row['ext'];
		}
		
		$qry->closeCursor();	
	}

	$file_type['pdf'] = "fa-file-pdf-o";
	$file_type['xlsx'] = "fa-file-excel-o";
	$file_type['xls'] = "fa-file-excel-o";
	$file_type['docx'] = "fa-file-word-o";
	$file_type['doc'] = "fa-file-word-o";
	$file_type['pptx'] = "fa-file-powerpoint-o";
	$file_type['ppt'] = "fa-file-powerpoint-o";
	$file_type['jpg'] = "fa-file-image-o";
	$file_type['png'] = "fa-file-image-o";
	$file_type['gif'] = "fa-file-image-o";

?>

<div id="main_content">
	<br>
	<div id="search_page" style="display:none;">
		<div class="row">
			<div class="col-sm-4 col-xs-12">
				<div class="form-group input-group">
					<input type="text" class="form-control" id="search_value" placeholder="enter search term">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" id="search_submit"><i class="fa fa-search"></i>
						</button>
					</span>					
				</div>				
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12" id="search_results"></div>
		</div>
	</div>
	<div id="tabs_page">
		<div id="tabs_regular">
			<ul class="nav nav-pills">
				<?php
					$i=0;
					foreach($tab as $key => $val){
						$class = $i==0 ? "active" : "";
						printf('<li class="%s"><a href="#tabs-%s" data-toggle="tab" aria-expanded="">%s</a></li>',$class,$key,$val);
						$i++;
					}
					unset($i);
					reset($tab);
				?>
			</ul>	
			<div class="tab-content">
			
				<?php
					$i=0;
					foreach($tab as $tabkey => $tabval){
						$class = $i==0 ? "active" : "";
						printf('<div id="tabs-%s" class="tab-pane fade in %s">',$tabkey,$class);
							if(!empty($tab_summary[$tabkey])) printf('<br><div class="well well-sm">%s</div>',$tab_summary[$tabkey]);
							if(isset($side[$tabkey])){
							
								if(count($side[$tabkey]) > 1){
									printf('<div id="accordion-%s" class="accordion-div" style="margin-top:1em;"><div class="accordion_content">',$tabkey);
										foreach($side[$tabkey] as $rowkey => $rowval){
											printf('<div class="accordion_row">');
												printf('<div class="accordion_header" data-sid="%d">%s</div>',$rowkey,$rowval);
												printf('<div class="accordion_body">');
													if(ADM === true){ printf('<div class="row text-right bottom5"><div class="col-xs-12"><button class="edit-content btn btn-default btn-xs" data-row="%s"><i class="fa fa-edit"></i></button></div></div>',$rowkey); }
													if(isset($content[$tabkey][$rowkey])){
														foreach($content[$tabkey][$rowkey] as $content_id => $body){
															printf('<div id="rid_%s" data-content-id="%s" data-tab=%d class="well well-sm">%s</div>',$rowkey,$content_id, $i, $body);													
														}
													}
													if(isset($fl[$rowkey])){
														printf('<div style="margin-left:2em;">');
														foreach($fl[$rowkey] as $filekey => $fileinfo){
															printf('<i class="fa %s"></i> ',$file_type[$fileinfo['ext']]);
															printf('<a href="/toolkit/file.php?fid=%d&tkn=%s" target="_blank">%s</a><br>',$filekey,$fileinfo['tkn'],$fileinfo['label']);
														}
														printf('</div>');													
													}
												printf('</div>');
											printf('</div>');
										}
									printf('</div></div>');
								}
								else{
									foreach($side[$tabkey] as $rowkey => $rowval){
										
										printf('<div style="margin-left:2em;">');
											printf('<div class="accordion_header" data-sid="%d"><h3>%s</h3></div>',$rowkey,$rowval);
											printf('<div class="accordion_body">');
												
												if($user->uid > 0){ printf('<div class="row text-right bottom5"><div class="col-xs-12"><button class="edit-content btn btn-default btn-xs" data-row="%s"><i class="fa fa-edit"></i></button></div></div>',$rowkey); }
												if(isset($content[$tabkey][$rowkey])){
													foreach($content[$tabkey][$rowkey] as $content_id => $body){
														printf('<div id="rid_%s" data-content-id="%s" data-tab=%d class="well well-sm">%s</div>',$rowkey,$content_id, $i, $body);													
													}
												}
												if(isset($fl[$rowkey])){
													printf('<div style="margin-left:2em;">');
													foreach($fl[$rowkey] as $filekey => $fileinfo){
														printf('<i class="fa %s"></i> ',$file_type[$fileinfo['ext']]);
														printf('<a href="/dash/toolkit/file.php?fid=%d&tkn=%s" target="_blank">%s</a><br>',$filekey,$fileinfo['tkn'],$fileinfo['label']);
													}
													printf('</div>');													
												}
											printf('</div>');
										printf('</div>');											
									}								
								}					
								
							}
						printf('</div>');
						$i++;
					}
				?>
							
			</div>			
		</div>
	</div>
	
	<?php if(ADM === true): ?>
	<div id="modal_forms">
		
		<!-- Modal -->
		<div class="modal fade" id="modal-1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form id="dialog_form_status">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Update Category</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id="edit_cid" name="edit_cid" class="content-form" value="">
						<input type="hidden" id="edit_rid" name="edit_rid" class="content-form" value="">						
						<textarea class="ckeditor" id="content_body" class="content-form" name="content_body" rows="40" cols="70" style="height:25em; width:99%;"></textarea>
							
					</div>
					<div class="modal-footer">
						<button id="content_cancel" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button id="content_submit" type="submit" class="btn btn-primary">Save changes</button>
					</div>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
	</div>
	<?php endif; ?>
	
</div>
