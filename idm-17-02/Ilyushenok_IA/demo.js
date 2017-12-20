var container, stats;
var camera, scene, raycaster, renderer;
var mouse = new THREE.Vector2(), INTERSECTED;
var radius = 100, theta = 0;
init();
animate();
function init() {
	jsdemo = document.getElementById('jsdemo');
	container = document.createElement( 'div' );
	container.style.width = '640px';
	container.style.height = '480px';
	container.style.marginLeft = 'auto';
	container.style.marginRight = 'auto';
	jsdemo.appendChild( container );
	camera = new THREE.PerspectiveCamera( 90, 640/480, 1, 10000 );
	scene = new THREE.Scene();
	scene.background = new THREE.Color( 0xffffff );
	var light = new THREE.DirectionalLight( 0xffffff, 1 );
	light.position.set( 1, 1, 1 ).normalize();
	scene.add( light );
	var geometryBox = new THREE.BoxBufferGeometry( 20, 20, 20 );
	var geometrySphere = new THREE.SphereBufferGeometry(20, 16, 16);
	var geometryCyl = new THREE.CylinderBufferGeometry(20, 20, 20, 16);
	for ( var i = 0; i < 1024; i ++ ) {
		var object;
		var r = Math.random();
		if(r < 1.0/3){
			object = new THREE.Mesh( geometryBox, new THREE.MeshLambertMaterial( {color: Math.random() * 0xffffff } ) );
		}else if(r < 2.0/3){
			object = new THREE.Mesh( geometrySphere, new THREE.MeshLambertMaterial( {color: Math.random() * 0xffffff } ) );
		}else{
			object = new THREE.Mesh( geometryCyl, new THREE.MeshLambertMaterial( {color: Math.random() * 0xffffff } ) );
		}
		
		object.position.x = Math.random() * 800 - 400;
		object.position.y = Math.random() * 800 - 400;
		object.position.z = Math.random() * 800 - 400;
		object.rotation.x = Math.random() * 2 * Math.PI;
		object.rotation.y = Math.random() * 2 * Math.PI;
		object.rotation.z = Math.random() * 2 * Math.PI;
		object.scale.x = Math.random() + 0.5;
		object.scale.y = Math.random() + 0.5;
		object.scale.z = Math.random() + 0.5;
		scene.add(object);
	}
	renderer = new THREE.WebGLRenderer();
	renderer.setPixelRatio( window.devicePixelRatio );
	renderer.setSize(640, 480);
	container.appendChild(renderer.domElement);
}

function animate() {
	requestAnimationFrame( animate );
	render();
}

function render() {
	theta += 0.1;
	camera.position.x = radius * Math.sin( THREE.Math.degToRad( theta ) );
	camera.position.y = radius * Math.sin( THREE.Math.degToRad( theta ) );
	camera.position.z = radius * Math.cos( THREE.Math.degToRad( theta ) );
	camera.lookAt( scene.position );
	camera.updateMatrixWorld();
	renderer.render( scene, camera );
}