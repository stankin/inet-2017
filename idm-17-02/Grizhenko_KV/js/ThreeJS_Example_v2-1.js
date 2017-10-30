// Куб на движке Three.js
// Версия 2.1 от 24.08.2014

    //-- Параметры --//
function Main_ThreeJS_example() {
    var right = 100;
    var width = 500;
    var height = 500;

    // Переменные
    var theta = 0, phi= 0;              // углы поворота
    var zStart = 2;                     // чтобы можно было восстановить начальное состояние по кнопке [home]
    var z = zStart;                     // расстояние до куба
    var cube, camera, renderer, scene;

    // Работа с клавиатурой
    var currentlyPressedKeys = {};
    var keys = [33, 34, 36, 37, 38, 39, 40];
    function handleKeys() {                                     // обработка нажатий клавиш
        if (currentlyPressedKeys[33]) {z -= 0.02;}              // Page Up
        if (currentlyPressedKeys[34]) {z += 0.02;}              // Page Down
        if (currentlyPressedKeys[37]) {phi = -2;}               // Влево
        if (currentlyPressedKeys[39]) {phi = 2;}                // Вправо
        if (currentlyPressedKeys[38]) {theta = -2;}             // Вверх
        if (currentlyPressedKeys[40]) {theta = 2;}              // Вниз
    }
    function handleKeyDown(event) {                             // клавиша нажата
        currentlyPressedKeys[event.keyCode] = true;

        if (event.keyCode == "36") {                            // Home
            camera.rotation.x = camera.rotation.y = camera.rotation.z = 0;
            z = zStart;
        }

        for (var k = 0; k < keys.length; k++)                   // чтобы не блокировать лишние клавиши
            if (keys[k] == event.keyCode) return false;
    }
    function handleKeyUp(event) {                               // клавиша отпущена
        currentlyPressedKeys[event.keyCode] = false;
    }


    var render = function () {
        handleKeys();
        requestAnimationFrame(render);
        setCameraPos();
        renderer.render(scene, camera);
    };

    function setCameraPos() {                                   // функция поворота
        camera.rotateOnAxis({x:1, y:0, z:0}, -theta * Math.PI / 180 );
        camera.rotateOnAxis({x:0, y:1, z:0}, -phi * Math.PI / 180 );
        phi = 0; theta = 0;

        camera.position.x = z * Math.sin(camera.rotation.y);
        camera.position.y = z * Math.sin(-camera.rotation.x) * Math.cos(camera.rotation.y);
        camera.position.z = z * Math.cos(-camera.rotation.x) * Math.cos(camera.rotation.y);
    }

    function start3DCanvas() {
        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(60, width/height, 0.1, 1000);
        scene.add(camera);

        renderer = new THREE.WebGLRenderer();
        renderer.setSize(width, height);
        renderer.setClearColor( 0xeeeeff, 1);

        var geometry = new THREE.BoxGeometry(1,1,1);            // геометрия фигуры, задаем куб
        var material = new THREE.MeshLambertMaterial({          // материал куба, в данном случае - текстура
            map: THREE.ImageUtils.loadTexture('images/topoboi_com-36665.jpg')
        });

        cube = new THREE.Mesh(geometry, material);
        cube.updateMatrix();
        cube.overdraw = true;
        scene.add(cube);

        var ambientLight = new THREE.AmbientLight(0xffffff);    // освещение, без него куб будет черным
        scene.add(ambientLight);

        document.onkeydown = handleKeyDown;
        document.onkeyup = handleKeyUp;

        ThreeJS_div.appendChild(renderer.domElement);

        render();
    }

    // Запуск программы
    start3DCanvas();
}
