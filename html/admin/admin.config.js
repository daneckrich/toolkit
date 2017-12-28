var ajaxURL = "admin.ajax.php";

var file_type = {};

file_type.pdf = "fa-file-pdf-o";
file_type.xlsx = "fa-file-excel-o";
file_type.xls = "fa-file-excel-o";
file_type.docx = "fa-file-word-o";
file_type.doc = "fa-file-word-o";
file_type.pptx = "fa-file-powerpoint-o";
file_type.ppt = "fa-file-powerpoint-o";
file_type.jpg = "fa-file-image-o";
file_type.png = "fa-file-image-o";
file_type.gif = "fa-file-image-o";

// function that runs if an action icon (edit, delete, open, etc) is clicked
// objTbl is in format:
//		objTbl.dtID = "the datatable id"
//		objTbl.action = "the folder clicked" (edit, delete, open, etc)
//		objTbl.data = []	(array of table row values
// initiated in dt.js

/*
$.fn.modal.Constructor.prototype.enforceFocus = function () {
    var $modalElement = this.$element;
    $(document).on('focusin.modal', function (e) {
        var $parent = $(e.target.parentNode);
        if ($modalElement[0] !== e.target && !$modalElement.has(e.target).length
            // add whatever conditions you need here:
            &&
            !$parent.hasClass('cke_dialog_ui_input_select') && !$parent.hasClass('cke_dialog_ui_input_text')) {
            $modalElement.focus()
        }
    })
};
*/
	
	
$.fn.modal.Constructor.prototype.enforceFocus = function() {
    modal_this = this
    $(document).on('focusin.modal', function (e) {
        // Fix for CKEditor + Bootstrap IE issue with dropdowns on the toolbar
        // Adding additional condition '$(e.target.parentNode).hasClass('cke_contents cke_reset')' to
        // avoid setting focus back on the modal window.
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
            && $(e.target.parentNode).hasClass('cke_contents cke_reset')) {
            modal_this.$element.focus()
        }
    })
};

function openAction(objTbl){

	switch(objTbl.action){

		case "open":
	
			$("#tabs_regular li:eq(1) a" ).tab('show');	
			$("#tab-section").show();	
			listCats(objTbl.data[0],true);	
			
			break;
			
		case "view":

			$('#tk-section-header').empty().html('<h4>' + objTbl.data[3] + ': ' + objTbl.data[4] + '</h4>');
			$('.side-edit').attr('data-side',objTbl.data[0]);
			getContent(objTbl.data[0]);
			getFiles(objTbl.data[0]);
			$("#tab-detail").show();		
			$("#tabs_regular li:eq(2) a" ).tab('show');	
			
			break;
			
		case "active - click to hide":			

			if(objTbl.tblID == "dt_0"){			
				var val = objTbl.data[1];
				var call = 'toggle_tab';
			}
			else{
				var val = objTbl.data[2];
				var call = 'toggle_side';			
			}
			
			var obj = { call_id: call, id: objTbl.data[0], update: val }			
			ajaxPOST(ajaxURL,obj).done(function(json){
				if(json.success == true){	
					createAlert("success", "display updated");
					if(objTbl.tblID=="dt_0"){ listTabs(true); }
					else{ listCats(objTbl.data[1],true); }					
				}
				else{
					createAlert("error", "DATABASE: there was a problem changing the display: " + json.msg);
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				createAlert("error", "SERVER: there was a problem removing the content: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
				console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
			});	
			
		
			break;
			
		case "hidden - click to display":
		
			if(objTbl.tblID == "dt_0"){			
				var val = objTbl.data[1];
				var call = 'toggle_tab';
			}
			else{
				var val = objTbl.data[2];
				var call = 'toggle_side';			
			}
			var obj = { call_id: call, id: objTbl.data[0], update: val }
			
			ajaxPOST(ajaxURL,obj).done(function(json){
				if(json.success == true){						
					createAlert("success", "display updated");
					if(objTbl.tblID=="dt_0"){ listTabs(true); }
					else{ listCats(objTbl.data[1],true) }				
				}
				else{
					createAlert("error", "DATABASE: there was a problem changing the display: " + json.msg);
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				createAlert("error", "SERVER: there was a problem removing the content: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
				console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
			});	
		
			break;
			
		case "delete":
		
			var tbl = objTbl.tblID == "dt_0" ? "delete_tab" : "delete_side";
		
			$.confirm({
				text: "Are you sure you want to remove this from display.  All of it's contents will be unavailable to users.",
				confirm: function(button,dtid,data) { 
					var obj = { call_id: tbl, id: objTbl.data[0] }
					console.log(JSON.stringify(obj));
					ajaxPOST(ajaxURL,obj).done(function(json){
						console.log(JSON.stringify(json));
						if(json.success == true){	
							createAlert("success", "the content has been sent to the recycle bin.");
							
							if(objTbl.tblID == "dt_0"){ listTabs(true); }
							else if (objTbl.tblID == "dt_1"){ listCats(objTbl.data[1],true); }
						}
						else{
							createAlert("error", "DATABASE: there was a problem removing the content: " + json.msg);
						}
					})
					.fail(function(jqXHR, textStatus, errorThrown) {
						createAlert("error", "SERVER: there was a problem removing the content: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
						console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
					});	
									},
				cancel: function(button) {}
			});	
			break;
	}
}	


function clearForm(){

	$('.content-form').val('');
	CKEDITOR.instances.content_body.setData('');	

}


function getContent(id){

	var tkbody = $('#tk-section-body').empty();
	var obj = { call_id: 'get_content', side_id: id }
	ajaxPOST(ajaxURL,obj).done(function(json){
		if(json.success == true){	
			if(json.rows==0){
				tkbody.html('no content');
			}
			else{
				tkbody.html(json.data[0].content_body.stripSlashes());
			}
		}
		else{
			createAlert("error", "DATABASE: there was a problem retrieving the content: " + json.msg);
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		createAlert("error", "SERVER: there was a problem retrieving the content: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
		console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
	});	
}

function getFiles(id){

	var tkfiles = $('#tk-section-files').empty();
	var obj = { call_id: 'get_files', side_id: id }
	ajaxPOST(ajaxURL,obj).done(function(json){
		if(json.success == true){	
			if(json.rows>0){
				$.each(json.data, function(k,v){
					var txt = JSON.stringify(v);
					tkfiles.append($('<div>').addClass("row")
								.append($('<div>').addClass("col-xs-4").html(' ' + v.file_label + v.active).prepend($('<i>').addClass("fa " + file_type[v.ext])))
								.append($('<div>').addClass("col-xs-8")
									.append($('<button>').addClass("btn btn-default btn-xs btn-file-download").attr({'data-fid':v.file_id,'data-tkn':v.file_tkn,'title':'download'}).html('<i class="fa fa-download"></i>'))
									.append($('<button>').addClass("btn btn-default btn-xs btn-file-edit").attr({'data-side':v.side_id, 'data-fid':v.file_id,'data-fl':v.file_label,'data-active':v.active,'data-tkn':v.file_tkn,'title':'edit'}).html('<i class="fa fa-edit"></i>'))
									.append($('<button>').addClass("btn btn-default btn-xs btn-file-delete").attr({'data-side':v.side_id,'data-fid':v.file_id,'data-fn':v.file_name,'data-tkn':v.file_tkn,'title':'delete'}).html('<i class="fa fa-trash"></i>'))));
				});
			}
			else{
				tkfiles.html("No Files");
			}
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		createAlert("error", "SERVER: there was a problem retrieving the files: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
		console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
	});	

}

function listTabs(b){

	$('#tab-section, #tab-detail').hide();
	$('#datatable-shell-0').showDataTable({
		dtID: "dt_0", 
		ajaxURL: ajaxURL, 
		params: { call_id: "dt_0" },
		cols: ['del','open','toggle'],
		hide: 2,  
		console: false,
		clear: b
	});
}

function listCats(id,b){
	
	$('#tab-detail').hide();
	$('#add_section').attr('data-id',id);
	$('#datatable-shell-1').showDataTable({
		dtID: "dt_1", 
		ajaxURL: ajaxURL, 
		params: { call_id: "dt_1", tab_id: id },
		cols: [ 'del','view','toggle' ],
		hide: 3,  
		console: false,
		clear: b
	});
}

// function called after the datatable is initialized for any custom config that needs to be done
// initiated in dt.js

function runAfterInit(dtid){

	var table = $(dtid).DataTable();

	if(dtid=="#dt_1"){
		table.column( 2 ).data().each( function ( cell, idx ) {
			 if(cell == "0"){
				table
					.cell(idx,6)
					.nodes()
					.to$()      // Convert to a jQuery object
					.children().removeClass('fa-toggle-on').attr('title','hidden - click to display');
			 }
		});		
	}
	else if(dtid=="#dt_0"){

		var tabopt = [];
		var table = $(dtid).DataTable();
		var tabList = table
			.columns()
			.eq(0)
			.data()
			.unique();

		var esel = $('#side_tab_id');
		esel.find('option').remove().end();
		$.each(tabList, function(key,val){
			tabopt = val.toString().split(',');			
			esel.append('<option value="' + tabopt[0] + '">' + tabopt[2] + '</option>'); 
		});
		
		table.column( 1 ).data().each( function ( cell, idx ) {
			 if(cell == "0"){
				table
					.cell(idx,4)
					.nodes()
					.to$()      // Convert to a jQuery object
					.children().removeClass('fa-toggle-on').attr('title','hidden - click to display');
			 }
		});	
	}
}

// function called when tabs are activated
// initiated in tabs.js

function loadTab(i){

	switch(i){
	
		case 0:
			listTabs(false);
			$('#tab-section, #tab-detail').hide();
			break;
			
		case 1:
			$('#tab-detail').hide();
			break;
	
	}
}

// CUSTOM FUNCTIONS

function clearDetail(){
	
}

$(function() {		

	listTabs();
	$('.datatable-shell table td').css('cursor','pointer');

	$('#profile-update').on('click',function(){
		$.confirm({
			text: "Are you sure you want to udpate the database?",
			confirm: function() { 
				var obj = { call_id: "batch-profile" }
				ajaxPOST(ajaxURL,obj).done(function(json){
					if(json.success == true){	
						createAlert("success", "update success: " + json.rows + " records updated");					
					}
					else{
						createAlert("error", "there was a problem updating the database");
					}
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					createAlert("error", "there was a problem updating the database - server problem");
				});	
			},
			cancel: function(button) {}
		});	
	});
	
	$('#datatable-shell-0').on( 'click', 'td', function () {
		
		var table = $('#dt_0').DataTable();
		var cell = table.cell(this);
		var idx = cell.index().column;
		var col = table.columns( idx ).header();
		var objTbl = {}
		objTbl.row = table.row( $(this).parents('tr') );
		objTbl.data = objTbl.row.data();	
		
		if(idx==2 || idx==3 || idx==4){
			$('#tab-content-id').val(objTbl.data[0]);
			$('#tab-content').val(objTbl.data[2]);
			$('#tab-weight option').prop("selected",false);
			$('#tab-weight option[value="' + objTbl.data[3] + '"').prop("selected",true);
			$('#tab-summary').val(objTbl.data[4]);
			$('#modal-1').modal('show');		                 
		}
	
    });	

	$('#add_tab').on('click',function(){
		$('#tab-content-id').val('0');
		$('#tab-content').val('');
		$('#tab-weight option[value="50"]').prop("selected",true);
		$('#modal-1').modal('show');		
	});
	
	$('#tab_submit').on('click',function(){
	
		var id = $('#tab-content-id').val();
		var txt = $('#tab-content').val();
		var weight = $('#tab-weight').val();
		var summary = $('#tab-summary').val();
		
		if(!txt.length){
			createAlert("error","You must enter a label for the tab");
			 $('#tab-content').focus();
		}
		else{
			var call = id == 0 ? 'add_tab' : 'update_tab';
			var obj = { call_id:call, tab_id:id, tab_label:txt, tab_weight:weight, tab_summary:summary }
			ajaxPOST(ajaxURL,obj).done(function(json){
				if(json.success == true){	
					createAlert("success", "tab updated");
					$('#modal-1').modal('hide');
					listTabs(true);
				}
				else{
					createAlert("error", "DATABASE: there was a problem updating the tabs: " + json.msg);
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				createAlert("error", "SERVER: there was a problem updating the tabs: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
				console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
			});	
		}	
	});
	
	$('#add_section').on('click',function(){
		var side_tab_id=$(this).attr('data-id');
		$('#side_id').val('0');
		$('#side_label').val('');	
		$('#side_tab_id option[value="' + side_tab_id + '"]').prop('selected',true);		
		$('#side_weight option[value="50"]').prop("selected",true);
		$('#modal-2').modal('show');		
	});
	
	$('#side_submit').on('click',function(){
	
		var id = $('#side_id').val();
		var tab_id = $('#side_tab_id').val();
		var txt = $('#side_label').val();
		var weight = $('#side_weight').val();
		
		if(!txt.length){
			createAlert("error","You must enter a label for the tab");
			 $('#side_label').focus();
		}
		else{
			var call = id == 0 ? 'add_side' : 'update_side';
			var obj = { call_id:call, side_id:id, side_tab_id: tab_id, side_label:txt, side_weight:weight }
			ajaxPOST(ajaxURL,obj).done(function(json){
				if(json.success == true){	
					createAlert("success", "category updated");
					$('#modal-2').modal('hide');
					listCats(tab_id,true);
				}
				else{
					createAlert("error", "DATABASE: there was a problem updating the tabs: " + json.msg);
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				createAlert("error", "SERVER: there was a problem updating the tabs: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
				console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
			});	
		}	
	});
	
	$('#datatable-shell-1').on( 'click', 'td', function () {
		
		var table = $('#dt_1').DataTable();
		var cell = table.cell(this);
		var idx = cell.index().column;
		var col = table.columns( idx ).header();
		var objTbl = {}
		objTbl.row = table.row( $(this).parents('tr') );
		objTbl.data = objTbl.row.data();	
		
		if(idx==3 || idx==4 || idx==5){			
			$('#side_id').val(objTbl.data[0]);
			$('#side_tab_id').val(objTbl.data[1]);
			$('#side_label').val(objTbl.data[4]);
			$('#side_weight option').prop("selected",false);
			$('#side_weight option[value="' + objTbl.data[5] + '"').prop("selected",true);
			$('#modal-2').modal('show');		                 
		}		
    });	
	
	$('#tk-edit-content').on('click',function(){
	
		$('#modal-3').modal('show');
		var rid = $(this).attr('data-side');
		var body = $('#tk-section-body').html();	
		$('#edit_rid').val(rid);
		CKEDITOR.instances.content_body.setData(body);	
	});
	
	$('#content_cancel').on('click',function(){
		clearForm();
	});
	
	$('#content_submit').on('click',function(ev){

		CKEDITOR.instances.content_body.updateElement();
		var id = $('#edit_rid').val();
		var body = $('#content_body').val();
		
		var args = { call_id: 'update_content', side_id: id, content_body: body }
		
		ajaxPOST(ajaxURL,args).done(function(json){
			if(json.success == true){	
				createAlert("success","Section Updated");
				getContent(id);
				$('#modal-3').modal('hide');
				clearForm();
			}
			else{
				createAlert("error","There was a problem saving the content");
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			createAlert("error","There was a problem saving the content");
			console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
		});			
		
	});
	
	$('#tk-add-file').on('click',function(){
	
		var side_id = $(this).attr('data-side');		
		$('.no-file').show();
		$('.yes-file').hide();
		$('#file_active_cb').prop("checked",false);
		$('.file-form').val('');
		$('#file_side_id').val(side_id);
		$('#modal-4').modal('show');
	
	});
	
	// ADD FILE	

	var uploader = new ss.SimpleUpload({
		   button: 'file-upload',
		   url: ajaxURL, // server side handler
		   name: 'uploadfile', // upload parameter name        
		   responseType: 'json',
		   maxSize: 4096, // kilobytes		  
		   allowedExtensions: ['pdf','docx','doc','xlsx','xlsx','pptx','ppt','jpg','png','gif'],
		   multipart: true, // 
		   onChange: function( filename, extension, uploadBtn ){	
				var file_side_id = $('#file_side_id').val();
				var label = $('#file_label').val();
				var active = $('#file_active_cb').prop('checked') ? "0" : "1";
				var data = { call_id: "file_upload", side_id: file_side_id, file_label: label, file_active: active }
				this.setData(data);
		   },		   	   
		   onExtError: function( filename, extension, uploadBtn ){
				createAlert("error", filename + " is not a valid file.  Files can only be PDF, DOCX, DOC, XLSX, XLS, PPTX, PPT, JPG, PNG, GIF");
				return false;
		   },
		   onSizeError: function( filename, fileSize, uploadBtn ){
				var i = fileSize/1024;
				i = i.toFixed(4);
				createAlert("error", filename + " is too large.  Files must be 4MB or less.  Your file is " + i + "MB");
				return false;
		   },
		   onError: function( filename, errorType, status, statusText, response, uploadBtn ){
				createAlert("error", filename + " upload error (" + errorType + ").  Please make sure your file type is valid and the size is under 4MB and try again.");
				$('#modal-file-ajax').modal('hide');
				return false;
		   },		   
		   onSubmit: function(filename, extension) {
			    $('#modal-file-ajax').modal('show');
		   },         
		   onComplete: function(filename, response, uploadBtn) {	
				$('#modal-file-ajax').modal('hide');
			   if (!response) {
				   createAlert("error", "No response from server: " + filename + " upload failed");
				   return false;            
			   }	
			   if(response.success == true){
					createAlert('success','file uploaded');
					getFiles($('#file_side_id').val());
					$('#modal-file-ajax').modal('hide');
					$('#modal-4').modal('hide');
			   }
			   else{
					createAlert("error", filename + " upload failed " + response.msg);
					return false;
			   }
		   }					
		});	
		
	$('#tk-section-files').on('click','.btn-file-download',function(){
		var fid = $(this).attr('data-fid');
		var tkn = $(this).attr('data-tkn');
		var url = "/toolkit/file.php?fid=" + fid + "&tkn=" + tkn;
		window.open(url);	
	});
	
	$('#tk-section-files').on('click','.btn-file-edit',function(){
		var file_side_id = $(this).attr('data-side');
		var fid = $(this).attr('data-fid');
		var tkn = $(this).attr('data-tkn');
		var label = $(this).attr('data-fl');
		var b = $(this).attr('data-active') == " (Hidden)" ? true : false;
		$('.no-file').hide();
		$('.yes-file').show();
		
		$('#file_side_id').val(file_side_id); 
		$('#file_id').val(fid);
		$('#file_label').val(label);
		$('#file_tkn').val(tkn);					
		$('#file_active_cb').prop('checked',b);
		
		$('#modal-4').modal('show');		
	});
	
	$('#tk-section-files').on('click','.btn-file-delete',function(){		
		
		var file_side_id = $(this).attr('data-side');
		var fid = $(this).attr('data-fid');
		var ftoken = $(this).attr('data-tkn');
	
		$.confirm({
				text: "Are you sure you want to delete this file?",
				confirm: function() { 
					var obj = { call_id: "delete_file", file_id: fid, tkn: ftoken }
					ajaxPOST(ajaxURL,obj).done(function(json){
						if(json.success == true){	
							createAlert("success", "the file has been deleted.");	
							getFiles(file_side_id);
						}
						else{
							createAlert("error", "DATABASE: there was a problem deleting the file: " + json.msg);
						}
					})
					.fail(function(jqXHR, textStatus, errorThrown) {
						createAlert("error", "SERVER: there was a problem deleting the file: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
						console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
					});	
				},
				cancel: function(button) {}
			});			
	});
	
	$('#file_submit').on('click',function(){
	
		var file_side_id = $('#file_side_id').val();
		var fid = $('#file_id').val();
		var label = $('#file_label').val();
		var active = $('#file_active_cb').prop('checked') ? "0" : "1";
		var ftoken = $('#file_tkn').val();
		
		var data = { call_id: "file_update", file_id: fid, tkn: ftoken, file_label: label, file_active: active }
		ajaxPOST(ajaxURL,data).done(function(json){
			if(json.success == true){	
				createAlert("success", "the file has been updated.");	
				$('#modal-4').modal('hide');
				getFiles(file_side_id);
			}
			else{
				createAlert("error", "DATABASE: there was a problem updating the file: " + json.msg);
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			createAlert("error", "SERVER: there was a problem updating the file: " + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
			console.log('ERROR: ' + JSON.stringify(jqXHR) + ' | ' + textStatus + ' | ' + errorThrown);
		});	
	
	});
	
});