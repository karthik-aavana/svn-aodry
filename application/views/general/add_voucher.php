<style>
    .round-btn .fa{
        line-height: 2.4;
    }
</style>
<div class="modal fade" id="add_voucher" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
    <div class="modal-dialog model-md modal-dialog-centered">
        <div class="modal-content">           
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
                <h4 class="modal-title">Add Voucher</h4>
            </div>
            <div class="modal-body">
                 <div id="loader">
                           <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                       </div>
                <form class="forms-sample filter">
                    <input type="hidden" name="voucher_id" value="0" id="voucher_id">
                    <div class="row">                        
                        <div class="col-sm-6">
                            <div id='voucher_type_class' class="form-group hidden">
                                <label>Voucher Type<span class="validation-color">*</span></label>
                                <select class="form-control select2" name="voucher_type" id="voucher_type">
                                    <option value="">Select Voucher Type</option>
                                    <option value="bank">Bank</option>
                                    <option value="cash">Cash</option>
                                    <option value="journal">Journal</option> 
                                    <option value="contra">Contra</option>
                                </select>
                                <span class="validation-color" id="err_vou_type"></span>
                            </div>
                            <div id='voucher_type_text_class' class="form-group">
                                <label>Voucher Type<span class="validation-color">*</span></label>
                                <input type="text" name="voucher_type_text" id="voucher_type_text" class="form-control" autocomplete="off">
                                <span class="validation-color" id="err_vou_type_text"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date">Invoice Date<span class="validation-color">*</span></label>
                                <div class="input-group date">
                                    <input type="text" name="invoice_date" class="form-control datepicker" id="invoice_date" autocomplete="off">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                </div>
                                <span class="validation-color" id="err_inv_dt"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Invoice/Particular Number</label>
                                <input type="text" name="invoice_number" id="invoice_number" class="form-control" autocomplete="off">
                                <span class="validation-color" id="err_inv_no"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Narration</label>
                                <input type="text"  name="invoice_narration" id="invoice_narration" class="form-control" id="" autocomplete="off">
                                <span class="validation-color" id="err_inv_nar"></span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row hidden">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Invoice Total<span class="validation-color">*</span></label>
                                <input type="number" name="invoice_total" id="invoice_total" class="form-control" id="" autocomplete="off">	
                                <span class="validation-color" id="err_invoice_total"></span>							
                            </div>
                        </div>
                    </div> -->
                    <div class="max-hgt">
                        <div class="row ledger_row" id="row_0">
                            <div class="col-sm-4">
                                <div class="form-group ">
                                    <label>Ledger<span class="validation-color">*</span></label>
                                    <select class="form-control select2" name="invoice_ledger" id="invoice_ledger">
                                        <option value="">Select ledger</option>
                                    </select>
                                    <span class="validation-color" id="err_ledger"></span>
                                    <span class="validation-color closing_span" id="view_closing_balance_0"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>CR/DR<span class="validation-color">*</span></label>
                                    <select class="form-control" name="amount_type" id="amount_type">
                                        <option value="">Select CR/DR</option>
                                        <option value="CR">CR</option>
                                        <option value="DR">DR</option>
                                    </select>
                                    <span class="validation-color" id="err_cr_dr"></span>
                                </div>
                            </div>
                            <div class="col-sm-3 pr">
                                <div class="form-group">
                                    <label>Amount<span class="validation-color">*</span></label>
                                    <input type="Number" min="0" name="voucher_amount" class="form-control" id="voucher_amount"  autocomplete="off">
                                </div>
                                <span class="validation-color" id="err_amount"></span>
                            </div>
                            <div class="col-sm-1 pl-0">
                                <a href="javascript:void(0)" class="btn btn-primary btn-xs round-btn mt-25" id="add_row" data-toggle="tooltip" data-placement="left" data-custom-class="tooltip-primary" data-original-title="Add More Row"><i class="fa fa-plus"></i></a> </span>
                            </div>
                            <!-- <div class="col-sm-1 pl">
                            <span class="btn btn-primary btn-xs round-btn"><a href="javascript:viod(0);" data-toggle="tooltip" data-placement="left" data-custom-class="tooltip-primary" data-original-title="Remove Row"><i class="fa fa-minus"></i></a></span>
                            </div> -->
                        </div>
                        <div class="row ledger_row" id="row_1">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select class="form-control select2" name="invoice_ledger" id="invoice_ledger">
                                        <option value="">Select ledger</option>
                                    </select>
                                    <span class="validation-color closing_span" id="view_closing_balance_1"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select class="form-control" name="amount_type" id="amount_type">
                                        <option value="">Select CR/DR</option>
                                        <option value="CR">CR</option>
                                        <option value="DR">DR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 pr">
                                <div class="form-group">
                                    <input type="Number" min="0" name="voucher_amount" class="form-control" id="voucher_amount">
                                </div>
                            </div>
                        </div>
                        <div id="append-data"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary tbl-btn" id="AddNewVoucher">
                    Add
                </button>
                <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
<div class="voucher_option1" style="display: none;"></div>
<div class="voucher_option2" style="display: none;"></div>
<script>
    var i = 1;
    $("#add_row").click(function () {
       // console.log(i, 'before');
        i++;
      //  console.log(i, 'after');
        var ledg_html = $('#add_voucher #row_1 [name=invoice_ledger]').html();

        var row = '<div class="row ledger_row" id="row_' + i + '"><div class="col-sm-4"><div class="form-group"><select class="form-control select2" name="invoice_ledger" id="invoice_ledger' + i + '">' + ledg_html + '</select><span class="validation-color closing_span" id="view_closing_balance_' + i + '"></span></div></div><div class="col-sm-4"><div class="form-group"><select class="form-control" name="amount_type" id="amount_type' + i + '"><option value="">Select CR/DR</option><option value="CR">CR</option><option value="DR">DR</option></select></div></div><div class="col-sm-3 pr"><div class="form-group"><input type="Number" min="0" name="voucher_amount" class="form-control" id="voucher_amount' + i + '" placeholder="Amount*"></div></div><div class="col-sm-1 pl-0"><a href="javascript:void(0)"  id="remove_row" class="remove_row btn btn-primary btn-xs round-btn" data-toggle="tooltip" data-placement="left" data-custom-class="tooltip-primary" data-original-title="Remove Row"><i class="fa fa-minus"></i></a></span></div></div>';

        $("#append-data").append(row);
         $(".select2").select2({
              dropdownParent: $("#add_voucher")
      });
    });

    $('body').on('shown.bs.modal', '.modal', function () {
        $(this).find('.select2').each(function () {
            var dropdownParent = $(document.body);
            if ($(this).parents('.modal.in:first').length !== 0)
                dropdownParent = $(this).parents('.modal.in:first');
            $(this).select2({
                dropdownParent: dropdownParent
            });
        });
    });

    $('body').on('shown.bs.modal', '.modal', function () {
            $("#voucher_type").select2({              
                    minimumResultsForSearch: -1
            });
    });

    /*$(document).on('click', '#remove_row', function () {
     $(this).closest('.row').fadeOut(200);
     });*/

    $(document).on('click', '.remove_row', function () {
        $(this).parents('.ledger_row').remove();
    });

    $("#AddNewVoucher").click(function () {
        //alert ("row_"+i);
        var inv_dt = $("#invoice_date").val();
        var inv_no = $("#invoice_number").val();
        var inv_nar = $("#invoice_narration").val();
        var vou_type = $("#voucher_type").val();
        var ledger = $("#invoice_ledger").val();
        var cr_dr = $("#amount_type").val();
        var invoice_total = $('#invoice_total').val();
        var amount = $("#voucher_amount").val();
        //alert("fdsf"+cr_dr);

        if (inv_dt == '' || inv_dt == null) {
            $("#err_inv_dt").text("Please Select Invoice Date");
            return false;
        } else {
            $("#err_inv_dt").text("");
        }

        /*if(invoice_total == '' || invoice_total == null){
            $('#err_invoice_total').text('Please Select Invoice invoice_total');
            return false;
        } else {
            $("#err_invoice_total").text("");
        }*/

        /*if (inv_no == '' || inv_no == null) {
         $("#err_inv_no").text("Please enter Invoice Number");
         return false;
         } else {
         $("#err_inv_no").text("");
         };*/

        /*if (inv_nar == '' || inv_nar == null) {
         $("#err_inv_nar").text("Please enter Narration.");
         return false;
         } else {
         $("#err_inv_nar").text("");
         }*/

        if (vou_type == '' || vou_type == null) {
            $("#err_vou_type").text("Please select voucher type.");
            return false;
        } else {
            $("#err_vou_type").text("");
        }
    });
    $(".datepicker").keypress(function (event) {
        event.preventDefault();
    });


    $(document).on('change','[name=invoice_ledger]',function(){
        var val = $(this).find('option:selected').attr('closing_balance');
        $(this).parent('.form-group:first').find('.closing_span').text(val);

        if(!$(this).find('option:selected').val()){
            $(this).parent('.form-group:first').find('.closing_span').text('');
        }
    });
    
</script>
<script src="<?php echo base_url('assets/'); ?>js/select2.js"></script> 