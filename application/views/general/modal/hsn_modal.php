<div class="modal fade" data-backdrop="false" id="hsn_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>HSN/SAC Lookup</h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <ul class="nav nav-tabs">
                                <li>
                                    <a href="#hsn" data-toggle="tab">HSN</a>
                                </li>
                                <li class="active">
                                    <a href="#sac" data-toggle="tab">SAC</a>
                                </li>
                            </ul>
                            <br>
                            <div class="tab-content">
                                <div class="tab-pane" id="hsn">
                                    <div class="form-group">
                                        Chapter
                                        <select class="form-control select2" id="chapter" name="chapter" style="width: 40%;">
                                            <?php
                                            foreach ($chapter as $row)
                                            {
                                                echo "<option value='$row->chapter_id'> Chapter $row->chapter_id $row->chapter</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <table id="index1" class="table table-bordered table-striped product_hsn_table1">
                                        <thead>

                                        <th>HSN/SAC Code</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                        </thead>
                                        <tbody id="product_hsn_table_body">
                                            <?php
                                            foreach ($hsn as $value)
                                            {
                                                ?>
                                                <tr>
                                                    <td><span id="accounting_code1"><?php echo $value->itc_hs_codes ?></span></td>
                                                    <td><?php echo $value->description ?></td>
                                                    <td align="center"><span class="btn btn-info apply1" class="close" data-dismiss="modal">Apply</span></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane active" id="sac">
                                    <table id="index" class="table table-bordered table-striped product_hsn_table">
                                        <thead>

                                        <th>SAC Code</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                        <thead>
                                        <tbody>
                                            <?php
                                            foreach ($sac as $value)
                                            {
                                                ?>
                                                <tr>

                                                    <td><span id="accounting_code"><?php echo $value->accounting_code ?></span></td>
                                                    <td><?php echo $value->description ?></td>
                                                    <td align="center"><span class="btn btn-info apply" class="close" data-dismiss="modal">Apply</span></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /controls -->
                </div> <!-- /control-group -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('product_close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $("table.product_hsn_table").on("click", "span.apply", function (event) {
        var row = $(this).closest("tr");
        var code = +row.find('#accounting_code').text();
        $('#hsn_sac_code').val(code);
        // $('#hsn_modal').modal('hide');
    });
    $("table.product_hsn_table1").on("click", "span.apply1", function (event) {
        var row = $(this).closest("tr");
        var code = +row.find('#accounting_code1').text();
        $('#hsn_sac_code').val(code);
        // $('#hsn_modal').modal('hide');
    });
    $('#chapter').change(function () {
        var id = $(this).val();
        $.ajax({
            url: "<?php echo base_url('product/getHsnData') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var table = $('#index1').DataTable();
                table.destroy();
                $('#product_hsn_table_body').empty();
                for (i = 0; i < data.length; i++) {
                    var newRow = $("<tr>");
                    var cols = "";
                    cols += "<td><span id='accounting_code1'>" + data[i].itc_hs_codes + "</span></td>";
                    cols += "<td>" + data[i].description + "</td>";
                    cols += "<td align='center'><span class='btn btn-info apply1' class='close' data-dismiss='modal'>Apply</span></td>";
                    cols += "</tr>";
                    newRow.append(cols);
                    $("table.product_hsn_table1").append(newRow);
                }
                $(document).ready(function () {
                    var t = $('#index1').DataTable({
                        "columnDefs": [{
                                "searchable": false,
                                "orderable": false,
                                "targets": 0
                            }],
                        "order": [[1, 'asc']]
                    });
                });
            }
        });
    });
</script>
