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
    var rectangleGeometry = new THREE.BoxGeometry(2, 2, 1);
    var rectangleMaterial = new THREE.MeshBasicMaterial({ color: 0x0000ff });
    var rectangle = new THREE.Mesh(rectangleGeometry, rectangleMaterial);
    rectangle.position.set(3, 0, 0.5);
    scene.add(rectangle);

    // Создаем шар
    var sphereGeometry = new THREE.BoxGeometry(2, 3, 1)
    var sphereMaterial = new THREE.MeshBasicMaterial({ color: 0xff0000 });
    var sphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
    sphere.position.set(-3, 0, 0.5);
    scene.add(sphere);

    // Массив разрешенных к взаимодействию объектов
    var interactiveObjects = [rectangle, sphere];

    // Переменные для отслеживания перемещения объекта
    var isDragging = false;
    var selectedObject = null;
    var offset = new THREE.Vector3();

    // Функция для добавления границ черного цвета на объект при наведении мыши
    function addOutlineOnHover(event) {
        const obj = event.target;

        const outlineMaterial = new THREE.MeshBasicMaterial({ color: 0x000000, side: THREE.BackSide });
        const outlineGeometry = new THREE.BufferGeometry().fromGeometry(obj.geometry);
        const outlineMesh = new THREE.Mesh(outlineGeometry, outlineMaterial);
        outlineMesh.scale.set(1.05, 1.05, 1.05);
        obj.add(outlineMesh);

        obj.addEventListener('mouseout', () => {
            obj.remove(outlineMesh);
        });
    }

    var outlineMeshSelectedObject = null;

    var selectedObjectRotateX = false;
    var selectedObjectRotateY = false;
    let selectedObjectRotatePoint = {
        point0deg: {x: 'undefined', y: 'undefined'},
        point90deg: {x: 'undefined', y: 'undefined'},
        isEmptyPoint: function () {
            if (this.point0deg.x === 'undefined' && this.point0deg.y === 'undefined')
                return true;
            return false;
        },
        clear: function () {
            this.point0deg.x = 'undefined';
            this.point0deg.y = 'undefined';
            this.point90deg.x = 'undefined';
            this.point90deg.y = 'undefined';
        },
        addPoint0deg: function (x, y) {
            this.point0deg.x = x;
            this.point0deg.y = y;
        },
        addPoint90deg: function (x, y) {
            this.point90deg.x = x;
            this.point90deg.y = y;
        }
    };

    // Обновляем новое положение объекта
    function updatePositionSelectedObject (newX, newY, reserveX, reserveY) {
        if (!Number.isInteger(selectedObject.rotation.z / Math.PI))
        {
            selectedObject.position.set(newX, newY, selectedObject.position.z);
        }
        else
        {
            selectedObject.position.set(reserveX, reserveY, selectedObject.position.z);
        }
    }

    function getIntersects(event)
    {
        var mouse = new THREE.Vector2();
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        var raycaster = new THREE.Raycaster();
        raycaster.setFromCamera(mouse, camera);

        return raycaster.intersectObjects(interactiveObjects);
    }

    // Поворот объектов вокруг своей оси
    document.getElementById('scene-container').addEventListener('wheel', (event) => {
        if (selectedObject != null)
        {
            const direction = event.deltaY > 0 ? 1 : -1;
            selectedObject.rotation.z += (Math.PI / 2) * direction;

            if (selectedObject.rotation.z / Math.PI === 2 || selectedObject.rotation.z / Math.PI === -2)
                selectedObject.rotation.z = 0;

            // Проверка на необходимость "доворота" фигуры, чтобы попасть в сетку
            if (selectedObjectRotateX || selectedObjectRotateY)
            {
                if (selectedObjectRotatePoint.isEmptyPoint())
                {
                    selectedObjectRotatePoint.addPoint0deg(selectedObject.position.x, selectedObject.position.y);

                    var rotateX = selectedObjectRotateX ? 0.5 : 0;
                    var rotateY = selectedObjectRotateY ? 0.5 : 0;

                    selectedObjectRotatePoint.addPoint90deg(selectedObject.position.x + rotateX - rotateY, selectedObject.position.y + rotateY - rotateX)
                }

                updatePositionSelectedObject(selectedObjectRotatePoint.point90deg.x, selectedObjectRotatePoint.point90deg.y, selectedObjectRotatePoint.point0deg.x, selectedObjectRotatePoint.point0deg.y);
            }

        }
    });

    function onMouseMove(event) {
        if (isDragging) {
            var intersects = getIntersects(event);

            if (intersects.length > 0) {
                var intersectionPoint = intersects[0].point;

                // Учитываем половину ширины и половину длины объекта при ограничении перемещения
                var halfWidth = selectedObject.geometry.parameters.width / 2;
                var halfHeight = selectedObject.geometry.parameters.height / 2;
                var newX = Math.max(Math.min(intersectionPoint.x, gridSizeX / 2 - 0.5 - halfWidth), -gridSizeX / 2 + 0.5 + halfWidth);
                var newY = Math.max(Math.min(intersectionPoint.y, gridSizeY / 2 - 0.5 - halfHeight), -gridSizeY / 2 + 0.5 + halfHeight);

                var rotateWidth = selectedObjectRotateX ? 0.5 : 0;
                var rotateHeight = selectedObjectRotateY ? 0.5 : 0;

                updatePositionSelectedObject(Math.round(newX) + rotateHeight, Math.round(newY) + rotateWidth, Math.round(newX) + rotateWidth, Math.round(newY) + rotateHeight);
            }
        }
    }

    function onMouseDown(event) {
        var intersects = getIntersects(event);

        if (intersects.length > 0) {
            isDragging = true;
            selectedObject = intersects[0].object;
            var intersectionPoint = intersects[0].point;
            offset.copy(intersectionPoint).sub(selectedObject.position);

            const outlineMaterial = new THREE.MeshBasicMaterial({ color: 0x000000, side: THREE.BackSide });
            outlineMeshSelectedObject = new THREE.Mesh(selectedObject.geometry, outlineMaterial);
            outlineMeshSelectedObject.scale.set(1.05, 1.05, 1.05);
            selectedObject.add(outlineMeshSelectedObject);

            selectedObjectRotateX = selectedObject.geometry.parameters.width % 2 === 0;
            selectedObjectRotateY = selectedObject.geometry.parameters.height % 2 === 0;
        }

        controls.enableZoom = false;
    }

    function onMouseUp() {
        isDragging = false;

        if (outlineMeshSelectedObject) {
            selectedObject.remove(outlineMeshSelectedObject);
            outlineMeshSelectedObject = null;
        }

        selectedObject = null;
        selectedObjectRotateX = false;
        selectedObjectRotateY = false;
        selectedObjectRotatePoint.clear();
        controls.enableZoom = true;
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