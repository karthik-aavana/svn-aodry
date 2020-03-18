<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<div class="content-wrapper">
    
    <div class="fixed-breadcrumb">

        <ol class="breadcrumb abs-ol">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li class="active">Expense Voucher</li>

        </ol>

    </div>

    <section class="content mt-50">

        <div class="row">

            <?php

            if ($this->session->flashdata('email_send') == 'success')

            {

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
                        <h3 class="box-title">Expense Voucher</h3>
                    </div>
                </div>
                <div id="filter">
                    <div class="box-body box-header with-border filter_body">
                        <div class="btn-group">
                            <span> <a href="#" class="btn btn-app view" data-toggle="tooltip" data-placement="bottom" data-original-title="View Expense Voucher"> <i class="fa fa-eye"></i> </a></span>
                            <span><a href="#" target="_blank" class="btn btn-app pdf" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>
                            <!-- <span> <a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Email Bill"> <i class="fa fa-envelope-o"></i> </a></span> -->
                            <span> <a href="javascript:void(0);" class="btn btn-app edit" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Expense Voucher" > <i class="fa fa-pencil"></i> </a></span>
                            <span class="delete_button" data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#delete_modal" data-id="" data-path="expense_bill/delete" data-return="expense_voucher" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Delete Expense Voucher"> <i class="fa fa-trash-o"></i> </a></span>
                        </div>
                    </div>
                </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                   <th width="2%">#</th>
                                    <th>Voucher Number</th>
                                    <th>Voucher Date</th>    
                                    <!-- <th>Debit Ledger</th>
                                    <th>Credit Ledger</th> -->
                                    <th>Grand Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "expense_voucher",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "check"},
                {"data": "voucher_number"},
                {"data": "voucher_date"},
                /*{"data": "invoice_number"},*/
               /* {"data": "from_account"},
                {"data": "to_account"},*/
                {"data": "grand_total"},
            ],
            "order": [[ 0, "desc" ]],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        $(document).on('click', 'input[name="check_expense"]', function() {
            if ($(this).is(":checked")) {
                $(document).find('[name=check_expense]').prop('checked',false);
                $(this).prop('checked',true);
                $('#filter .edit').attr('href',$(this).parent().find('[name=edit]').val());
                $('#filter .pdf').attr('href',$(this).parent().find('[name=pdf]').val());
                $('#filter .view').attr('href',$(this).parent().find('[name=view]').val());
                $('#filter .delete_button').attr('data-id',$(this).parent().find('[name=delete]').val());
                $('#plus_btn').hide();
                $('#filter').show();
            } else {
                $('#plus_btn').show();
                $('#filter').hide();
            }
        });
    });
</script>