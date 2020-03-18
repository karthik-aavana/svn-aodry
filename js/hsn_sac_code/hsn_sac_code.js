$("table.product_table").on("click", "span.apply", function (event)
{
    var row = $(this).closest("tr");
    var code = +row.find('#accounting_code').text();
    $('#hsn_sac_code').val(code)
});
$("table.product_table1").on("click", "span.apply1", function (event)
{
    var row = $(this).closest("tr");
    var code = +row.find('#accounting_code1').text();
    $('#hsn_sac_code').val(code)
});
$('#chapter').change(function ()
{
    var id = $(this).val();
    $.ajax(
            {
                url: base_url + 'product/get_hsn_data',
                method: "POST",
                dataType: "JSON",
                data: {id: id},
                success: function (data)
                {
                    var table = $('#index1').DataTable();
                    table.destroy();
                    $('#product_table_body').empty();
                    for (i = 0; i < data.length; i++)
                    {
                        var newRow = $("<tr>");
                        var cols = "";
                        cols += "<td></td>";
                        cols += "<td><span id='accounting_code1'>" + data[i].itc_hs_codes + "</span></td>";
                        cols += "<td>" + data[i].description + "</td>";
                        cols += "<td align='center'><span class='btn btn-info apply1' class='close' data-dismiss='modal'>Apply</span></td>";
                        cols += "</tr>";
                        newRow.append(cols);
                        $("table.product_table1").append(newRow)
                    }
                    $(document).ready(function ()
                    {
                        var t = $('#index1').DataTable(
                                {
                                    "columnDefs": [
                                        {
                                            "searchable": !1,
                                            "orderable": !1,
                                            "targets": 0
                                        }],
                                    "order": [
                                        [1, 'asc']
                                    ]
                                });
                        t.on('order.dt search.dt', function ()
                        {
                            t.column(0,
                                    {
                                        search: 'applied',
                                        order: 'applied'
                                    }).nodes().each(function (cell, i)
                            {
                                cell.innerHTML = i + 1
                            })
                        }).draw()
                    })
                }
            })
})
