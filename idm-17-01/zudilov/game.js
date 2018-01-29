function draw_dialogue(num){
	state = 1;
	ctx.drawImage(img,0,0);
	ctx.fillStyle = "rgba(120,35,0,0.5)"
	ctx.fillRect(250,100,550,350);
	ctx.font = "30px Arial";
	ctx.fillStyle = "rgb(255,255,255)";
	ctx.fillText("Зудилов А.А.",300,150)
	ctx.fillText("Всем хорошего здоровья",300,180)
	ctx.fillText("№1 Гитхаб и все такое свободное.",300,210)
	ctx.fillText("№2 Командная работа.",300,240)
	ctx.fillText("№3 Настройка локалки.",300,270)
	ctx.fillText("Финал: Самооценка, тест итп.",300,300)
	if(toilet_presses>1){
		ctx.fillText("Вы нажали на туалет в "+toilet_presses+"й раз",300,330)
	}
	ctx.lineWidth = "5";
	if(num==1){
		ctx.strokeStyle = "White";
	}
	else{
		ctx.strokeStyle = "Yellow";
		ctx.fillStyle = "Yellow";
	}
	ctx.fillText("Отлично. Спасибо, что напомнил",292,395)
	ctx.rect(260,350,530,70);
	ctx.stroke();
}
var mus = new Audio("1.mp3")
mus.volume = 0.9;
mus.play();
var toilet_presses = 0;
var game_canv = document.getElementById("game"),
ctx = game_canv.getContext('2d');
var state = 0
//ctx.fillRect(0, 0, example.width, example.height);
var img = new Image();
var img2 = new Image();
img.src = "map.png";
img2.src = "map2.png";
img.onload = function(){
	ctx.drawImage(img,0,0);
}
game_canv.onmousemove = function(pos){
	ctx.clearRect(0,0,992,512);
	x = pos.clientX;
	y = pos.clientY;
	if(state==0){
		if(x>730 & y>320){
			ctx.drawImage(img2,0,0);
		}
		else{
			ctx.drawImage(img,0,0);
		}
	}
	else{
		if(x>300 & y>350 & x<750 & y<420){
			draw_dialogue(0);
		}
		else{
			draw_dialogue(1);
		}
	}
		
}
game_canv.onclick = function(pos){
	x = pos.clientX;
	y = pos.clientY;
	if(x>730 & y>320 & state==0){
		toilet_presses+=1;
		draw_dialogue(1);
	}
	if(x>260 & y>350 & x<720 & y<420){
		state = 0;
		ctx.clearRect(0,0,992,512);
		ctx.drawImage(img,0,0);
	}
	
}