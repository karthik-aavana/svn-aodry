<div class="modal fade" id="upload_customer_doc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
                <h4 class="modal-title">Upload Customer</h4>
            </div>
            <form class="" id="upload_bulk_dervice" method="post" action="<?= base_url(); ?>Customer/add_bulk_upload_customer" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="file" id='bulk_customer' name="bulk_customer" class="form-control file-upload-default">
                                <span class="validation-color" id="err_bulk_customer"></span>
                                
                            </div>                          
                                <div class="date_note">
                                    <p><strong>Caution!</strong> Make sure your CSV Contain All the Data Required!.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button id='add_bulk_customer' class="btn btn-primary tbl-btn save_bulk">Submit</button>
                    <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#add_bulk_customer').click(function(){
    if(document.getElementById("bulk_customer").files.length == 0 ){
        $('#err_bulk_customer').text('Please Select File!');
        return false;
}
})
</script>