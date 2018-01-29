/**
 * Created by sedan on 2016-06-11.
 * 수정 2016-07-23
 * 수정 2017-01-08
 */

var CAL = {};

CAL.getData = function () {
    /*하나의 행씩 row 배열에 넣고 4개의 행을 rows 배열에 넣음*/
    var rows = [];
    var row = {};
    row = ['7', '8', '9', '*'];
    rows.push(row);
    row = {};
    row = ['4', '5', '6', '/'];
    rows.push(row);
    row = {};
    row = ['1', '2', '3', '-'];
    rows.push(row);
    row = {};
    row = ['0', 'C', '=', '+'];
    rows.push(row);
    return rows;
}


CAL.display = function (rows) {

    var button = null;
    var wrap = $("#wrap");

    for (var i = 0; i < rows.length; i++) {
        for (var j = 0; j < rows[i].length; j++) {

            button = $("<button num='" + rows[i][j] + "'id='" + rows[i][j] + "' > " + rows[i][j] + "</button>");
            wrap.append(button);

        }
    }
}


$(document).ready(function () {
    CAL.display(CAL.getData());

    $("button").click(function () {
        var push = $(this).attr('id');

        if (push == 'C') {
            $("#view").html('');
            $(this).attr('num', 'CLEAR');
        }
        else if (push == "=") {
            var str = $("#view").html();
            $("#view").html(eval(str));
            $(this).attr('num', $("#view").html());
        }
        else {
            $("#view").append(push);
        }
    });

    $("h1").click(function () {
        location.reload();
    });

});




