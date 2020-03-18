<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<div class="content-wrapper">

    <section class="content">

        <div class="row">

            <?php

            if ($this->session->flashdata('email_send') == 'success') {

                ?>

                <div class="col-sm-12">

                    <div class="alert alert-success">

                        <button class="close" data-dismiss="alert" type="button">

                            ×

                        </button>

                        Email has been send with the attachment.

                        <div class="alerts-con"></div>

                    </div>

                </div>

			<?php } ?>

            <div id="holded-top">

                <div class="col-md-12">

                    <div class="box" id="plus_btn">

                        <div class="box-body">

                            <div class="btn-group">

                                <button type="button" class="btn btn-default btn-rounded dropdown-toggle" data-toggle="dropdown">

                                    <span class="fa fa-plus"></span>

                                    <span class="sr-only">Toggle Dropdown</span>

                                </button>

                                <ul class="dropdown-menu" role="menu">

								<?php

								if (in_array($advance_voucher_module_id, $active_add)) {

								    ?>

                                        <li>

                                            <a href="<?php echo base_url('advance_voucher/add'); ?>" title="Add Advance voucher">Add Advance voucher </a>

                                        </li>

                                    <?php } ?>

                                    <?php

                                    if (in_array($sales_module_id, $active_add)) {

                                        ?>

                                        <li>

                                            <a  href="<?php echo base_url('sales/add'); ?>" title="Add New Sales">Add New Sales </a>

                                        </li>

                                    <?php } ?>

                                    <?php

                                    if (in_array($receipt_voucher_module_id, $active_add)) {

                                        ?>

                                        <li>

                                            <a href="<?php echo base_url('receipt_voucher'); ?>" title="Receipt Voucher List">Receipt Vouchers </a></li>

                                        <li>

                                    <?php } ?>

                                    <?php

                                    if (in_array($recurrence_module_id, $active_view)) {

                                        ?>

                                        <li>

                                            <a href="<?php echo base_url('sales/recurrence_invoice_list'); ?>" title="Recurrence Invoice List">Recurrence Invoice List </a>

                                        <li>

                                    <?php } ?>

                                </ul>

                            </div>                           

                        </div>

                    </div>

                    <div class="box" id="filter">

                        <div class="box-body filter_body">

                            <div class="btn-group">

                                <span> <a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="View Bill"> <i class="fa fa-eye"></i> </a></span>

                                <span> <a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Receive Payment"> <i class="fa fa-money"></i> </a></span>

                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#advance_voucher"> <a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Advance Vouchers"> <i class="fa fa-book"></i> </a></span>

                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#followUp"><a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Follow Up Dates"><i class="fa fa-calendar-o"></i></a></span>

                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#pdf_type_modal"><a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>

                                <span> <a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Email Bill"> <i class="fa fa-envelope-o"></i> </a></span>

                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#recurrence_invoice"><a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Generate Recurrence Invoice"><i class="fa fa-eye"></i></a></span>

                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-path="sales/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>

                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#excess_amount"> <a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Excess Amount"> <i class="fa fa-money"></i> </a></span>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-md-12">

                    <div class="table-responsive">

                        <div class="box">

                            <div class="box-body">

                                <table id="list_datatable" class="table custom_datatable table-bordered table-striped table-hover" >

                                    <thead>

                                        <tr>

                                            <th> # </th>

                                            <th> Date</th>

                                            <th> Customer & Invoice Number</th>

                                            <th> Invoice Total</th>

                                            <th> Total Receivable</th>

                                            <th> Net Receivable</th>

                                            <th> Total Received Amount</th>

                                            <th> Total Pending Amount</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td>

                                                <input type="checkbox" name="check_item" class="form-check-input">

                                            </td>

                                            <td>10-07-2019</td>

                                            <td>Aavana Corporate | <a href="javascript:void(0)">INV-001287</a></td>

                                            <td>07</td>

                                            <td align="right">785000.00</td>

                                            <td align="right">900000.00</td>

                                            <td align="right">80000.00</td>

                                            <td align="right">4500.00</td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <div id="myModal" class="modal fade" role="dialog">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">Update Date</h4>

                </div>

                <div class="modal-body">

                    <form>

                        <div class="row">

                            <div class="col-sm-12">

                                <div class="form-group">

                                    <label for="date">Date<span class="validation-color">*</span></label>

                                    <input type="hidden" id="salesId" name="salesId" value="">

                                    <input type="hidden" name="type" id="type" value="sales">

                                    <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" readonly="">

                                    <span class="validation-color" id="err_date"></span>

                                </div>

                            </div>

                            <div class="col-sm-12">

                                <div class="form-group">

                                    <label for="date">Comments<span class="validation-color">*</span></label>

                                    <textarea class="form-control" id="comments" name="comments"></textarea>

                                    <div class="form-group text-center">

                                        <input type="submit" class="btn btn-info" id="post_notification_date" name="post_notification_date">

                                        <span class="validation-color" id="err_date"></span>

                                    </div>

                                </div>

                            </div>

                    </form>

                    <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive"></table>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

<div id="myModal2" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">

                    &times;

                </button>

                <h4 class="modal-title">Status</h4>

            </div>

            <div class="modal-body">

                <div class="alert alert-success">

                    <strong>Success!</strong> Updated Follow Up date.

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-info" data-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>



<div id="advance_voucher" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">

                    &times;

                </button>

                <h4 class="modal-title">Advance view in sales invoice</h4>

            </div>

            <div class="modal-body">                

                    <div class="box">

                        <div class="box-body">

                        	<div class="table-responsive">

                            <table id="advance_voucher_table" class="table custom_datatable table-bordered table-striped table-hover" >

                                <thead>

                                <th>Customer </th>

                                <th>Advance Voucher</th>

                                <th>Referance Invoice</th>

                                <th>Amount</th>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td>Hari Gouwda</td>

                                        <td>AD/VOU</td>

                                        <td>Sal-12</td>

                                        <td><i class="fa fa-rupee"></i> 874654654658.00</td>

                                    </tr>

                                    <tr>

                                        <td>Hari Gouwda</td>

                                        <td>AD/VOU</td>

                                        <td>Sal-12</td>

                                        <td><i class="fa fa-rupee"></i> 874654654658.00</td>

                                    </tr>

                                    <tr>

                                        <td>Hari Gouwda</td>

                                        <td>AD/VOU</td>

                                        <td>Sal-12</td>

                                        <td><i class="fa fa-rupee"></i> 874654654658.00</td>

                                    </tr>

                                    <tr>

                                        <td>Hari Gouwda</td>

                                        <td>AD/VOU</td>

                                        <td>Sal-12</td>

                                        <td><i class="fa fa-rupee"></i> 874654654658.00</td>

                                    </tr>

                                    <tr>

                                        <td>Hari Gouwda</td>

                                        <td>AD/VOU</td>

                                        <td>Sal-12</td>

                                        <td><i class="fa fa-rupee"></i> 874654654658.00</td>

                                    </tr>

                                    <tr>

                                        <td>Hari Gouwda</td>

                                        <td>AD/VOU</td>

                                        <td>Sal-12</td>

                                        <td><i class="fa fa-rupee"></i> 874654654658.00</td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <!-- <div class="modal-footer">

                <button type="button" class="btn btn-info" data-dismiss="modal">

                    Close

                </button>

            </div> -->

        </div>

    </div>

</div>

<div id="excess_amount" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">

                    &times;

                </button>

                <h4 class="modal-title">Excess Amount</h4>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label>Purpose<span class="validation-color">*</span></label>

                            <select class="form-control" id="j_voucher">

                                <option value="">Select Purpose</option>

                                <option value="advance">Advance</option>

                                <option value="income">Income</option>

                            </select>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <label>Date<span class="validation-color">*</span></label>

                        <div class="input-group date">

                            <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="2019-07-10">

                            <div class="input-group-addon">

                                <span class="fa fa-calendar"></span>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label>Voucher Number<span class="validation-color">*</span></label>

                            <input type="text" class="form-control" />

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label>Amount<span class="validation-color">*</span></label>

                            <input type="text" class="form-control" />

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label>Ledger<span class="validation-color">*</span></label>

                            <input type="text" class="form-control" />

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label>CR/DR<span class="validation-color">*</span></label>

                            <select class="form-control">

                                <option value="">Select CR/DR</option>

                                <option value="DR">DR</option>

                                <option value="CR">CR</option>

                            </select>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label>Ledger<span class="validation-color">*</span></label>

                            <input type="text" class="form-control" />

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label>CR/DR<span class="validation-color">*</span></label>

                            <select class="form-control">

                                <option value="">Select CR/DR</option>

                                <option value="DR">DR</option>

                                <option value="CR">CR</option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-info" data-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

<style>

	#advance_voucher .modal-body, #advance_voucher .box {

		padding: 4px 6px;

		box-shadow: none;

		margin-bottom: auto;

	}

	#advance_voucher .modal-body table tr td:last-child{

		text-align: right;

	}

</style>

</div>

<?php

$this->load->view('layout/footer');

// $this->load->view('sales/pay_now_modal');

$this->load->view('sales/pdf_type_modal');

// $this->load->view('advance_voucher/advance_voucher_modal');

$this->load->view('general/delete_modal');

$this->load->view('recurrence/recurrence_invoice_modal');

?>

<script>

    $(document).ready(function () {

        $('#list_datatable').DataTable({});

        $('#advance_voucher_table').DataTable({

			"fnDrawCallback" : function(oSettings) {

				var rowCount = this.fnSettings().fnRecordsDisplay();

				if (rowCount <= 10) {

					$('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').hide();

				} else {

					$('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').show();

				}

			}

        });

        $("#post_notification_date").click(function (e) {

            e.preventDefault();

            var sales_id = $('#salesId').val();

            var sales_type = $('#type').val();

            var update_date = $('#invoice_date').val();

            var comments = $('#comments').val();

            if (update_date == null || update_date == "") {

                $('#err_date').text('please Select Date');

                return false;

            }

            $.ajax({

                url: base_url + "follow_up/follow_up",

                type: "POST",

                data: {

                    'sales_id': sales_id,

                    'sales_type': sales_type,

                    'update_date': update_date,

                    'comments': comments

                },

                success: function (data) {

                    var obj = JSON.parse(data);

                    if (obj.status = 'success') {

                        $("#myModal2").modal('show');

                    }

                }

            })

        });

    });  

	

	   $('body').on('change' , 'input[type="checkbox"][name="check_item"]' , function () {

        var i = 0;

        $.each($("input[name='check_item']:checked") , function () {

            i++;

        });

        if (i == 1)

        {

            var row = $("input[name='check_item']:checked").closest("tr");

            var action_button = row.find('.action_button').html();



            $('#plus_btn').hide();

            $('.filter_body').html(action_button);

            $('#filter').show();

        }

        else

        {

            $('#plus_btn').show();

            $('#filter').hide();

            $('.filter_body').html('');

        }

    });

	



    function addToModel(id) {

        document.getElementById("salesId").value = id;

        $.ajax({

            type: "GET",

            url: base_url + "follow_up/follow/" + id,

            success: function (data) {

                $("#follow_up_table").html(data);

            }

        });

    }

</script>