<div class="modal fade" id="receipt_popup_view" role="dialog">
  <div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          &times;
        </button>
        <h4 class="modal-title">Customer Receipt Details 
          
          <button class="btn auto_set" style="background: #dcd9d9; color: black;float: right;
    margin-right: 20px;">Auto Set</button>
          
        </h4>
        
      </div>
      <div class="modal-body">
        <div id="loader">
            <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
        </div>
        <table class="table table-bordered" id="receipt_data">
          <thead style="color: blue">
            <tr>
              <th scope="col">Select</th>
              <th scope="col">Reference Number</th>
              <th scope="col">Total Invoice Amount</th>
              <th scope="col">Discount</th>
              <th scope="col">Round Off(+/-)</th>
              <th scope="col">Receipt Amount</th>
              <th scope="col">Pending Amount</th>
            </tr>
          </thead>
          <?php
            if(!empty($receipt_ids)){
              $i = $inv_row_count = 1;
              $tbody = '';
              foreach ($receipt_ids as $key => $value) { 
                $row = '';
                if(!empty($opening_balance)){
                  if(@$opening_balance[$value->receipt_id]){
                    $inv_row_count++;
                    $opening_balance_ary = $opening_balance[$value->receipt_id];
                    $row .= '<tr class="inv_row_count" row="'.$inv_row_count.'"><td class="reference_number_div"><input type="hidden" name="opening_balance_id" value="'.$opening_balance_ary['id'].'"><input type="checkbox" name="reference_number" id="opening_balance" class="receipt_voucher_number" checked value="opening_balance"></td><td colspan="1">Opening Balance</td><td>'.precise_amount($opening_balance_ary['opening_balance']).'</td><td colspan="4"><input type="text" class="form-control number_only" id="receipt_amount_'.$inv_row_count.'" name="receipt_amount" data-id="'.$value->party_id.'" ledger_id="'.$opening_balance_ary['ledger_id'].'" paid_amount="'.precise_amount($opening_balance_ary['paid_amount']).'"  pending_amount="'.precise_amount($opening_balance_ary['pending_amount']).'" blnc="'.precise_amount($opening_balance_ary['opening_balance']).'" value="'.precise_amount($opening_balance_ary['paid_amount']).'" autocomplete="off"></td></tr>';
                  }
                }
                foreach ($data[$value->receipt_id] as $key => $ref) {
                  
                  if($ref->reference_id != 0){
                    $row .= '<tr class="inv_row_count '.($ref->is_edit == '0' ? 'editable_disable' : '').'" row="'.$inv_row_count.'">
                                <td class="reference_number_div"><input type="checkbox" name="reference_number" id="'.$ref->reference_id.'"  class="receipt_voucher_number" checked value="'.$ref->reference_id.'"><input type="hidden" class="form-control" id="old_receipt_amount_'.$inv_row_count.'" name="old_receipt_amount"  value="'.($ref->invoice_receipt_amount != '' ? precise_amount($ref->invoice_receipt_amount) : '0.00').'" ><input type="hidden" name="is_edit" value="'.$ref->is_edit.'"></td>
                                 <td>'.$ref->reference_number.'</td>
                                 <td>'.precise_amount($ref->invoice_total).'</td>
                                 <td class="discount_div"><input type="text" class="form-control number_only" id="discount_'.$inv_row_count.'" data-id="'.$ref->reference_id.'" name="discount" value="'.($ref->discount != '' ? precise_amount($ref->discount) : '0.00').'"></td>
                                 <td class="round_off_div">
                                  <div class="input-group"><input type="hidden" name="icon_round_off" value="'.($ref->round_off_icon == 'plus' ? 'plus' : 'minus').'"><span class="input-group-addon toggle_plus" id="Input_addon_roundoff" name="Round Off" style="width:20px;"><i id="roundoff_button" class="fa '.($ref->round_off_icon == 'plus' ? 'fa-plus' : 'fa-minus').'"></i></span><input type="text" class="form-control number_only" name="round_off" id="round_off_'.$inv_row_count.'" data-id="'.$ref->reference_id.'" stlye="float: left;" value="'.($ref->round_off != '' ? precise_amount($ref->round_off) : '0.00').'"></div><span class="validation-color" id="err_grand_total_'.$inv_row_count.'"></span></td><td class="receipt_amount_div"><input type="text" class="form-control number_only" id="receipt_amount_'.$inv_row_count.'" name="receipt_amount" data-id="'.$ref->reference_id.'" value="'.precise_amount($ref->invoice_receipt_amount).'"><input type="hidden" class="form-control number_only" id="paid_amount_'.$inv_row_count.'" class="form-control" name="paid_amount" value="'.precise_amount($ref->invoice_paid_amount).'" readonly=""><input type="hidden" class="form-control" id="remaining_amount_'.$inv_row_count.'" name="remaining_amount" value="'.$ref->invoice_pending.'"></td><input type="hidden" class="form-control" id="reference_number_text_'.$inv_row_count.'" name="reference_number_text" value="'.$ref->reference_number.'"><input type="hidden" class="form-control" id="current_paid_amount_'.$inv_row_count.'" name="current_paid_amount" value="'.precise_amount($ref->invoice_paid_amount).'"><input type="hidden" class="form-control" id="current_invoice_amount_'.$inv_row_count.'" name="current_invoice_amount" value="'.precise_amount($ref->invoice_total).'"><input type="hidden" class="form-control" id="old_receipt_amount_'.$inv_row_count.'" name="old_receipt_amount" value="'.($ref->invoice_receipt_amount != '' ? precise_amount($ref->invoice_receipt_amount) : '0.00').'"><input type="hidden" class="form-control" id="last_receipt_amount_'.$inv_row_count.'" name="last_receipt_amount" value="'.($ref->invoice_receipt_amount != '' ? precise_amount($ref->invoice_receipt_amount) : '0.00').'"><td><input type="text" value="'.($ref->invoice_pending != '' ? precise_amount($ref->invoice_pending) : '0.00').'" id="pending_amount_'.$inv_row_count.'" disabled="disabled" name="pending_amount"><input type="hidden" class="float_number form-control" id="invoice_total_'.$inv_row_count.'" name="invoice_total" value="'.($ref->invoice_total != '' ? precise_amount($ref->invoice_total) : '0.00').'"></td></tr>';
                    $inv_row_count++;
                    /*$sales_data['sales_'.$value->reference_id]['sales_grand_total'] = $sales->customer_payable_amount;*/
                    if($ref->round_off_icon == 'plus'){
                      $sales_data['sales_'.$ref->reference_id]->sales_paid_amount -= ((float)$ref->invoice_receipt_amount + (float)$ref->discount);
                      
                    }else{
                      $sales_data['sales_'.$ref->reference_id]->sales_paid_amount -= ((float)$ref->invoice_receipt_amount + (float)$ref->discount + (float)$ref->round_off);
                    }
                  }
                }
                if(!empty($excess_data)){
                  if(@$excess_data[$value->receipt_id]){
                    $excess = $excess_data[$value->receipt_id];
                    $row .= '<tr class="inv_row_count" row="'.$inv_row_count.'"><td class="reference_number_div"><input type="hidden" name="excess_sales_id" id="excess_sales_id" value="'.$excess['excess_sales_id'].'"><input type="checkbox" name="reference_number" id="excess_amount"  class="receipt_voucher_number" checked value="excess_amount"></td><td colspan="2">Excess Amount</td><td colspan="4"><input type="text" class="form-control number_only" id="receipt_amount_'.$inv_row_count.'" name="receipt_amount" data-id="'.$value->party_id.'" value="'.precise_amount($excess['excess_amount']).'" autocomplete="off"></td></tr>';
                  }
                }
                $tbody .= "<tbody id='receipt_data_".$value->party_id."' cust_id='".$value->party_id."' style='display:none;'>".$row."</tbody>";
                ?>
              <?php $i++; }
              echo $tbody;
            }
            ?>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary tbl-btn download_search" id="save_invoice">Save</button>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
    .table>tbody>tr>td{vertical-align: inherit;}
</style>