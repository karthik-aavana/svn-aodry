<div class="modal fade" id="receipt_popup_view" role="dialog">
  <div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          &times;
        </button>
        <h4 class="modal-title">Customer Receipt Details</h4>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead style="color: blue">
            <tr>
              <th scope="col">Select</th>
              <th scope="col">Reference Number</th>
              <th scope="col">Total Invoice Amount</th>
              <th scope="col">Pending Amount</th>
              <th scope="col">Discount</th>
              <th scope="col">Round Off(+/-)</th>
              <th scope="col">Receipt Amount</th>
            </tr>
          </thead>
          <tbody id='receipt_data'>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary tbl-btn download_search" id="save_receipt">Save</button>
      </div>
    </div>
  </div>
</div>