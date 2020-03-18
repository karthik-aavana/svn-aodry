<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>



<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

    </section>

    <div class="fixed-breadcrumb">

        <ol class="breadcrumb abs-ol">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li><a href="<?php echo base_url('report/all_reports'); ?>">Report</a></li>

            <li class="active">Logs Report</li>

        </ol>

    </div>

    <!-- Main content -->

    <section class="content mt-50">

        <div class="row">

            <!-- right column -->

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">Logs Report</h3>

                    </div>



                    <!-- /.box-header  -->

                    <div class="box-body">

                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                                <tr>

                                    <th style="min-width: 50px; text-align: center;"><a class="open_distinct_modal" style="color: white !important;">Lead Date</a></th>



                                    <th style="min-width: 50px; text-align: center;"><a class="open_distinct_modal"  style="color: white !important;">Number of Leads</a></th>



                                    <th style="min-width: 50px; text-align: center;"><a class="open_distinct_modal" style="color: white !important;">Number of lead edits</a></th>

                                    

                                    <th style="min-width: 50px; text-align: center;"><a class="open_distinct_modal" style="color: white !important;">Stages</a></th>

                                </tr>

                            </thead>

                            <tbody></tbody>

                            <tfoot></tfoot>

                        </table>

                    </div>

                    <!-- /.box-body -->

                </div>

                <!-- /.box -->

            </div>

            <!--/.col (right) -->



        </div>

        <!-- /.row -->

    </section>



    <!-- /.content -->

</div>

<!-- /.content-wrapper -->



<!-- Modals starts -->

<!-- Modal -->

<div class="modal fade" id="distinct-modal" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title"><span id="modal-title"></span></h4>

            </div>

            <form id="form-filter" class="">

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12 hide">

                            <div class="form-group">

                                <select id="modal-select_type"  name="modal-select_type">

                                    <option value="">Select</option>

                                    <option value="lead_date">Lead Date</option>

                                    <option value="asr_no">Lead No.</option>

                                    <option value="customer">Customer</option>

                                    <option value="group">Group</option>

                                    <option value="stages">Stages</option>

                                    <option value="source">Source</option>

                                    <option value="business_type">Business Type</option>

                                    <option value="next_action_date">Next Action Date</option>

                                    <option value="priority">Priority</option>

                                </select>

                            </div>

                        </div>



                        <div class="col-md-12" id="from_date_div">

                            <div class="form-group">

                                <input type="text" name="filter_from_date" value="" class="form-control datepicker" id="filter_from_date" placeholder="From Date">

                            </div>

                        </div>

                        <div class="col-md-12" id="to_date_div">

                            <div class="form-group">

                                <input type="text" name="filter_to_date" value="" class="form-control datepicker" id="filter_to_date" placeholder="End date">

                            </div>

                        </div>



                        <div class="col-md-12" id="asr_no_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_asr_no"  name="filter_asr_no">

                                    <option value="" >Select</option>

                                    <?php

                                    foreach ($asr_no as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->lead_id ?>"><?= $value->asr_no ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>



                        <div class="col-md-12" id="customer_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_customer_name"  name="filter_customer_name">

                                    <option value="" >Select</option>

                                    <?php

                                    foreach ($customers as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->customer_id ?>"><?php echo $value->customer_code . ' - ' . $value->customer_name ?></option>

                                        <?php

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>



                        <div class="col-md-12" id="group_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_group"  name="filter_group">

                                    <option value="" >Select</option>

                                    <?php

                                    foreach ($group as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->lead_group_id ?>"><?= $value->lead_group_name ?></option>

                                        <?php

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>



                        <div class="col-md-12" id="stages_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_stages"  name="filter_stages">

                                    <option value="" >Select</option>

                                    <?php

                                    foreach ($stages as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->lead_stages_id ?>"><?= $value->lead_stages_name ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>





                        <div class="col-md-12" id="source_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_source"  name="filter_source">

                                    <option value="" >Select</option>

                                    <?php

                                    foreach ($source as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->lead_source_id ?>"><?= $value->lead_source_name ?></option>

                                        <?php

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>



                        <div class="col-md-12" id="business_type_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_business_type"  name="filter_business_type">

                                    <option value="" >Select</option>

                                    <?php

                                    foreach ($business_type as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->lead_business_id ?>"><?= $value->lead_business_type ?></option>

                                        <?php

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>



                        <div class="col-md-12" id="from_next_action_date_div">

                            <div class="form-group">

                                <input type="text" name="filter_from_next_action_date" value="" class="form-control datepicker" id="filter_from_next_action_date" placeholder="From Date">

                            </div>

                        </div>

                        <div class="col-md-12" id="to_next_action_date_div">

                            <div class="form-group">

                                <input type="text" name="filter_to_next_action_date" value="" class="form-control datepicker" id="filter_to_next_action_date" placeholder="End date">

                            </div>

                        </div>



                        <div class="col-md-12" id="priority_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_priority"  name="filter_priority">

                                    <option value="">Select</option>

                                    <option value="hot">Hot</option>

                                    <option value="warm">Warm</option>

                                    <option value="cold">Cold</option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button name="filter_search" type="button" class="btn btn-primary" id="filter_search" value="filter" data-dismiss="modal">Apply</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    <button type="reset" class="btn btn-danger" id="reset-all">Reset All</button>



                    <button type="button" class="btn btn-warning" id="reset-in-date">Reset Lead Date</button>

                    <button type="button" class="btn btn-warning" id="reset-asr_no">Reset Lead No</button>

                    <button type="button" class="btn btn-warning" id="reset-customer">Reset Customer</button>

                    <button type="button" class="btn btn-warning" id="reset-group">Reset Group</button>

                    <button type="button" class="btn btn-warning" id="reset-stages">Reset Stages</button>

                    <button type="button" class="btn btn-warning" id="reset-source">Reset Source</button>

                    <button type="button" class="btn btn-warning" id="reset-business_type">Reset Business Type</button>

                    <button type="button" class="btn btn-warning" id="reset-in-next_action_date">Reset Next Action Date</button>

                    <button type="button" class="btn btn-warning" id="reset-priority">Reset Priority</button>

                </div>

            </form>

        </div>



    </div>

</div>



</div>





<!-- modals ends -->

<?php

$this->load->view('layout/footer');

// $this->load->view('sales/pay_now_modal');

$this->load->view('sales/pdf_type_modal');

// $this->load->view('advance_voucher/advance_voucher_modal');

$this->load->view('general/delete_modal');

$this->load->view('recurrence/recurrence_invoice_modal');

?>



<script>

    $(document).on('click', '#reset-all', function ()

    {

        $('#filter_asr_no option:selected').prop("selected", false)

        $('#filter_from_date option:selected').prop("selected", false)

        $('#filter_to_date option:selected').prop("selected", false)

        $('#filter_customer_name option:selected').prop("selected", false)

        $('#filter_group option:selected').prop("selected", false)

        $('#filter_stages option:selected').prop("selected", false)

        $('#filter_source option:selected').prop("selected", false)

        $('#filter_business_type option:selected').prop("selected", false)

        $('#filter_from_next_action_date option:selected').prop("selected", false)

        $('#filter_to_next_action_date option:selected').prop("selected", false)

        $('#filter_priority option:selected').prop("selected", false)

        $('.select2-selection__rendered').empty();

    })



    $(document).on("click", ".open_distinct_modal", function ()

    {

        var title = $(this).data("title");

        var type = $(this).data("type");

        $(".modal-header #modal-title").html(title);

        $(".modal-body #modal-select_type").val(type);

        $('#modal-select_type').change();

    });



    $(document).ready(function () {



        $("#reset-asr_no").click(function () {

            $("#asr_no_div .select2-selection__rendered").empty();

            $('#filter_asr_no option:selected').prop("selected", false)

        })

        $("#reset-in-date").click(function () {

            $('#filter_from_date').val('');

            $('#filter_to_date').val('');

        })

        $("#reset-customer").click(function () {

            $("#customer_div .select2-selection__rendered").empty();

            $('#filter_customer_name option:selected').prop("selected", false)

        })

        $('#reset-group').click(function () {

            $("#group_div .select2-selection__rendered").empty();

            $('#filter_group option:selected').prop("selected", false)

        })

        $('#reset-stages').click(function () {

            $("#stages_div .select2-selection__rendered").empty();

            $('#filter_stages option:selected').prop("selected", false)

        })

        $('#reset-source').click(function () {

            $("#source_div .select2-selection__rendered").empty();

            $('#filter_source option:selected').prop("selected", false)

        })

        $('#reset-business_type').click(function () {

            $("#business_type_div .select2-selection__rendered").empty();

            $('#filter_business_type option:selected').prop("selected", false)

        })

        $("#reset-in-next_action_date").click(function () {

            $('#filter_from_next_action_date').val('');

            $('#filter_to_next_action_date').val('');

        })

        $('#reset-priority').click(function () {

            $("#priority_div .select2-selection__rendered").empty();

            $('#filter_priority option:selected').prop("selected", false)

        })



        $("#modal-select_type").on("change", function (event)

        {

            var type = $('#modal-select_type').val();



            if (type == "")

            {

                $("#customer_div").hide();

                $("#asr_no_div").hide();

                $("#from_date_div").hide();

                $("#to_date_div").hide();

                $("#from_next_action_date_div").hide();

                $("#to_next_action_date_div").hide();

                $("#group_div").hide();

                $("#stages_div").hide();

                $("#priority_div").hide();

                $("#source_div").hide();

                $("#receivable_date_div").hide();

                $("#business_type_div").hide();

            }



            if (type == "asr_no")

            {

                $("#asr_no_div").show();

                $('#reset-asr_no').show();

            } else

            {

                $("#asr_no_div").hide();

                $('#reset-asr_no').hide();

            }



            if (type == "lead_date")

            {

                $("#from_date_div").show();

                $("#to_date_div").show();

                $("#reset-in-date").show();

            } else

            {

                $("#from_date_div").hide();

                $("#to_date_div").hide();

                $("#reset-in-date").hide();

            }



            if (type == "customer")

            {

                $("#customer_div").show();

                $("#reset-customer").show();

            } else

            {

                $("#customer_div").hide();

                $("#reset-customer").hide();

            }



            if (type == "group")

            {

                $("#group_div").show();

                $("#reset-group").show();

            } else

            {

                $("#group_div").hide();

                $("#reset-group").hide();

            }



            if (type == "stages")

            {

                $("#stages_div").show();

                $("#reset-stages").show();

            } else

            {

                $("#stages_div").hide();

                $("#reset-stages").hide();

            }



            if (type == "source")

            {

                $("#source_div").show();

                $("#reset-source").show();



            } else

            {

                $("#source_div").hide();

                $("#reset-source").hide();

            }



            if (type == "business_type")

            {

                $("#business_type_div").show();

                $("#reset-business_type").show();



            } else

            {

                $("#business_type_div").hide();

                $("#reset-business_type").hide();

            }



            if (type == "next_action_date")

            {

                $("#from_next_action_date_div").show();

                $("#to_next_action_date_div").show();

                $("#reset-in-next_action_date").show();

            } else

            {

                $("#from_next_action_date_div").hide();

                $("#to_next_action_date_div").hide();

                $("#reset-in-next_action_date").hide();

            }



            if (type == "priority")

            {

                $("#priority_div").show();

                $("#reset-priority").show();

            } else

            {

                $("#priority_div").hide();

                $("#reset-priority").hide();

            }



        }).change();



        generateOrderTable();

        function generateOrderTable() {



            $('#list_datatable').DataTable({

                "processing": true,

                "serverSide": true,

                "responsive": true,

                "ajax": {

                    "url": base_url + "report/logs_report",

                    "dataType": "json",

                    "type": "POST",

                    "data": {

                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',

                        'filter_asr_no': $('#filter_asr_no').val(),

                        'filter_from_date': $('#filter_from_date').val(),

                        'filter_to_date': $('#filter_to_date').val(),

                        'filter_customer_name': $('#filter_customer_name').val(),

                        'filter_from_next_action_date': $('#filter_from_next_action_date').val(),

                        'filter_to_next_action_date': $('#filter_to_next_action_date').val(),

                        'filter_group': $('#filter_group').val(),

                        'filter_stages': $('#filter_stages').val(),

                        'filter_source': $('#filter_source').val(),

                        'filter_business_type': $('#filter_business_type').val(),

                        'filter_priority': $('#filter_priority').val()

                    }

                },

                "columns": [

                    {"data": "lead_date"},

                    {"data": "asr_no"},

                    {"data": "customer"},

                    {"data": "group"},

                    {"data": "stages"},

                    {"data": "source"},

                    {"data": "business_type"},

                    {"data": "next_action_date"},

                    {"data": "priority"}

                ],



                "columnDefs": [{

                        "targets": "_all",

                        "orderable": false

                    }],



                lengthMenu: [

                    [10, 50, 100, 100000],

                    ['10 rows', '50 rows', '100 rows', 'Show all']

                ],

                dom: 'Blfrtip',

                // buttons: [

                //     'csv', 'excel', 'pdf'

                // ]

                buttons: [

                    {extend: 'csv', footer: true},

                    {extend: 'excel', footer: true},

                    {extend: 'pdf', footer: true, orientation: 'landscape', exportOptions: {stripNewlines: false}}

                ]

            });



            return true;

        }





        $('#filter_search').click(function () { //button filter event click



            $("#list_datatable").dataTable().fnDestroy()

            generateOrderTable()



        });

    });





</script>

