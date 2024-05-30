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
    /*#controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1;
    }*/

    button {
        display: block;
        margin-bottom: 5px;
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
    <div id="controls">
        <button id="zoomIn">Zoom In</button>
        <button id="zoomOut">Zoom Out</button>
        <button id="moveLeft">Move Left</button>
        <button id="moveRight">Move Right</button>
    </div>
</div>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.130.2/examples/js/controls/OrbitControls.js"></script>-->

<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/examples/js/controls/OrbitControls.js"></script>
<script>
    // Создание сцены
    const scene = new THREE.Scene();
    scene.background = new THREE.Color('#F0F8FF');
    const sceneContainer = document.getElementById('scene-container');

    const camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 0.1, 1000 );
    camera.position.z = 5;
    camera.position.y = -10;

    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(sceneContainer.clientWidth, sceneContainer.clientHeight);
    sceneContainer.appendChild(renderer.domElement);

    // Добавляем масштабирование камерой
    var controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableRotate = false;

    //-----------------------------------------------

    // Создаем материал для ячеек сетки
    var gridSizeX = 25;
    var gridSizeY = 25;

    // Создаем сетку
    var gridGeometry = new THREE.PlaneBufferGeometry(1, 1);
    var gridMesh = new THREE.Group();

    var gridColor = new THREE.Color('#808080'); // Серый цвет
    var cellMaterial = new THREE.MeshBasicMaterial({ color: gridColor, transparent: true, opacity: 0.5, side: THREE.DoubleSide }); // Один цвет и полупрозрачность
    var edgesMaterial = new THREE.LineBasicMaterial({ color: 0x000000 }); // Черный цвет для границ

    for (var i = 0; i < gridSizeX * gridSizeY; i++) {
        var cellGeometry = new THREE.BoxBufferGeometry(1, 1, 0.01);
        var cell = new THREE.Mesh(cellGeometry, cellMaterial);
        var edges = new THREE.LineSegments(new THREE.EdgesGeometry(cellGeometry), edgesMaterial);
        cell.position.set(i % gridSizeX - gridSizeX / 2 + 0.5, Math.floor(i / gridSizeX) - gridSizeY / 2 + 0.5, 0);
        gridMesh.add(cell);
        cell.add(edges); // Добавляем границы к ячейке
    }

    // Добавили сетку на сцену
    scene.add(gridMesh);
    //-----------------------------------------------


    // Тестовый куб
    const geometry = new THREE.BoxGeometry(1, 1, 1);
    const material = new THREE.MeshBasicMaterial( { color: 0x00ff00 } );
    const cube = new THREE.Mesh( geometry, material );
    cube.position.set(0, 0, 0.5);
    scene.add( cube );

    // Создаем прямоугольник
    var rectangleGeometry = new THREE.BoxGeometry(3, 2, 1);
    var rectangleMaterial = new THREE.MeshPhongMaterial({ color: 0x0000ff });
    var rectangle = new THREE.Mesh(rectangleGeometry, rectangleMaterial);
    rectangle.position.set(3, 0, 0.5);
    scene.add(rectangle);

    // Создаем шар
    var sphereGeometry = new THREE.BoxGeometry(1, 2, 1)
    var sphereMaterial = new THREE.MeshPhongMaterial({ color: 0xff0000 });
    var sphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
    sphere.position.set(-3, 0, 0.5);
    scene.add(sphere);

    // Массив разрешенных к взаимодействию объектов
    var interactableObjects = [rectangle, sphere];

    // Переменные для отслеживания перемещения объекта
    var isDragging = false;
    var selectedObject = null;
    var offset = new THREE.Vector3();

    function onMouseMove(event) {
        if (isDragging) {
            var mouse = new THREE.Vector2();
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

            var raycaster = new THREE.Raycaster();
            raycaster.setFromCamera(mouse, camera);

            var intersects = raycaster.intersectObjects(interactableObjects);

            if (intersects.length > 0) {
                var intersectionPoint = intersects[0].point;

                // Учитываем половину ширины и половину длины объекта при ограничении перемещения
                var halfWidth = selectedObject.geometry.parameters.width / 2;
                var halfHeight = selectedObject.geometry.parameters.height / 2;
                var newX = Math.max(Math.min(intersectionPoint.x, gridSizeX / 2 - 0.5 - halfWidth), -gridSizeX / 2 + 0.5 + halfWidth);
                var newY = Math.max(Math.min(intersectionPoint.y, gridSizeY / 2 - 0.5 - halfHeight), -gridSizeY / 2 + 0.5 + halfHeight);

                // Обновляем новое положение объекта
                selectedObject.position.set(Math.round(newX), Math.round(newY) + 0.5, selectedObject.position.z);
            }
        }
    }

    function onMouseDown(event) {
        var mouse = new THREE.Vector2();
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        var raycaster = new THREE.Raycaster();
        raycaster.setFromCamera(mouse, camera);

        var intersects = raycaster.intersectObjects(interactableObjects);

        if (intersects.length > 0) {
            isDragging = true;
            selectedObject = intersects[0].object;
            var intersectionPoint = intersects[0].point;
            offset.copy(intersectionPoint).sub(selectedObject.position);
        }
    }

    function onMouseUp() {
        isDragging = false;
        selectedObject = null;
    }

    document.addEventListener('mousemove', onMouseMove, false);
    document.addEventListener('mousedown', onMouseDown, false);
    document.addEventListener('mouseup', onMouseUp, false);

    //------------------------------------
    function animate() {
        requestAnimationFrame( animate );

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