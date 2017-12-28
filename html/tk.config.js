var ajaxURL = "tk.ajax.php";

function loadTab(i){}

function clearForm(){

	$('.content-form').val('');
	CKEDITOR.instances.content_body.setData('');	

}

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

$(function() {		
	
	$('#modal-1').modal('hide');
	
	var acc_id;
	$('.accordion-div').each(function(i, el){
		acc_id = '#' + $(el).attr('id');
		$(acc_id).loadAccordion({type:'html'});
	});
	
	$('.edit-content').on('click',function(){
	
		$('#modal-1').modal('show');
		
		var e = $(this).parent().parent().next('div');
		var body = e.html();
		var cid = e.attr('data-content-id');
		var rid = $(this).attr('data-row');
		
		$('#edit_cid').val(cid);
		$('#edit_rid').val(rid);
		CKEDITOR.instances.content_body.setData(body);	
	});
	
	$('#content_cancel').on('click',function(){
		clearForm();
	});
	
	$('#content_submit').on('click',function(ev){
	
		ev.preventDefault();
		CKEDITOR.instances.content_body.updateElement();
		var cid = $('#edit_cid').val();
		var rid = $('#edit_rid').val();
		var body = $('#content_body').val();
		
		var args = { call_id: 'update_content', content_id: cid, row_id: rid, content_body: body }
		
		ajaxPOST(ajaxURL,args).done(function(json){
			console.log(JSON.stringify(json));
			if(json.success == true){	
				createAlert("success","Section Updated");
				$('#rid_'+rid).html(body);
				$('#rid_'+rid).attr({'data-content-id':json.rid});
				$('#modal-1').modal('hide');
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
	
	$('div.well a[href^="#"]').on('click', function(e){	
		
		e.preventDefault();
		var aid = $(this).attr('href');
		var e = $(aid);
		var tab_i = e.closest('.well').attr('data-tab');
		
		$('#tabs_page ul.nav-pills li:eq(' + tab_i + ') a').tab('show');	
		e.closest('div.panel-collapse').collapse('show');
		setTimeout(function(){
			y = e.position().top - 50;
			x = 0;
			window.scrollTo(x, y); 
		}, 500);		
		
	});
	
	$('div.well a[href^="http"]').on('click', function(e){			
		e.preventDefault();
		var url = $(this).attr('href');
		window.open(url, '_blank');		
	});

	
	$('a[data-toggle="tab"], div.well a[href^="#"]').on('click', function(e) {
		console.log($(this).attr('href'));
		history.pushState(null, null, $(this).attr('href'));
	});
  
	// navigate to a tab when the history changes
	window.addEventListener("popstate", function(e) {
		if(location.hash.length){
			var activeTab = $('[href=' + location.hash + ']');
			if (activeTab.length) {
				activeTab.tab('show');
			} 
			else {
				$('.nav-pills a:first').tab('show');
			}
		}
		else {
			$('.nav-pills a:first').tab('show');
		}
	});
	
});