<?php
/******************************************************************************************************

using the bones framework

	index.php	[all content]
	ajax.php	[all the db calls]
	config.js	[processing user actions and data returned from ajax calls]

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

if(ADM == false){

	echo '<div id="main_content"><br><div class="alert alert-danger">You do not have permission to access this page</div></div>';
	$pg->execute();
	exit;
}

$pg = new buildTemplate();
$pg->addTabs();
$pg->addDataTables();
$pg->addForm();
$pg->addJS("/toolkit/js/ckeditor/ckeditor.js");
$pg->addJS("admin.config.js");
$pg->addCSS("admin.css");
$pg->execute();

function sectionWeight(){
	for($i=10;$i<=70;$i++){
		printf('<option value="%d">%d</option>',$i,$i);
	}
}
?>

<div id="main_content">
	<div class="row"><h1>Toolkit Administration</h1></div>	
	<div id="tabs_page">
		<div id="tabs_regular">
			<ul class="nav nav-tabs">
				<li class="active" ><a href="#tabs-min-0" data-toggle="tab">Tabs</a></li>
				<li><a id="tab-section" style="display:none" href="#tabs-min-1" data-toggle="tab">Sections</a></li>
				<li><a id="tab-detail" style="display:none" href="#tabs-min-2" data-toggle="tab">Content</a></li>
			</ul>	
			<div class="tab-content">
				<div id="tabs-min-0" class="tab-pane fade in active">
					<div class="row text-right" style="margin:3px;">
						<div class="col-lg-12">
							<button class="btn btn-primary" id="add_tab" title="add tab"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					<div id="datatable-shell-0" class="datatable-shell"></div>
				</div>	
				<div id="tabs-min-1" class="tab-pane fade in">
					<div class="row text-right" style="margin:3px;">
						<div class="col-lg-12">
							<button class="btn btn-primary" id="add_section" data-id="0" title="add section"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					<div id="datatable-shell-1" class="datatable-shell"></div>
				</div>	
				<div id="tabs-min-2" class="tab-pane fade in">
					<br>
					<div id="tk-section-header" class="well well-sm tk-section-detail"></div>
					<div class="row text-right bottom5">
						<div class="col-xs-12">
							<button id="tk-edit-content" class="side-edit btn btn-default btn-xs" title="edit content" data-side=""><i class="fa fa-edit"></i></button>
						</div>
					</div>
					<div id="tk-section-body" class="well well-sm tk-section-detail"></div>
					<div class="row text-right bottom5">
						<div class="col-xs-12">
							<button id="tk-add-file" class="side-edit btn btn-default btn-xs" title="add file" data-side=""><i class="fa fa-plus"></i></button>
						</div>
					</div>
					<div id="tk-section-files" class="well well-sm tk-section-detail"></div>
				</div>
	
	
	<div id="modal_forms">
		
		<!-- Modal -->
		<div class="modal fade" id="modal-1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form id="dialog_form_tab">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Update Toolkit Tabs</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id="tab-content-id" class="form-control tab-form">
						<div class="form-group">
							<label>Tab Description</label>
							<input type="text" id="tab-content" class="form-control tab-form">
						</div>	
						<div class="form-group">
                            <label>Weight</label>
                            <select class="form-control tab-content" id="tab-weight">
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="17">17</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="20">20</option>
								<option value="21">21</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>
								<option value="25">25</option>
								<option value="26">26</option>
								<option value="27">27</option>
								<option value="28">28</option>
								<option value="29">29</option>
								<option value="30">30</option>
								<option value="31">31</option>
								<option value="32">32</option>
								<option value="33">33</option>
								<option value="34">34</option>
								<option value="35">35</option>
								<option value="36">36</option>
								<option value="37">37</option>
								<option value="38">38</option>
								<option value="39">39</option>
								<option value="40">40</option>
								<option value="41">41</option>
								<option value="42">42</option>
								<option value="43">43</option>
								<option value="44">44</option>
								<option value="45">45</option>
								<option value="46">46</option>
								<option value="47">47</option>
								<option value="48">48</option>								
								<option value="49">49</option>
								<option value="50">50</option>
							</select>
							<p class="help-block">This determines the tab order.</p>
						</div>	

						<div class="form-group">
                            <label>Tab Summary</label>
                            <textarea id="tab-summary" class="form-control tab-form" rows="3"></textarea>
							<p class="help-block">brief description that displays above the sections.</p>
						</div>							
					</div>
					<div class="modal-footer">
						<button id="tab_cancel" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button id="tab_submit" type="button" class="btn btn-primary">Save</button>
					</div>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
		<!-- Modal -->
		<div class="modal fade" id="modal-2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form id="dialog_form_category">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Update Toolkit Category</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id="side_id" class="form-control content-form">
						<div class="form-group">
                            <label>Tab</label>
                            <select class="form-control content-form" id="side_tab_id">								
							</select>							
						</div>										
						<div class="form-group">
							<label>Tab Description</label>
							<input type="text" id="side_label" class="form-control content-form">
						</div>	
						<div class="form-group">
                            <label>Weight</label>
                            <select class="form-control tab-content" id="side_weight">
							<?php sectionWeight(); ?>												
							</select>
							<p class="help-block">This determines the category order.</p>
						</div>													
							
					</div>
					<div class="modal-footer">
						<button id="side_cancel" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button id="side_submit" type="button" class="btn btn-primary">Save</button>
					</div>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
		<!-- Modal -->
		<div class="modal fade" id="modal-3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form id="dialog_form_side">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Update Category</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id="edit_rid" name="edit_rid" class="content-form" value="">						
						<textarea id="content_body" class="ckeditor content-form" name="content_body" rows="60" cols="70" style="height:45em; width:99%;"></textarea>
							
					</div>
					<div class="modal-footer">
						<button id="content_cancel" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button id="content_submit" type="button" class="btn btn-primary">Save changes</button>
					</div>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
		<!-- Modal -->
		<div class="modal fade" id="modal-4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form id="dialog_form_file">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">File Updates</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id="file_side_id" name="file_side_id" class="content-form file-form" value="">	
						<input type="hidden" id="file_id" name="file_id" class="content-form file-form" value="">	
						<input type="hidden" id="file_name" name="file_name" class="content-form file-form" value="">	
						<input type="hidden" id="file_tkn" name="file_tkn" class="content-form file-form" value="">								
						<div class="form-group">
							<label>File Label</label>
							<input type="text" id="file_label" class="form-control content-form file-form">
						</div>
						<div class="form-group">
                            <div class="checkbox">
								<label>
									<input id="file_active_cb" name="file_active_cb" type="checkbox" value="0">Hidden
								</label>
                            </div>
                        </div>						
						<div>
							<button id="file-upload" type="button" class="btn btn-default file-nav no-file" title="upload file"><i class="fa fa-upload"></i></button>
						</div>
					</div>
					<div class="modal-footer">
						<button id="file_cancel" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button id="file_submit" type="button" class="btn btn-primary file-nav yes-file">Save</button>
					</div>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->	
		
		<div class="modal fade" id="modal-file-ajax" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div style="width:100%;text-align:center;">
						<i class="fa fa-spin fa-spinner fa-4x"></i><br>
						<h3>Loading...</h3>				
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
	</div>
	
</div>
