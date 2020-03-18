$(function () {
    var closing_balance = $('#closing_balance').text();
    if (closing_balance != undefined && closing_balance != '' && closing_balance != '--')
    {
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: base_url + 'schedule_note/closing_balance/',
            data: {bal: closing_balance},
            success: function (data) {
            }
        });
    }
    var content_id = 0;
    var ledger_id = 0;
    var text;
    var calid;
    var val5, val6, var7, val8, val9, val10, val10_1, val10_2, val10_3, val11, val12, val13, val14, val15, val16, val17, val18;
    var val56, val57, val58, val59, val60, val61;
    $("#schedule_note_table td").dblclick(function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        $this = $(this);
        if ($this.data('editing'))
            return;
        var val = $this.text();
        text = '';
        if (val.search('Equity Shares of Rs.10/- each') != -1)
        {
            val = val.replace(' Equity Shares of Rs.10/- each', '');
            text = ' Equity Shares of Rs.10/- each';
        } else
        {
            text = '';
        }
        content_id = $(this).data('val');
        if (content_id == undefined)
        {
            ledger_id = $(this).data('id');
        } else
        {
            ledger_id = 0;
        }

        calid = $(this).data('id');

        $this.empty();
        $this.data('editing', true);
        $('<input type="text" class="form-control">').val(val).appendTo($this);
    });
    putOldValueBack = function () {
        $("#schedule_note_table .form-control").each(function () {
            $this = $(this);
            var val1 = $this.val();

            if (ledger_id != 0)
            {
                var content1 = 'ledger_title';
                content_id = ledger_id;
            } else
            {
                var content1 = $('#schedule_content').val();
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: base_url + 'schedule_note/edit_content/',
                data: {id: content_id, val: val1, content: content1},
                success: function (data) {
                    // $this.val(val1);
                    text1.text(val1);
                }
            });

            if (calid == 'c6')
            {
                val5 = $('.cal5').text();
                val6 = val1;
                if (!$.isNumeric(val5))
                {
                    val5 = 0;
                }
                if (!$.isNumeric(val6))
                {
                    val6 = 0;
                }
                val7 = (parseFloat(val5) - parseFloat(val6));
                val8 = $('.cal8').text();
                if (!$.isNumeric(val8))
                {
                    val8 = 0;
                }
                val9 = (parseFloat(val7) - parseFloat(val8));
                val10_1 = $('.cal10_1').text();
                val10_2 = $('.cal10_2').text();
                val10_3 = $('.cal10_3').text();
                if (!$.isNumeric(val10_1))
                {
                    val10_1 = 0;
                }
                if (!$.isNumeric(val10_2))
                {
                    val10_2 = 0;
                }
                if (!$.isNumeric(val10_3))
                {
                    val10_3 = 0;
                }
                val10 = (parseFloat(val10_1) + parseFloat(val10_2) + parseFloat(val10_3));
                val11 = (parseFloat(val9) - parseFloat(val10));
                var share_amt = $('.share_amt').val();
                val13 = $('.cal13').text();
                val14 = $('.cal14').text();
                if (!$.isNumeric(share_amt))
                {
                    share_amt = 0;
                }
                if (!$.isNumeric(val13))
                {
                    val13 = 0;
                }
                if (!$.isNumeric(val14))
                {
                    val14 = 0;
                }
                val15 = (parseFloat(val13) - parseFloat(val14));
                val16 = (parseFloat(val11) + parseFloat(val15));
                val17 = (parseFloat(val16) / parseFloat(share_amt));
                val18 = (parseFloat(val16) / parseFloat(share_amt));
            }
            if (calid == 'c8')
            {
                val7 = $('.cal7').text();
                val8 = val1;
                if (!$.isNumeric(val7))
                {
                    val7 = 0;
                }
                if (!$.isNumeric(val8))
                {
                    val8 = 0;
                }
                val9 = (parseFloat(val7) - parseFloat(val8));
                val10_1 = $('.cal10_1').text();
                val10_2 = $('.cal10_2').text();
                val10_3 = $('.cal10_3').text();
                if (!$.isNumeric(val10_1))
                {
                    val10_1 = 0;
                }
                if (!$.isNumeric(val10_2))
                {
                    val10_2 = 0;
                }
                if (!$.isNumeric(val10_3))
                {
                    val10_3 = 0;
                }
                val10 = (parseFloat(val10_1) + parseFloat(val10_2) + parseFloat(val10_3));
                val11 = (parseFloat(val9) - parseFloat(val10));
                var share_amt = $('.share_amt').val();
                val13 = $('.cal13').text();
                val14 = $('.cal14').text();
                if (!$.isNumeric(share_amt))
                {
                    share_amt = 0;
                }
                if (!$.isNumeric(val13))
                {
                    val13 = 0;
                }
                if (!$.isNumeric(val14))
                {
                    val14 = 0;
                }
                val15 = (parseFloat(val13) - parseFloat(val14));
                val16 = (parseFloat(val11) + parseFloat(val15));
                val17 = (parseFloat(val16) / parseFloat(share_amt));
                val18 = (parseFloat(val16) / parseFloat(share_amt));
            }
            if (calid == 'c10_1')
            {
                val9 = $('.cal9').text();
                val10_1 = val1;
                val10_2 = $('.cal10_2').text();
                val10_3 = $('.cal10_3').text();
                if (!$.isNumeric(val10_1))
                {
                    val10_1 = 0;
                }
                if (!$.isNumeric(val10_2))
                {
                    val10_2 = 0;
                }
                if (!$.isNumeric(val10_3))
                {
                    val10_3 = 0;
                }
                val10 = (parseFloat(val10_1) + parseFloat(val10_2) + parseFloat(val10_3));
                val11 = (parseFloat(val9) - parseFloat(val10));
                var share_amt = $('.share_amt').val();
                val13 = $('.cal13').text();
                val14 = $('.cal14').text();
                if (!$.isNumeric(share_amt))
                {
                    share_amt = 0;
                }
                if (!$.isNumeric(val13))
                {
                    val13 = 0;
                }
                if (!$.isNumeric(val14))
                {
                    val14 = 0;
                }
                val15 = (parseFloat(val13) - parseFloat(val14));
                val16 = (parseFloat(val11) + parseFloat(val15));
                val17 = (parseFloat(val16) / parseFloat(share_amt));
                val18 = (parseFloat(val16) / parseFloat(share_amt));
            }
            if (calid == 'c10_2')
            {
                val9 = $('.cal9').text();
                val10_1 = $('.cal10_1').text();
                val10_2 = val1;
                val10_3 = $('.cal10_3').text();
                if (!$.isNumeric(val10_1))
                {
                    val10_1 = 0;
                }
                if (!$.isNumeric(val10_2))
                {
                    val10_2 = 0;
                }
                if (!$.isNumeric(val10_3))
                {
                    val10_3 = 0;
                }
                val10 = (parseFloat(val10_1) + parseFloat(val10_2) + parseFloat(val10_3));
                val11 = (parseFloat(val9) - parseFloat(val10));
                var share_amt = $('.share_amt').val();
                val13 = $('.cal13').text();
                val14 = $('.cal14').text();
                if (!$.isNumeric(share_amt))
                {
                    share_amt = 0;
                }
                if (!$.isNumeric(val13))
                {
                    val13 = 0;
                }
                if (!$.isNumeric(val14))
                {
                    val14 = 0;
                }
                val15 = (parseFloat(val13) - parseFloat(val14));
                val16 = (parseFloat(val11) + parseFloat(val15));
                val17 = (parseFloat(val16) / parseFloat(share_amt));
                val18 = (parseFloat(val16) / parseFloat(share_amt));
            }
            if (calid == 'c10_3')
            {
                val9 = $('.cal9').text();
                val10_1 = $('.cal10_1').text();
                val10_2 = $('.cal10_2').text();
                val10_3 = val1;
                if (!$.isNumeric(val10_1))
                {
                    val10_1 = 0;
                }
                if (!$.isNumeric(val10_2))
                {
                    val10_2 = 0;
                }
                if (!$.isNumeric(val10_3))
                {
                    val10_3 = 0;
                }
                val10 = (parseFloat(val10_1) + parseFloat(val10_2) + parseFloat(val10_3));
                val11 = (parseFloat(val9) - parseFloat(val10));
                var share_amt = $('.share_amt').val();
                val13 = $('.cal13').text();
                val14 = $('.cal14').text();
                if (!$.isNumeric(share_amt))
                {
                    share_amt = 0;
                }
                if (!$.isNumeric(val13))
                {
                    val13 = 0;
                }
                if (!$.isNumeric(val14))
                {
                    val14 = 0;
                }
                val15 = (parseFloat(val13) - parseFloat(val14));
                val16 = (parseFloat(val11) + parseFloat(val15));
                val17 = (parseFloat(val16) / parseFloat(share_amt));
                val18 = (parseFloat(val16) / parseFloat(share_amt));
            }
            if (calid == 'c13')
            {
                var share_amt = $('.share_amt').val();
                val11 = $('.cal11').text();
                val13 = val1;
                val14 = $('.cal14').text();
                if (!$.isNumeric(share_amt))
                {
                    share_amt = 0;
                }
                if (!$.isNumeric(val11))
                {
                    val11 = 0;
                }
                if (!$.isNumeric(val13))
                {
                    val13 = 0;
                }
                if (!$.isNumeric(val14))
                {
                    val14 = 0;
                }
                val15 = (parseFloat(val13) - parseFloat(val14));
                val16 = (parseFloat(val11) + parseFloat(val15));
                val17 = (parseFloat(val16) / parseFloat(share_amt));
                val18 = (parseFloat(val16) / parseFloat(share_amt));
            }
            if (calid == 'c14')
            {
                var share_amt = $('.share_amt').val();
                val11 = $('.cal11').text();
                val13 = $('.cal13').text();
                val14 = val1;
                if (!$.isNumeric(share_amt))
                {
                    share_amt = 0;
                }
                if (!$.isNumeric(val11))
                {
                    val11 = 0;
                }
                if (!$.isNumeric(val13))
                {
                    val13 = 0;
                }
                if (!$.isNumeric(val14))
                {
                    val14 = 0;
                }
                val15 = (parseFloat(val13) - parseFloat(val14));
                val16 = (parseFloat(val11) + parseFloat(val15));
                val17 = (parseFloat(val16) / parseFloat(share_amt));
                val18 = (parseFloat(val16) / parseFloat(share_amt));
            }
            if (calid == 'c56')
            {
                val56 = val1;
                val58 = $('.cal58').text();
                if (!$.isNumeric(val56))
                {
                    val56 = 0;
                }
                if (!$.isNumeric(val58))
                {
                    val58 = 0;
                }
                val60 = (parseFloat(val56) + parseFloat(val58));
            }
            if (calid == 'c58')
            {
                val56 = $('.cal56').text();
                val58 = val1;
                if (!$.isNumeric(val56))
                {
                    val56 = 0;
                }
                if (!$.isNumeric(val58))
                {
                    val58 = 0;
                }
                val60 = (parseFloat(val56) + parseFloat(val58));
            }
            if (calid == 'c57')
            {
                val57 = val1;
                val59 = $('.cal59').text();
                if (!$.isNumeric(val57))
                {
                    val57 = 0;
                }
                if (!$.isNumeric(val59))
                {
                    val59 = 0;
                }
                val61 = (parseFloat(val57) + parseFloat(val59));
            }
            if (calid == 'c59')
            {
                val57 = $('.cal57').text();
                val59 = val1;
                if (!$.isNumeric(val57))
                {
                    val57 = 0;
                }
                if (!$.isNumeric(val59))
                {
                    val59 = 0;
                }
                val61 = (parseFloat(val57) + parseFloat(val59));
            }

            if (val6 != undefined)
            {
                $('.cal7').empty().html((val7).toFixed(2)).data('editing', false);
                // $('.cal7').text(val7);
            }
            if (val8 != undefined)
            {
                $('.cal9').empty().html((val9).toFixed(2)).data('editing', false);
                // $('.cal9').text(val9);
            }
            if (val10_1 != undefined)
            {
                $('.cal10').empty().html((val10).toFixed(2)).data('editing', false);
                $('.cal11').empty().html((val11).toFixed(2)).data('editing', false);
                // $('.cal10').text(val10);
                // $('.cal11').text(val11);
            }
            if (val11 != undefined)
            {
                $('.cal15').empty().html((val15).toFixed(2)).data('editing', false);
                $('.cal16').empty().html((val16).toFixed(2)).data('editing', false);
                $('.cal17').empty().html((val17).toFixed(2)).data('editing', false);
                $('.cal18').empty().html((val18).toFixed(2)).data('editing', false);
                // $('.cal15').text(val15);
                // $('.cal16').text(val16);
                // $('.cal17').text(val17);
                // $('.cal18').text(val18);
            }
            if (val56 != undefined || val58 != undefined)
            {
                $('.cal60').empty().html(val60).data('editing', false);
            }
            if (val57 != undefined || val59 != undefined)
            {
                $('.cal61').empty().html(val61).data('editing', false);
            }
            var closing_balance = $('#closing_balance').text();
            if (calid == 'ly_closing_balance')
            {
                var balance = val1;
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: base_url + 'schedule_note/ly_closing_balance/',
                    data: {bal: balance},
                    success: function (data) {
                    }
                });
            }
            if (closing_balance != undefined && closing_balance != '' && closing_balance != '--')
            {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: base_url + 'schedule_note/closing_balance/',
                    data: {bal: closing_balance},
                    success: function (data) {
                    }
                });
            }

            var td = $this.closest('td');

            if (text != '')
            {
                var total = (parseFloat(val1) * 10);
                $('#auth').empty().html(val1 + text).data('editing', false);
                $('#issued').empty().html(val1 + text).data('editing', false);
                $('#auth_amt').empty().html((total).toFixed(2)).data('editing', false);
                $('#issued_amt').empty().html((total).toFixed(2)).data('editing', false);
                $('#total_amt').empty().html((total).toFixed(2)).data('editing', false);
            } else
            {
                td.empty().html(val1).data('editing', false);
            }
        });
    };
    $("#schedule_note_table td").click(function (e) {
        $this = $(this);
        if ($this.data('editing'))
            return;
        putOldValueBack();
    });
});
