$(document).on("click", "a.autodetect_bank", function (e) {
    e.preventDefault();
    var t = $(this);
    var url = t.attr("href");
    $.ajax({
        url: url,
        success: function (data) {
            var jsondata = $.parseJSON(data);
            var objarr = {};
            for (var i in jsondata) {
                var obj = jsondata[i];
                var pageid = "page_" + obj.page;
                if (objarr[pageid] === undefined) {
                    objarr[pageid] = [];
                }
                objarr[pageid].push({
                    x: obj.left,
                    y: obj.top,
                    width: (obj.right - obj.left),
                    height: (obj.bottom - obj.top)
                });
            }
            console.log(objarr);
            for (var i in objarr) {
                var id = "#" + i;
                $(id).selectAreas('add', objarr[i]);
            }
            /*var pages = $("img.page");            
             for (var i = 0; i < pages.length; i++) {
             
             }*/
        }
    });
});
$(document).ready(function (e) {
    if ($("img.page").length > 0) {
        $('img.page').selectAreas({
        });
    }
});
$(document).on("click", "a.savedata", function (e) {
    e.preventDefault();
    var pages = $("img.page");
    var loc = {};
    for (var i = 0; i < pages.length; i++) {
        var page = $(pages[i]);
        var pg = page.data("pg");
        if (loc[pg] === undefined) {
            loc[pg] = [];
        }
        console.log(page.data("pg"));
        var areas = page.selectAreas('areas');
        if (areas.length > 0) {
            $.each(areas, function (id, area) {
                loc[pg] = areaToString(area);
            });
        }
    }
    console.log(loc);
    var t = $(this);
    var url = t.attr("href");
    $.ajax({
        url: url,
        type: "post",
        data: {pageobjlist: loc},
        success: function (data) {
            window.location = dataviewurl;
            // window.location.href = "<?= base_url()?>bank_statement/view_bank_statement";
        }
    });
});
function areaToString(area) {
    return (typeof area.id === undefined ? false : {left: area.x - 5, top: (area.y), right: (area.x + area.width), bottom: (area.y + area.height)});
}

