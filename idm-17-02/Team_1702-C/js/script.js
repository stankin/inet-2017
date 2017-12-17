// board size
var boardWidth = 10;
var boardHeight = 10;
var boardMaxSize = 100;
var board2D;
var board2DInitial;

// board move
var boardMove = false;
var boardMX, boardMY;
var boardMSkip = false;

// board cell
var cellSize = 20;
var cellSizeMin = 5;
var cellSizeMax = 50;
var cellSizeDelta = 2;
var cellClass = "cell";
var cellClassPrefix = "cell cell-";
var cellBlockClass = "cell cell-block";
var cellWallClass = "cell cell-wall";
var cellStyleBGPart = "linear-gradient(135deg, rgba(0,0,0,0.36) 0%,rgba(0,0,0,0.21) 50%,rgba(0,0,0,0.36) 100%)";
var cell_id = "cell-Y-X";

// cells
var cellTypes = [];
var currentCellType = 1;
var maxCellTypes = 7;

// calculations
var wrapBorders = false;
var activeCellType = 1;
var calcCellTypes = [];
var calcInitBoard;
var calcStepBoardLast;
var calcStepBoardNext;
var pauseResume = true;
var calcTimeMS = 250;
var calcActive;

//creation
var textFile = null;

//misc
var configsJSON = "config/configs.json";
var boardConfigs = [];
var langJSON = "language/__file__.json";
var fDummy = "__file__";
var collapseIT = '<span>▴ Свернуть</span>';
var collapseIT_dummy = '<span>▴ __file__</span>';
var uncollapseIT = '<span>▾ Развернуть</span>';
var uncollapseIT_dummy = '<span>▾ __file__</span>';
var optPrefixId = "opt-";

function setup(){
	var settings = document.getElementById("settings");
	settings.className = 'settings-normal';
	
	var collapseITClass = 'settings-normal';
	var uncollapseITClass = 'settings-collapsed';
	
	var collapse = document.getElementById("collapse");
	collapse.innerHTML = collapseIT;
	collapse.onclick = function(){
		var settings = document.getElementById("settings");
		console.log(settings.className);
		if(settings.className == '' || settings.className == collapseITClass){
			settings.className = uncollapseITClass;
			this.innerHTML = uncollapseIT;
		}else{
			settings.className = collapseITClass;
			this.innerHTML = collapseIT;
		}
	}
	
	initSets();
	
	var addSet = document.getElementById("add_set");
	addSet.onclick = function(){
		addNewSet();
	}
	
	var remSet = document.getElementById("zem_set");
	remSet.disabled = true;
	remSet.onclick = function(){
		remLastSet();
	}
	
	// init board
	board2D = board2DInit();
	
	boardSet(boardWidth, boardHeight);
	
	board.onmousedown = function(event){
		boardMove = true;
		boardMX = event.clientX;
		boardMY = event.clientY;
	}
	
	board.onmouseup = function(){
		boardMove = false;
	}
	window.onmouseup = function(){
		boardMove = false;
	}
	
	window.onmousemove = function(event){
		if(boardMove){
			boardMSkip = true;
			var nbMX = event.clientX;
			var nbMY = event.clientY;
			window.scrollBy(boardMX - nbMX, boardMY - nbMY);
			boardMX = nbMX;
			boardMY = nbMY;
		}
	}
	
	board.addEventListener("mousewheel", MouseWheelHandler, false);
	board.addEventListener("DOMMouseScroll", MouseWheelHandler, false);
	
	var inputWidth = document.getElementsByName("width")[0];
	inputWidth.value = boardWidth;
	var inputHeight = document.getElementsByName("height")[0];
	inputHeight.value = boardHeight;
	inputWidth.onchange = function(){
		onchangeBoardWH();
	}
	inputHeight.onchange = function(){
		onchangeBoardWH();
	}
	
	var infoButton = document.getElementById("button_info");
	infoButton.onclick = function(){
		var info = document.getElementById("info");
		info.style.display = "block";
	}
	
	var info = document.getElementById("info");
	info.onclick = function(){
		var info = document.getElementById("info");
		info.style.display = "none";
	}
	
	var startButton = document.getElementById("start_button");
	startButton.onclick = function(){
		setControlButtonsDisabledState(true, false, false);
		startCalculations();
	}
	
	var pauseButton = document.getElementById("pause_button");
	pauseButton.onclick = function(){
		setControlButtonsDisabledState(true, false, false);
		pauseResumeCalculations();
	}
	
	var stopButton = document.getElementById("stop_button");
	stopButton.onclick = function(){
		setControlButtonsDisabledState(false, true, true);
		stopCalculations();
	}
	
	setControlButtonsDisabledState(false, true, true);
	
	var saveButton = document.getElementById("save_config");
	saveButton.onclick = function(){
		downloadJSON("config.json", boardToJSON());
	}
	
	var wrapBordersCheckbox = document.getElementsByName("wrap")[0];
	wrapBordersCheckbox.onclick = function(){
		var wrapBordersCheckbox = document.getElementsByName("wrap")[0];
		wrapBordersCheckbox.value = !(wrapBordersCheckbox.value);
		console.log("Wrap boarders - " + wrapBordersCheckbox.checked);
        wrapBorders = wrapBordersCheckbox.checked;
	}
	
	var configsList = document.getElementById("config");
	while (configsList.hasChildNodes()) {
		configsList.removeChild(configsList.lastChild);
	}
	$.getJSON(configsJSON, function(data){
		for(var i = 0; i < data.configs.length; i++){
			if(data.configs[i].json != null){
				console.log("Asynchronically loading '" + data.configs[i].name + "'...");
				$.getJSON(data.configs[i].json, function(cdata){
					console.log("..." + cdata.name + " successfully loaded");	
					boardConfigs.push(cdata);
					var configsList = document.getElementById("config");
					var opt = document.createElement("option");
                    opt.id = optPrefixId + cdata.name;
					opt.value = cdata.name;
					opt.innerHTML = cdata.visName;
					configsList.appendChild(opt);
				});
			}
		}
	});
	
	var selectConfig = document.getElementById("select_config");
	selectConfig.onclick = function(){
		var configsList = document.getElementById("config");
		for(var i = 0; i < boardConfigs.length; i++){
			if(boardConfigs[i].name === configsList.value){
				loadBoard(boardConfigs[i]);
				break;
			}
		}
	}
    
    var langRU = document.getElementById("ru");
    langRU.onclick = function(){
        setLanguageTo("ru");
    }
    
    var langMY = document.getElementById("my");
    langMY.onclick = function(){
        setLanguageTo("my");
    }
    
    setLanguageTo("ru");
    
	//test
}

function setLanguageTo(lang){
    var newLang = langJSON.replace(fDummy, lang);
    console.log("Loading language from file: " + newLang);
    $.getJSON(newLang, function(data){
        setLanguageFromData(data);
    });
    
    console.log("Set language to <" + lang + ">");
}

function setLanguageFromData(data){
    // by id's
        for(var i = 0; i < data.id.length; i++){
            var pair = data.id[i];
            var el = document.getElementById(pair[0]);
            console.log("..." + pair[0]);
            if(el){
                el.innerHTML = pair[1];
            }else{
                console.log("language error on key: '" + pair[0] + "', value: '" + pair[1] + "'");
            }
        }
        
        // misc
        var collapse = document.getElementById("collapse");
        var col_state = 0;
        if(collapse.innerHTML == uncollapseIT){
            col_state = 1;
        }
        collapseIT = collapseIT_dummy.replace(fDummy, data.misc.collapse_text);
        uncollapseIT = uncollapseIT_dummy.replace(fDummy, data.misc.uncollapse_text);
        collapse.innerHTML = (col_state == 0 ? collapseIT : uncollapseIT);
        
        // sets
        for(var i = 0; i < data.sets.length; i++){
            var pair = data.sets[i];
            var el = document.getElementById(optPrefixId + pair[0]);
            console.log("opt..." + pair[0]);
            if(el){
                el.innerHTML = pair[1];
            }else{
                console.log("language error on key: '" + pair[0] + "', value: '" + pair[1] + "'");
            }
        }
}

function loadBoard(board){
	var wrapBordersCheckbox = document.getElementsByName("wrap")[0];
	wrapBordersCheckbox.value = board.wrapBorders;
	wrapBorders = board.wrapBorders;
	var inputWidth = document.getElementsByName("width")[0];
	inputWidth.value = board.width;
	var inputHeight = document.getElementsByName("height")[0];
	inputHeight.value = board.height;
	onchangeBoardWH();
	
	cleanBoard();
	
	for(var i = 0; i < board.cells.length; i++){
		var x = board.cells[i].x;
		var y = board.cells[i].y;
		var v = board.cells[i].v;
		board2D[y][x] = v;
		board2DInitial[y][x] = v;
		
		for(var j = 0; j < boardMaxSize; j++){
			var cID = getCellID(y, x);
			var cell = document.getElementById(cID);
			if(cell !== null){
				if(v > 0){
					cell.className = cellClassPrefix + (v-1);
				}else if(v < 0){
					cell.className = cellWallClass;
				}
			}
		}
	}
}

function getCellID(j, i){
	return cell_id.replace("Y", j).replace("X", i);
}

function cleanBoard(visual = false){
	for(var j = 0; j < boardMaxSize; j++){
		for(var i = 0; i < boardMaxSize; i++){
            if(visual == false){
                board2D[j][i] = 0;
                board2DInitial[j][i] = 0;
            }
			
			var cID = getCellID(j, i);
			var cell = document.getElementById(cID);
			if(cell !== null){
				cell.className = cellClass;
			}
		}
	}
}

function boardToJSON(){
	var board = {
		name: "conf_name",
		visName: "Default config name",
		wrapBorders: wrapBorders,
		width: boardWidth,
		height: boardHeight,
		cells: []
	};
	
	for(var j = 0; j < boardHeight; j++){
		for(var i = 0; i < boardWidth; i++){
			if(board2D[j][i] != 0){
				board.cells.push({x: i, y: j, v: board2D[j][i]});
			}
		}
	}
	var text = JSON.stringify(board);
	console.log(text);
	var data = new Blob([text], {type: 'text/plain'});

    // If we are replacing a previously generated file we need to
    // manually revoke the object URL to avoid memory leaks.
    if (textFile !== null) {
      window.URL.revokeObjectURL(textFile);
    }
	
	textFile = window.URL.createObjectURL(data);

    return textFile;
}

function downloadJSON(filename, fileURL) {
    var a = document.createElement('a');
    a.style = "display: none";  
    a.href = fileURL;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(function(){
        document.body.removeChild(a);  
    }, 250);      
}

function setControlButtonsDisabledState(start_s, pause_s, stop_s){
	var startButton = document.getElementById("start_button");
	startButton.disabled = start_s;
	var pauseButton = document.getElementById("pause_button");
	pauseButton.disabled = pause_s;
	var stopButton = document.getElementById("stop_button");
	stopButton.disabled = stop_s;
}

function startCalculations(){
    console.log("start >>>");
    
	copyBoard(board2D, board2DInitial);
    
    calcActive = true;
    
    // copy calc cell types
    updateSetsData();
    calcCellTypes = [];
    for(var i = 0; i < cellTypes.length; i++){
        calcCellTypes.push(cellTypes[i]);
    }
    
    console.log(calcCellTypes);
    
    activeCellType = 0;
    
    stepCalculationTO();
    
}

function stepCalculationTO(){
    console.log("step...");
    stepCalculation();
    
    if(calcActive){
        setTimeout(stepCalculationTO, calcTimeMS);
    }
}

function stepCalculation(){    
    var b2D = newBoardWithSizeFrom(board2D);

    var lim_k = calcCellTypes.length;
    for(var k = 0; k < lim_k; k++){
        var realCT = (k + activeCellType) % lim_k;
        var realCTF = realCT + 1;
        console.log("calculating for the cell type (" + realCTF + ")");
        var rct_unique = calcCellTypes[realCT].unique;
        var rct_spawn = calcCellTypes[realCT].spawn;
        var rct_surv = calcCellTypes[realCT].survive;
        var rct_die = calcCellTypes[realCT].die;
        
        for(var j = 0; j < boardHeight; j++){
            for(var i = 0; i < boardWidth; i++){
                var c = board2D[j][i];
                var n = numOfNeighbours(realCTF, j, i, rct_unique);

                if(c == realCTF){
					// may survive
                	b2D[j][i] = realCTF;
                	
                    // dies? - not enough
                    if(n < rct_surv){
                        b2D[j][i] = 0;
                    }
                    // dies? - too much
                    if(n >= rct_die){
                        b2D[j][i] = 0;
                    }
                }else if(c == 0){
                    // spawns?
                    if(n == rct_spawn){
                        b2D[j][i] = realCTF;
                    }
                }else if(c == -1){
                    b2D[j][i] = -1;
                }
            }
        }
    }
    
    displayBoard(b2D);
    
    copyBoard(b2D, board2D);

    activeCellType++;
    if(activeCellType >= calcCellTypes.length){
        activeCellType = 0;
    }
    console.log("active cell: " + activeCellType);
}

function getCell(j, i){
    return document.getElementById(getCellID(j, i));
}

function setCellTo(j, i, v){
    var cell = getCell(j, i);
    if(cell !== null){
        if(v > 0){
            cell.className = cellClassPrefix + (v-1);
        }else if(v < 0){
            cell.className = cellWallClass;
        }else{
        	cell.className = cellClass;
        }
    }
}

function displayBoard(board){
    for(var j = 0; j < boardHeight; j++){
        for(var i = 0; i < boardWidth; i++){
            setCellTo(j, i, board[j][i]);
        }
    }
}

function cellOn(y, x){
    // clipping -
	if(x < 0){
		if(wrapBorders){
			x = boardWidth + x;
		}else{
			return 0;
		}
	}

	if(y < 0){
		if(wrapBorders){
			y = boardHeight + y;
		}else{
			return 0;
		}
	}
    
    // clipping +
    if(x >= boardWidth){
        if(wrapBorders){
			x = boardWidth - x;
		}else{
			return 0;
		}
    }
    
    if(y >= boardHeight){
		if(wrapBorders){
			y = boardHeight - y;
		}else{
			return 0;
		}
	}
    
    return board2D[y][x];
}

function numOfNeighbours(id, y, x, unique){
    var n = 0;
    for(var j = -1; j < 2; j++){
        for(var i = -1; i < 2; i++){
            if(i != 0 || j != 0){
            	var ny = (y + j);
            	var nx = (x + i)
                var c = cellOn(ny, nx);
                // not a wall or an empty cell
                if(c > 0){
                    if(unique){
                        if(id == c){
                            n++;
                        }
                    }else{
                        n++;
                    }
                }
            }
        }
    }
    
    return n;
}

function copyBoard(src, dest){
    for(var j = 0; j < src.length; j++){
        for(var i = 0; i < src.length; i++){
            dest[j][i] = src[j][i];
        }
    }
}

function newBoardWithSizeFrom(src){
    var newBoard = [];
    for(var j = 0; j < src.length; j++){
        var row = [];
        for(var i = 0; i < src.length; i++){
            row.push(0);
        }
        newBoard.push(row);
    }
    
    return newBoard;
}

function pauseResumeCalculations(){
	if(pauseResume){
		pauseResume = false;
		console.log("Paused");
        calcActive = false;
	}else{
		pauseResume = true;
		console.log("Resumed");
        calcActive = true;
        
        stepCalculationTO();
	}
}

function stopCalculations(){
    calcActive = false;
    
    setTimeout(function(){
		copyBoard(board2DInitial, board2D);
		displayBoard(board2D);

		console.log("stop.");
    	}, 2*calcTimeMS);
}

function board2DInit() {
	board2D = [];
	board2DInitial = [];
	for(var j = 0; j < boardMaxSize; j++){
		var col = [];
		var col_ = [];
		for(var i = 0; i < boardMaxSize; i++){
			col[i] = 0;
			col_[i] = 0;
		}
		board2D[j] = col;
		board2DInitial[j] = col_;
	}
	
	return board2D;
}

function onchangeBoardWH(){
	var bWidthInput = document.getElementsByName("width")[0];
	bWidthInput.disabled = true;
	var bHeightInput = document.getElementsByName("height")[0];
	bHeightInput.disabled = true;
	
	boardSet(bWidthInput.value, bHeightInput.value);
	
	bWidthInput.disabled = false;
	bHeightInput.disabled = false;
	
}

function onclickCell(){
	if(!boardMSkip){
		var that = this;
		var x = this.getAttribute("x");
		var y = this.getAttribute("y");
		
		if(board2D[y][x] == 0){
			board2D[y][x] = currentCellType;
			console.log("Cell [y: " + y + "; x: " + x + "] - Set to " + currentCellType + "!");
			
			that.className = cellClassPrefix + (currentCellType-1);
		}else{
			board2D[y][x] = 0;
			console.log("Cell [y: " + y + "; x: " + x + "] - Unset.");
			
			that.className = cellClass;
		}
	}else{
		boardMSkip = false;
	}
}

function onrightclickCell(e){
	e.preventDefault();
	
	var that = e.target;
	var x = e.target.getAttribute("x")
	var y = e.target.getAttribute("y")
	
	if(board2D[y][x] == 0){
		board2D[y][x] = -1;
		console.log("Wall-Cell [y: " + y + "; x: " + x + "] - Set!");
		
		that.className = cellWallClass;
	}else{
		board2D[y][x] = 0;
		console.log("Wall-Cell [y: " + y + "; x: " + x + "] - Unset.");
		
		that.className = cellClass;
	}
}

function getCellClass(y, x){
	if(board2D[y][x] == -1){
		return cellWallClass;
	}
	
	if(board2D[y][x] >= 1){
		return cellClassPrefix + (board2D[y][x]-1);
	}
	
	return cellClass;
}

function boardSet(bWidth, bHeight){
	var board = document.getElementById('board');
	var table = board.children[0];
	if(board.children.length > 0){
		board.removeChild(table);
	}
	table = document.createElement('table');
	
	boardWidth = parseInt(bWidth);
	boardHeight = parseInt(bHeight);
	//board2D = 
	
	for(var j = 0; j < boardHeight; j++){
		var tr = document.createElement('tr');
		
		for(var i = 0; i < boardWidth; i++){
			var td = document.createElement('td');
			var cell = document.createElement('div');
			cell.className = getCellClass(j, i)
			cell.setAttribute("y", j);
			cell.setAttribute("x", i);
			cell.setAttribute("id", getCellID(j, i));
			cell.onclick = onclickCell;
			cell.addEventListener("contextmenu", function(event){ onrightclickCell(event); });
			td.appendChild(cell);
			
			tr.appendChild(td);
		}
		
		table.appendChild(tr);
	}
	
	board.appendChild(table);
}

function updateBoard(){
	
}

function MouseWheelHandler(e) {
	e.preventDefault();
	
	// cross-browser wheel delta
	var e = window.event || e; // old IE support
	var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
	
	//console.log("delta " + delta);
	if(delta > 0 || delta < 0){
		if(delta > 0){
			cellSize += cellSizeDelta;
		}else if(delta < 0){
			cellSize -= cellSizeDelta;
		}
		
		if(cellSize < cellSizeMin){
			cellSize = cellSizeMin;
		}
		
		if(cellSize > cellSizeMax){
			cellSize = cellSizeMax;
		}
		
		jss.set('div.cell', {'width': cellSize+'px', 'height': cellSize+'px'});
	}
	
	return false;
}

function initSets(){
	var cont = document.getElementById("sets");
	var setsTable = document.createElement("table");
	setsTable.setAttribute("id", "sets_table");
	
	var params = ["Выбор для рисования", "Цвет", "Соседей для появления", "Соседей для выживания", "Соседей для смерти", "Уникальный набор"];
	var text_ID = ["setSelection_text", "setColor_text", "nToBorn_text", "nToSurvive_text", "nToDie_text", "uniqueSet_text"];
	for(var j = 0; j < params.length; j++){
		var tr = document.createElement("tr");
		var td = document.createElement("td");
		var p = document.createElement("p");
		p.setAttribute("id", text_ID[j]);
		p.innerHTML = params[j];
		td.appendChild(p);
		tr.appendChild(td);
		
		setsTable.appendChild(tr);
	}
	
	cont.appendChild(setsTable);
	
	addNewSet();
	var blockDrawing = document.getElementsByName("blockDrawing");
	blockDrawing[0].checked = true;
}

function randomColor(){
	var letters = '0123456789abcdef';
	var ret = '#';
	for (var i = 0; i < 6; i++) {
		ret += letters[Math.floor(Math.random() * 16)];
	}
	console.log(ret);
	return ret;
}

function updateSetsData(){
    var setsTable = document.getElementById("sets_table");
    var rows = setsTable.children;
    //cellTypes[n] = {color: values[0], spawn: nToSpawn, survive: nToSurvive, die: nToDie, unique: values[5]};
    for(var i = 0; i < cellTypes.length; i++){
        cellTypes[i].spawn = parseInt(rows[2].children[i+1].children[0].value);
        cellTypes[i].survive = parseInt(rows[3].children[i+1].children[0].value);
        cellTypes[i].die = parseInt(rows[4].children[i+1].children[0].value);
        cellTypes[i].unique = rows[5].children[i+1].children[0].checked;
    }
}

function addNewSet(){
	var setsTable = document.getElementById("sets_table");
	
	var n = cellTypes.length;
	
	var types = ["radio", "color", "number", "number", "number", "checkbox"];
	var nToSpawn = 3 + Math.floor(Math.random() * 5);
	var nToSurvive = 2 + Math.floor((nToSpawn - 3)*Math.random());
	var nToDie = nToSpawn + 1 + Math.floor((8 - nToSpawn) * Math.random());
	//console.log("S: " + nToSpawn + ", D: " + nToDie);
	var values = [null, null, nToSpawn, nToSurvive, nToDie, (Math.random() >= 0.5 ? true : false)];
	for(var i = 0; i < 6; i++){
		var input = document.createElement("input");
		input.setAttribute("type", types[i]);
		if(i == 0){
			input.setAttribute("name", "blockDrawing");
			input.onchange = function(){
				var blockDrawing = document.getElementsByName("blockDrawing");
				for(var i = 0; i < blockDrawing.length; i++){
					if(blockDrawing[i].checked){
						currentCellType = i + 1;
						console.log("drawing block changed - #" + currentCellType);
						break;
					}
				}
			}
		}
		if(i == 1){
			input = document.createElement("div");
			input.className = ("cell-" + n);
			input.style = "width: 16px; height: 16px; margin: auto;";
		}
		if(i < 5){
			//console.log("v: " + values[i]);
			input.value = values[i];
		}else{
			input.checked = values[i];
		}
		var td = document.createElement("td");
		td.appendChild(input);
		setsTable.children[i].appendChild(td);
	}
	
	var rem = document.getElementById("zem_set");
	rem.disabled = false;
	
	cellTypes[n] = {color: values[0], spawn: nToSpawn, survive: nToSurvive, die: nToDie, unique: values[5]};
	
	var add = document.getElementById("add_set");
	if(n >= maxCellTypes){
		add.disabled = true;
	}else{
		add.disabled = false;
	}
}

function setDrawingBlock(){
	
}

function remLastSet(){
	if(cellTypes.length > 1){
		if(currentCellType == cellTypes.length){
			var blockDrawing = document.getElementsByName("blockDrawing");
			if(blockDrawing[blockDrawing.length-1].checked){
				blockDrawing[blockDrawing.length-2].checked = true;
				currentCellType--;
			}
		}
		
		cellTypes.pop();
		
		var setsTable = document.getElementById("sets_table");
		for(var j = 0; j < 6; j++){
			var tr = setsTable.children[j];
			
			tr.removeChild(tr.lastChild);
		}
		
		if(cellTypes.length <= 1){
			var rem = document.getElementById("zem_set");
			rem.disabled = true;
		}
	}
	
	var add = document.getElementById("add_set");
	if(cellTypes.length >= maxCellTypes){
		add.disabled = true;
	}else{
		add.disabled = false;
	}
}

window.onload = function(){
	setup();
}
