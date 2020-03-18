$(function ()
{
    var str1 = '';
    var str2 = '';
    var str3 = '';
    $('#note1').autoComplete(
            {
                minChars: 1,
                cache: !1,
                source: function (term, suggest)
                {
                    str2 = term;
                    term = term.toLowerCase();
                    str1 = term.substring(term.length - 1, term.length);
                    if (str1 == '#')
                    {
                        $.ajax(
                                {
                                    url: base_url + "note_template/get_note_template/",
                                    type: "GET",
                                    dataType: "json",
                                    success: function (data)
                                    {
                                        var suggestions = [];
                                        for (var i = 0; i < data.length; ++i)
                                        {
                                            suggestions.push(data[i].hash_tag)
                                        }
                                        suggest(suggestions)
                                    }
                                })
                    }
                },
                onSelect: function (event, ui)
                {
                    var j = 0;
                    var str4;
                    for (var i = str2.length - 1; i >= 0; i--)
                    {
                        if (str2[i] == '#')
                        {
                            j = i;
                            break
                        }
                    }
                    str3 = str2.substring(0, j + 1);
                    str4 = ui.split('#');
                    $('#note1').val(str3 + str4[1] + " ")
                }
            });
    var string1 = '';
    var string2 = '';
    var string3 = '';
    $('#note2').autoComplete(
            {
                minChars: 1,
                cache: !1,
                source: function (term, suggest)
                {
                    string2 = term;
                    term = term.toLowerCase();
                    string1 = term.substring(term.length - 1, term.length);
                    if (string1 == '#')
                    {
                        $.ajax(
                                {
                                    url: base_url + "note_template/get_note_template/",
                                    type: "GET",
                                    dataType: "json",
                                    success: function (data)
                                    {
                                        var suggestions = [];
                                        for (var i = 0; i < data.length; ++i)
                                        {
                                            suggestions.push(data[i].hash_tag)
                                        }
                                        suggest(suggestions)
                                    }
                                })
                    }
                },
                onSelect: function (event, ui)
                {
                    var j = 0;
                    var string4;
                    for (var i = string2.length - 1; i >= 0; i--)
                    {
                        if (string2[i] == '#')
                        {
                            j = i;
                            break
                        }
                    }
                    string3 = string2.substring(0, j + 1);
                    string4 = ui.split('#');
                    $('#note2').val(string3 + string4[1] + " ")
                }
            })
})
