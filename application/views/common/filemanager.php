<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
	var dir = '<?php echo $directory; ?>';
</script>
<div class="content-wrapper" id="filemanager">
    <?php $this->load->view('common/file_view'); ?>
</div>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"> -->
<script src="<?php echo site_url() ?>/assets/js/file_manager.js"></script>
<style type="text/css">
	.page{background: #e0dfdf;color: black;}
	.active_page{background: #3f51b5;color: #fff;}
	label{    text-transform: initial !important;}
</style>
<script type="text/javascript"><!--
<?php if ($target) { ?>
$(document).on('click','a.thumbnail', function(e) {
	e.preventDefault();

	<?php if ($thumb) { ?>
	$('#<?php echo $thumb; ?>').find('img').attr('src', $(this).find('img').attr('src'));
	<?php } ?>

	$('#<?php echo $target; ?>').val($(this).parent().find('input').val());

	$('#filemanager').modal('hide');
});
<?php } ?>
$(document).ready(function(){
	$(document).on('click','a.directory', function(e) {
		e.preventDefault();
		$('#filemanager').load($(this).attr('href'));
	});

	$(document).on('click','.pagination a', function(e) {
		e.preventDefault();

		$('#filemanager').load($(this).attr('href'));
	});

	$(document).on('click','#button-parent', function(e) {
		e.preventDefault();

		$('#filemanager').load($(this).attr('href'));
	});

	$(document).on('click','.bread_crumb', function(e) {
		e.preventDefault();

		$('#filemanager').load($(this).attr('href'));
	});

	$(document).on('click','#button-refresh', function(e) {
		e.preventDefault();
		$('#filemanager').load($(this).attr('href'));
		//window.location.reload();
	});

	$(document).on('keydown','input[name=\'search\']', function(e) {
		if (e.which == 13) {
			$(document).find('#button-search').trigger('click');
		}
	});

	$(document).on('click','#button-search', function(e) {
		e.preventDefault();
		var url = '<?php echo site_url("filemanager/LoadManager"); ?>?token=<?php echo $token; ?>&directory='+dir;

		var filter_name = $('input[name=\'search\']').val();

		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
		}

		<?php if ($thumb) { ?>
		url += '&thumb=' + '<?php echo $thumb; ?>';
		<?php } ?>

		<?php if ($target) { ?>
		url += '&target=' + '<?php echo $target; ?>';
		<?php } ?>

		$('#filemanager').load(url);
	});

	$(document).on('click','#button-upload', function() {
		$('#form-upload').remove();

		$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file[]" value="" multiple="multiple" /></form>');

		$('#form-upload input[name=\'file[]\']').trigger('click');

		if (typeof timer != 'undefined') {
	    	clearInterval(timer);
		}

		timer = setInterval(function() {
			if ($('#form-upload input[name=\'file[]\']').val() != '') {
				clearInterval(timer);

				$.ajax({
					url: '<?php echo site_url("filemanager"); ?>/upload?directory='+dir,
					type: 'post',
					dataType: 'json',
					data: new FormData($('#form-upload')[0]),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function() {
						$('#button-upload i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
						$('#button-upload').prop('disabled', true);
					},
					complete: function() {
						$('#button-upload i').replaceWith('<i class="fa fa-upload"></i>');
						$('#button-upload').prop('disabled', false);
					},
					success: function(json) {
						$('#msg').html(json);
						if (json['error']) {
							alert_d.text = stripHtml(json['error']);
                			PNotify.error(alert_d);
						}

						if (json['success']) {
							alert_d.text = json['success'];
                			PNotify.success(alert_d);

							$('#button-refresh').trigger('click');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert_d.text =thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText;
                		PNotify.error(alert_d);	
					}
				});
			}
		}, 500);
	});

	$(document).on('click','#button-folder',function(e){
		e.preventDefault();
		OpenFolder($(this));
	})

	function OpenFolder(ths){
		
		ths.popover({
			html: true,
			placement: 'bottom',
			trigger: 'click',
			title: '<?php echo $entry_folder; ?>',
			content: function() {
				html  = '<div class="input-group">';
				html += '  <input type="text" name="folder" value="" placeholder="<?php echo $entry_folder; ?>" class="form-control">';
				html += '  <span class="input-group-btn"><button type="button" title="<?php echo $button_folder; ?>" id="button-create" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></span>';
				html += '</div>';

				return html;
			}
		});
	}

	$(document).on('shown.bs.popover','#button-folder', function() {
	
		$('#button-create').on('click',function(e) {
			e.preventDefault();
			if($('input[name=\'folder\']').val() != ''){
				$.ajax({
					url: '<?php echo site_url("filemanager"); ?>/folder?token=<?php echo $token; ?>&directory='+dir,
					type: 'post',
					dataType: 'json',
					data: 'folder=' + encodeURIComponent($('input[name=\'folder\']').val()),
					beforeSend: function() {
						$(document).find('#button-create').prop('disabled', true);
					},
					complete: function() {
						$(document).find('#button-create').prop('disabled', false);
					},
					success: function(json) {
						if (json['error']) {
							alert_d.text = stripHtml(json['error']);
                			PNotify.error(alert_d);
						}

						if (json['success']) {
							alert_d.text =json['success'];
                			PNotify.success(alert_d);

							$(document).find('#button-refresh').trigger('click');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert_d.text = thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText;
                		PNotify.error(alert_d);
					}
				});
			}else{
				alert_d.text ='Invalid folder name!';
                PNotify.error(alert_d);
			}
		});
	});

	$(document).on('click','#button_delete', function(e) {
		e.preventDefault();
		if($('input[name^=\'path\']:checked').length > 0){
			if (confirm('<?php echo $text_confirm; ?>')) {
				$.ajax({
					url: '<?php echo site_url("filemanager"); ?>/delete?token=<?php echo $token; ?>',
					type: 'post',
					dataType: 'json',
					data: $('input[name^=\'path\']:checked'),
					beforeSend: function() {
						$(document).find('#button-delete').prop('disabled', true);
					},
					complete: function() {
						$(document).find('#button-delete').prop('disabled', false);
					},
					success: function(json) {
						if (json['error']) {
							alert_d.text =json['error'];
                			PNotify.error(alert_d);
						}

						if (json['success']) {
							alert_d.text = json['success'];
                			PNotify.success(alert_d);

							$(document).find('#button-refresh').trigger('click');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert_d.text =thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText;
                		PNotify.error(alert_d);
					}
				});
			}
		}else{
			alert_d.text ='Please select file/folder to delete';
            PNotify.error(alert_d);
		}
	});

	function stripHtml(html){
   		var tmp = document.createElement("DIV");
   		tmp.innerHTML = html;
   		return tmp.textContent || tmp.innerText || "";
	}
});
//--></script>