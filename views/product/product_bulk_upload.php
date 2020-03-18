<div class="modal fade" id="upload_doc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
                <h4 class="modal-title">Upload Product</h4>
            </div>
            <form class="" id="upload_bulk_product" method="post" action="<?= base_url(); ?>Product/add_bulk_upload_product" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="file" id='bulk_product' name="bulk_product" class="form-control file-upload-default">
                                <span class="validation-color" id="err_bulk_product"></span>
                                <!-- <input type="hidden" name="voucher_type" value="<?= $voucher_type; ?>">
                                <input type="hidden" name="redirect_uri" value="<?= $redirect_uri; ?>">
                                <input type="hidden" name="company_id"> -->
                            </div>                          
                                <div class="date_note">
                                    <p><strong>Caution!</strong> Make sure your CSV Contain All the Data Required!.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button id='add_bulk_product' class="btn btn-primary tbl-btn save_bulk">Submit</button>
                    <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#add_bulk_product').click(function(){
    if(document.getElementById("bulk_product").files.length == 0 ){
        $('#err_bulk_product').text('Please Select File!');
        return false;
}
})
</script>