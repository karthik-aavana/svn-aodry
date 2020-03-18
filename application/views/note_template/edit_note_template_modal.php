<div id="edit_note_template_modal" class="modal fade" role="dialog" data-backdrop="static">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4>Edit Note Template</h4>
         </div>
         <form role="form" id="form_note" method="post">
            <div class="modal-body">
               <div id="loader_coco">
                        <h1 class="ml8">
                            <span class="letters-container">
                                <span class="letters letters-left">
                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                </span>
                            </span>
                            <span class="circle circle-white"></span>
                            <span class="circle circle-dark"></span>
                            <span class="circle circle-container">
                            <span class="circle circle-dark-dashed"></span>
                            </span></h1>
                    </div>
               <div class="row">
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label for="hash_tag">Hash Tag (#)<span style="color:red;">*</span></label>
                        <input type="hidden" id='id' name="id" value="">
                        <input type="text" class="form-control" id="edit_hash_tag" placeholder="#tag" name="edit_hash_tag" value="" readonly>
                        <span class="validation-color" id="err_edit_hash_tag"><?php echo form_error('title'); ?></span>
                        <span style="color: green;" id="edit_msg_hash_tag"><?php echo form_error('title'); ?></span>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label for="title">Title<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" placeholder="Title" id="edit_title" name="edit_title" value="">
                        <span class="validation-color" id="err_edit_title"><?php echo form_error('title'); ?></span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-12">
                     <div class="form-group">
                        <label for="content">Content<span style="color:red;">*</span></label>
                        <textarea class="form-control" id="edit_content" name="edit_content" rows="5"></textarea>
                        <span class="validation-color" id="err_edit_content"><?php echo form_error('content'); ?></span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-info btn-flat" id="template_submit" type="button">Update</button>  
            </div>
         </form>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).on("click", ".edit_note_template", function() {
      var id = $(this).data('id');
      $.ajax({
         url : base_url + 'note_template/edit_get_note_template/' + id,
         dataType : 'JSON',
         method : 'POST',
         success : function(result) {
             var content = result[0].content;
            var content_new = content.replace(/\r\n|\\r\\n|\\n|\n/g, '&#10;')
            $('#id').val(result[0].note_template_id);
            $('#edit_hash_tag').val(result[0].hash_tag);
            $('#edit_title').val(result[0].title);
            $('#edit_content').html(content_new);

         }
      });
   });
</script>
<script src="<?php echo base_url('assets/js/note_template/') ?>edit_note_template.js"></script>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script> 