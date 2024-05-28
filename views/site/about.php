<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    body { margin: 0; }
    canvas { width: 100%; height: 100% }
    #scene-container {
        width: 800px;
        height: 600px;
        margin: 0 auto;
    }
</style>

<script type="importmap">
  {
    "imports": {
      "three": "https://cdn.jsdelivr.net/npm/three@v0.164.1/build/three.module.js",
      "three/addons/": "https://cdn.jsdelivr.net/npm/three@v0.164.1/examples/jsm/"
    }
  }
</script>
<!-- <script type="module" src="../../web/js/main-scene.jsm"></script> -->

<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

    <code><?= __FILE__ ?></code> -->

    <div id="scene-container"></div>
</div>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.130.2/examples/js/controls/OrbitControls.js"></script>-->

<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/examples/js/controls/OrbitControls.js"></script>
<script>
    // Создание сцены
    const scene = new THREE.Scene();
    scene.background = new THREE.Color('#F0F8FF');
    const camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 0.1, 1000 );

    const renderer = new THREE.WebGLRenderer();
    renderer.setSize( window.innerWidth, window.innerHeight );
    document.body.appendChild( renderer.domElement );

    const geometry = new THREE.BoxGeometry( 1, 1, 1 );
    const material = new THREE.MeshBasicMaterial( { color: 0x00ff00 } );
    const cube = new THREE.Mesh( geometry, material );
    scene.add( cube );

    camera.position.z = 5;
    camera.position.y = -10;

    // Создаем материал для ячеек сетки
    var gridSizeX = 25;
    var gridSizeY = 25;
    var gridColors = [];

    // Create a group for the grid
    var gridGeometry = new THREE.PlaneBufferGeometry(1, 1);
    var gridMesh = new THREE.Group();

    var gridColor = new THREE.Color('#808080'); // Серый цвет
    var cellMaterial = new THREE.MeshBasicMaterial({ color: gridColor, transparent: true, opacity: 0.5, side: THREE.DoubleSide }); // Один цвет и полупрозрачность
    var edgesMaterial = new THREE.LineBasicMaterial({ color: 0x000000 }); // Черный цвет для границ

    for (var i = 0; i < gridSizeX * gridSizeY; i++) {
        var cellGeometry = new THREE.BoxBufferGeometry(1, 1, 0.01);
        var cell = new THREE.Mesh(cellGeometry, cellMaterial);
        var edges = new THREE.LineSegments(new THREE.EdgesGeometry(cellGeometry), edgesMaterial);
        cell.position.set(i % gridSizeX - gridSizeX / 2 + 0.5, Math.floor(i / gridSizeX) - gridSizeY / 2 + 0.5, -1);
        gridMesh.add(cell);
        cell.add(edges); // Добавляем границы к ячейке
    }

    // Add the grid to the scene
    scene.add(gridMesh);

    // Добавляем управление камерой
    var controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.25;
    controls.enableZoom = true;

    let raycaster = new THREE.Raycaster();
    let selectedObject = null;
    let isMovingObject = false;
    const mouse = new THREE.Vector2();

    function onMouseDown(event) {
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0 && intersects[0].object.userData.isMovable) {
            selectedObject = intersects[0].object;
            isMovingObject = true;
        }
    }

    function onMouseMove(event) {
        if (isMovingObject) {
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);

            const intersects = raycaster.intersectObjects(scene.children, true);

            if (intersects.length > 0) {
                selectedObject.position.copy(intersects[0].point);
            }
        }
    }

    function onMouseUp() {
        isMovingObject = false;
    }

    window.addEventListener('mousedown', onMouseDown, false);
    window.addEventListener('mousemove', onMouseMove, false);
    window.addEventListener('mouseup', onMouseUp, false);

    function animate() {
        requestAnimationFrame( animate );

        //cube.rotation.x += 0.01;
        //cube.rotation.y += 0.01;

        controls.update();
        renderer.render( scene, camera );
    }
    animate();
</script>

<script type="module">
    //import * as THREE from 'three';
    //import * as THREE from '../../node_modules/three';
    /*import { OrbitControls } from '../../node_modules/three/addons/controls/OrbitControls.js';
    import { GLTFLoader } from '../../node_modules/three/addons/loaders/GLTFLoader.js';



    const controls = new OrbitControls( camera, renderer.domElement );
    const loader = new GLTFLoader();*/
</script>