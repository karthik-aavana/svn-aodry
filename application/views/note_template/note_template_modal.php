<div id="note_template_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4><!-- <?php echo $this->lang->line('product_hsn_sac_lookup'); ?> -->
                    Add Note Template
                </h4>
            </div>
        <form id="templateForm">
            <div class="modal-body">
                <div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
             <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="hash_tag">Hash Tag (#)<span style="color:red;">*</span></label>
                            <input type="text" class="form-control" id="hash_tag" placeholder="#tag" name="hash_tag" value="<?php echo set_value('hash_tag'); ?>">
                            <span class="validation-color" id="err_hash_tag"><?php echo form_error('title'); ?></span>
                            <span style="color: green;" id="msg_hash_tag"><?php echo form_error('title'); ?></span>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="title">Title<span style="color:red;">*</span></label>
                            <input type="text" class="form-control" placeholder="Title" id="title" name="title" value="<?php echo set_value('title'); ?>">
                            <span class="validation-color" id="err_title"><?php echo form_error('title'); ?></span>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="content">Content<span style="color:red;">*</span></label>
                            <textarea class="form-control" id="content" name="content"></textarea>
                            <span class="validation-color" id="err_content"><?php echo form_error('content'); ?></span>
                        </div>
                    </div>
                </div>
                </form>                
                    <div class="modal-footer">                        
                        <button type="button" id="template_modal_submit" class="btn btn-info" class="close"> Add </button>
                        <button type="button" class="btn btn-submit" class="close" data-dismiss="modal">Cancel</button>
                    </div>
            </div>
        </div>
    </div>
     
<script src="<?php echo base_url('assets/js/note_template/') ?>note_template.js"></script>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script> 
<script type="text/javascript">
    $(document).ready(function () {
        $("#template_modal_submit").click(function (event) {
            // var name_regex = /^[a-zA-Z\s]+$/;
            var hash_tag = $('#hash_tag').val();
            var title = $('#title').val();
            var content = $('#content').val();
            if (hash_tag == null || hash_tag == "")
            {
                $("#err_hash_tag").text("Please Enter Hash tag");
                $('#msg_hash_tag').text("");
                return false;
            } else if ($('#err_hash_tag').text() == 'Please Enter Hash tag')
            {
                $('#err_hash_tag').text("");
            }
            if (title == null || title == "")
            {
                $("#err_title").text("Please Enter Title");
                return false;
            } else
            {
                $('#err_title').text("");
            }
            if (content == null || content == "")
            {
                $("#err_content").text("Please Enter Content");
                return false;
            } else
            {
                $("#err_content").text("");
            }
            if ($('#err_hash_tag').text() != '')
            {
                return false;
            }
            $.ajax({
                url: base_url + "note_template/add_note_template_modal/",
                type: "POST",
                dataType: "json",
                data: {hash_tag: $("#hash_tag").val(), title: $("#title").val(), content: $("#content").val()},
                beforeSend: function(){
                     // Show image container
                    $("#loader").show();
                }, 
                success: function (data)
                {
                    if(data.flag){
                        $("#loader").hide();
                        $('#note_template_modal').modal('hide');
                        // $("#loader").hide();
                        dTable.destroy();
                        dTable = getAllNoteTemplate();
                        alert_d.text = data.msg;
                        PNotify.success(alert_d);
                        $('#templateForm').trigger("reset");
                        $('#msg_hash_tag').text('');
                    }else{
                        $("#loader").hide();    
                        $('#note_template_modal').modal('hide');
                        alert_d.text = data.msg;
                        PNotify.error(alert_d);
                    }
                    //alert('Template Added Successfully.');
                }
            });
        });
    });
</script>
