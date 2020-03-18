<div class="modal fade" id="upload_doc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
                <h4 class="modal-title">Upload Voucher</h4>
            </div>
            <form class="" id="upload_bulk_voucher" method="post" action="<?= base_url(); ?>general_voucher/ImportVoucher" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="file" name="bulk_voucher" class="form-control file-upload-default">
                                <input type="hidden" name="voucher_type" value="<?= $voucher_type; ?>">
                                <input type="hidden" name="redirect_uri" value="<?= $redirect_uri; ?>">
                                <input type="hidden" name="company_id">
                            </div>                          
                                <div class="date_note">
                                    <p><strong>Caution!</strong> Make sure your CSV date format is <span class="csv_date_formate">dd-mm-YYYY</span> as the Invoice/Voucher date in the software will be saved as <span class="csv_date_formate">dd-mm-YYYY</span>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button class="btn btn-primary tbl-btn save_bulk">Submit</button>
                    <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>