<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('purchase_credit_note'); ?>">Purchase Credit Note</a></li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <?php
            if ($this->session->flashdata('email_send') == 'success') {
                ?>
                <div class="col-sm-12">
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        Email has been send with the attachment.
                        <div class="alerts-con"></div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Purchase Credit Note</h3>
                            <?php
                            if (in_array($purchase_credit_note_module_id, $active_add)) {
                                ?>
                                <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('purchase_credit_note/add'); ?>">Add Purchase Credit Note </a>
                            <?php } ?>
                        </div>
                    </div>                    
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body"></div>
                    </div>                    
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <th>Invoice Date</th>
                                    <th>Voucher Number</th>
                                    <th>Credit Note Reference Number</th>                                   
                                    <th>Supplier Name</th>
                                    <!-- <th>Supplier Credit Note No</th> -->
                                    <th>Credit Note Value</th>
                                    <!-- <th>View</th> -->
                                    <!-- <th>Amount Payable</th>
                                    <th>Amount Paid</th>
                                    <th>Balance payable</th>      -->               
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <tr>
                                    <td><input type="checkbox" name="check_item" class="form-check-input checkBoxClass"></td>
                                    <td>87854</td>
                                    <td>16-08-2019</td>
                                    <td>MAnjunath</td>
                                    <td>9562358744</td>
                                    <td>78000.00</td>                                    
                                    <td>54110.00</td>
                                    <td>50000.00</td>
                                    <td>44110.00</td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" id="old-model">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Date</h4>
            </div>
            <div class="modal-body">              
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="date">Date<span class="validation-color">*</span></label>
                                <input type="hidden" id="salesId" name="salesId" value="">
                                <input type="hidden" name="type" id="type" value="purchase_credit_note">
                                <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="" readonly="">
                                <span class="validation-color" id="err_date"></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="date">Comments<span class="validation-color">*</span></label>
                                <textarea class="form-control" id="comments" name="comments"></textarea><br>
                                <div class="form-group text-center">
                                    <input type="submit" class="btn btn-info" id="post_notification_date" name="post_notification_date">
                                    <span class="validation-color" id="err_date"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>        
        </div>
    </div>
</div>
<div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Status</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <strong>Success!</strong> Updated Follow Up date.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('layout/footer');
// $this->load->view('purchase_credit_note/pay_now_modal');
// $this->load->view('purchase_credit_note/pdf_type_modal');
$this->load->view('general/delete_modal');
?>
<script>
    /*$('#list_datatable').DataTable();*/
</script>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "iDisplayLength": 15,
            "lengthMenu": [ [15, 25, 50,100, -1], [15, 25, 50,100, "All"] ],
            "ajax": {
                "url": base_url + "purchase_credit_note",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "date"},
                {"data": "invoice_number"},
                {"data": "reference_invoice_number"},
                {"data": "supplier"},
                /*{"data": "credit_note_number"},*/
                {"data": "grand_total"},
                /*{"data": "purchase_cn_voucher_view"},*/
                        /*{"data": "grand_total"},
                         {"data": "grand_total"},
                         {"data": "grand_total"},*/
                        /*{"data": "invoice_status"},*/
                        // {"data": "added_user"},                
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        <?php 
        $purchase_cn_success = $this->session->flashdata('purchase_cn_success');
        $purchase_cn_error = $this->session->flashdata('purchase_cn_error');
        ?>
        var alert_success = '<?= $purchase_cn_success; ?>';
        var alert_failure = '<?= $purchase_cn_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
</script>
