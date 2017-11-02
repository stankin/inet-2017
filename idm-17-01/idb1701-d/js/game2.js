
step = 0;

diff=3;
function clear_all(form) {
    step = 0;
    for (i=0;i<9; ++i) {
        position="a"+i;
        form[position].value="";
    }

}


function clickit(field) {

    if (step == -1) {alert("Нажмите кнопку новая игра!"); return;}



    position=field.name.substring(1,2,1);

    position = 'a'+position;

    if (field.form[position].value !="") {alert("Это поле занято!"); return;}

    field.form[position].value="X";

    if (eval_pos(field.form)) {

        field.form.output.value="Победа!";

        step = -1;

        return;

    }

    position=get_move(field.form);

    field.form.output.value=' ' ;

    if (position=="") {

        field.form.output.value="Ничья.";

        step = -1;



        return;

    }

    field.form[position].value="O";

    if (eval_pos(field.form)) {

        field.form.output.value="Вы проиграли!";

        step = -1;

    }

}



// Модуль показывает есть ли победитель

function eval_pos(form) {

    if ((form.a0.value!="" &&

            form.a0.value==form.a3.value && form.a0.value==form.a6.value)||



        (form.a0.value!=""

            && form.a0.value==form.a1.value && form.a0.value==form.a2.value) ||

        (form.a0.value!=""

            && form.a0.value==form.a4.value && form.a0.value==form.a8.value) ||

        (form.a1.value!=""

            && form.a1.value==form.a4.value && form.a1.value==form.a7.value) ||

        (form.a2.value!=""

            && form.a2.value==form.a5.value && form.a2.value==form.a8.value) ||

        (form.a2.value!=""

            && form.a2.value==form.a4.value && form.a2.value==form.a6.value) ||

        (form.a3.value!=""

            && form.a3.value==form.a4.value && form.a3.value==form.a5.value) ||

        (form.a6.value!=""

            && form.a6.value==form.a7.value && form.a6.value==form.a8.value))

        return true;

    else

        return false;

}



function f(a) {

    if (a == "") return "."; else return a;

}



// Управление перемещением

function comp_move(form,player,weight,depth) {



    var cost;

    var bestcost=-2;

    var position;

    var newplayer;

    if (player=="X") newplayer="O"; else newplayer="X";

    if (depth==diff) return 0;



    if (eval_pos(form)) return 1;

    for (var i=0; i<9; ++i) {

        position='a'+i;

        if (form[position].value != "")

            continue;

        form[position].value=player;

        cost = comp_move(form,newplayer, -weight, depth+1);

        if (cost > bestcost) {

            bestcost=cost;

            if (cost==1) i=9;

        }

        form[position].value="";

    }

    if (bestcost==-2) bestcost=0;

    return(-bestcost);

}





function get_move(form) {

    var cost;

    var bestcost=-2;

    bestmove="";

    // Первый ход

    if (step++ == 0)

        if (form.a4.value=="")

            return "a4";

        else

        if (form.a0.value=="")

            return "a0";



    for (var i=0; i<9; ++i) {

        localposition='a'+i;

        if (form[localposition].value != "")

            continue;

        form[localposition].value="O";

        cost=comp_move(form,"X", -1, 0);

        if (cost > bestcost) {

            if (cost==1) i=9;

            bestmove=localposition;

            bestcost=cost;

        }

        form[localposition].value="";

    }

    return bestmove;

}



// Когда пользователь хочет обойти правила

function complain(field) {

    field.form.output.focus(); // put focus eleswhere

    alert("Не надо пытаться жульничать!");

}

//a href="http://www.cgi.ru/

// the end -->
