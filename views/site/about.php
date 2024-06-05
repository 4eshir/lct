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
<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/examples/js/cameras/CinematicCamera.js"></script>
<script>
    // Создание сцены
    const scene = new THREE.Scene();
    scene.background = new THREE.Color('#F0F8FF');
    const sceneContainer = document.getElementById('scene-container');

    const camera = new THREE.PerspectiveCamera( 75, sceneContainer.clientWidth / sceneContainer.clientHeight, 1, 1000 );
    camera.position.z = 20;
    camera.position.y = -10;

    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(sceneContainer.clientWidth, sceneContainer.clientHeight);
    sceneContainer.appendChild(renderer.domElement);

    // Добавляем масштабирование камерой
    var controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableRotate = false;

    //-----------------------------------------------

    const drift = 0.5;

    // Создаем материал для ячеек сетки
    var gridSizeX = 9;
    var gridSizeY = 10;

    // Создаем сетку
    var gridGeometry = new THREE.PlaneBufferGeometry(1, 1);
    var gridMesh = new THREE.Group();

    var gridColor = new THREE.Color('#808080'); // Серый цвет

    var edgesMaterial = new THREE.LineBasicMaterial({ color: 0x000000 }); // Черный цвет для границ
    var driftCellX = gridSizeX % 2 == 0 ? 0 : drift;
    var driftCellY = gridSizeY % 2 == 0 ? 0 : drift;

    for (var i = 0; i < gridSizeX * gridSizeY; i++) {
        var cellGeometry = new THREE.BoxBufferGeometry(1, 1, 0.01);
        var cellMaterial = new THREE.MeshBasicMaterial({ color: gridColor, transparent: true, opacity: 0.5, side: THREE.DoubleSide }); // Один цвет и полупрозрачность
        var cell = new THREE.Mesh(cellGeometry, cellMaterial);
        var edges = new THREE.LineSegments(new THREE.EdgesGeometry(cellGeometry), edgesMaterial);
        cell.position.set(i % gridSizeX - gridSizeX / 2 + driftCellX, Math.floor(i / gridSizeX) - gridSizeY / 2 + driftCellY, 0);
        gridMesh.add(cell);
        cell.add(edges); // Добавляем границы к ячейке
    }

    // Добавили сетку на сцену
    scene.add(gridMesh);

    // Тестовые объекты для отладки
    //-----------------------------------------------
    const geometry = new THREE.BoxGeometry(1, 1, 1);
    const material = new THREE.MeshBasicMaterial( { color: 0x00ff00 } );
    const cube = new THREE.Mesh( geometry, material );
    cube.position.set(0, 0, 0.5);
    scene.add(cube);

    var rectangleGeometry = new THREE.BoxGeometry(2, 2, 1);
    var rectangleMaterial = new THREE.MeshBasicMaterial({ transparent: true, opacity: 0.8, color: 0x0000ff });
    var rectangle = new THREE.Mesh(rectangleGeometry, rectangleMaterial);
    rectangle.position.set(3, 0, 0.5);
    scene.add(rectangle);

    var sphereGeometry = new THREE.BoxGeometry(2, 3, 1)
    var sphereMaterial = new THREE.MeshBasicMaterial({ transparent: true, opacity: 0.8, color: 0xff0000, side: THREE.DoubleSide });
    var sphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
    sphere.position.set(-3, 0, 0.5);
    scene.add(sphere);


    // Основные механики
    //--------------------------------

    function init() {
        // !!! написать функцию инициализации объектов, не забыть про указание  interactiveObjects и axisZ
    }

    var dot = {
        x: 'undefined',
        y: 'undefined',
        addDot: function (x, y) {
            this.x = x;
            this.y = y;
        },
        isIntegerCoordinate: function () {
            return Number.isInteger(this.x) && Number.isInteger(this.y);
        },
        clearDot: function () {
            this.x = 'undefined';
            this.y = 'undefined';
        },
        isEmpty: function () {
            return this.x === 'undefined' || this.y === 'undefined';
        }
    }

    function isEqualsDots(anotherDot, otherDot) {
        if (!anotherDot || !otherDot)
            return false;
        return anotherDot.x == otherDot.x && anotherDot.y == otherDot.y;
    }

    // Массив разрешенных к взаимодействию объектов
    var interactiveObjects = [rectangle, sphere];

    // Переменные для отслеживания перемещения объекта
    var isDragging = false;
    var selectedObject = null;
    var axisZ = 2;   // Высота на которую будем поднимать объекты при перемещении
    var offset = new THREE.Vector3();

    var outlineMeshSelectedObject = null;
    var outlineMeshSelectedObjectHover = null;

    var selectedObjectRotateX = false;
    var selectedObjectRotateY = false;
    let selectedObjectRotatePoint = {
        point0deg: Object.create(dot),
        point90deg: Object.create(dot),
        isEmptyPoint: function () {
            return this.point0deg.isEmpty() || this.point90deg.isEmpty();
        },
        clear: function () {
            this.point0deg.clearDot();
            this.point90deg.clearDot();
        },
        addPoint0deg: function (x, y) {
            this.point0deg.addDot(x, y);
        },
        addPoint90deg: function (x, y) {
            this.point90deg.addDot(x, y);
        },
        getPoint: function () {
            if (isRotation())
            {
                return this.point90deg;
            }

            return this.point0deg;
        }
    };

    //const target = new THREE.Vector3(0, 0, 0); // Целевая точка, на которую будет смотреть камера
    const radius = 0.1;
    let theta = 0;
    function updateCamera(event)
    {
        //camera.rotateZ(Math.PI);

        //camera.position.y = camera.position.y - camera.position.y * drift;
        //const direction = Math.PI/2 > 0 ? 1 : -1;
        //camera.position.x += gridSizeX / 2;
        camera.position.y = 10;
        theta += 0.1;
        camera.rotateZ(-Math.PI);
        //camera.rotateX(Math.cos( THREE.MathUtils.degToRad( theta ) ) );
        //camera.lookAt(cube.position);

console.log(camera.position.x, camera.position.y , camera.position.z)
console.log(camera.rotation.x, camera.rotation.y , camera.rotation.z)
        //camera.position.x = 10;
        //
        //camera.rotateY();

        /*const radius = 5; // Радиус вращения камеры вокруг объекта
        camera.position.x = target.x + radius * Math.cos(cube.rotation.y);
        camera.position.z = target.z + radius * Math.sin(cube.rotation.y);

        camera.lookAt(target);*/
        /*theta += 0.1;

        camera.position.x = radius * Math.sin( THREE.MathUtils.degToRad( theta ) );
        camera.position.y = radius * Math.sin( THREE.MathUtils.degToRad( theta ) );
        camera.position.z = radius * Math.cos( THREE.MathUtils.degToRad( theta ) );


        camera.updateMatrixWorld();*/
        //theta += 0.001;
        //console.log(camera.rotation.x, camera.rotation.y , camera.rotation.z)
        //console.log(Math.cos( THREE.MathUtils.degToRad( theta ) ));
        //camera.rotateX(Math.cos( THREE.MathUtils.degToRad( theta ) ) );
        //camera.rotation.y = radius * Math.sin( THREE.MathUtils.degToRad( theta ) );
        //camera.position.z = radius * Math.cos( THREE.MathUtils.degToRad( theta ) );

        camera.updateMatrixWorld();
    }

    function getIntersects(event)
    {
        var mouse = new THREE.Vector2();
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        var raycaster = new THREE.Raycaster();
        raycaster.params.PointsCloud = { threshold: 10 };
        raycaster.setFromCamera(mouse, camera);

        return raycaster.intersectObjects(interactiveObjects);
    }

    // Функция для добавления границ на объект при наведении
    function addOutlineOnHover(event)
    {
        if (!isDragging)
        {
            var intersects = getIntersects(event);

            if (outlineMeshSelectedObjectHover) {
                selectedObject.remove(outlineMeshSelectedObjectHover);
                outlineMeshSelectedObjectHover = null;
                selectedObject = null;
            }

            if (intersects.length > 0) {
                selectedObject = intersects[0].object;
                var intersectionPoint = intersects[0].point;
                offset.copy(intersectionPoint).sub(selectedObject.position);

                const outlineMaterial = new THREE.MeshBasicMaterial({color: 0x0fff00, side: THREE.BackSide});
                outlineMeshSelectedObjectHover = new THREE.Mesh(selectedObject.geometry, outlineMaterial);
                outlineMeshSelectedObjectHover.scale.set(1.05, 1.05, 1.05);
                selectedObject.add(outlineMeshSelectedObjectHover);
            }
        }
    }

    // Обновляем новое положение объекта
    function updatePositionSelectedObject (newDot, newZ = null)
    {
        if (newZ === null)
        {
            newZ = axisZ;
        }

        selectedObject.position.set(newDot.x, newDot.y, newZ);

        setColorGridMesh(); // Обновляем тени
    }

    // Поворот объектов вокруг своей оси
    document.getElementById('scene-container').addEventListener('wheel', (event) => {
        if (selectedObject && isDragging)
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

                    var rotateX = selectedObjectRotateX ? drift : 0;
                    var rotateY = selectedObjectRotateY ? drift : 0;

                    selectedObjectRotatePoint.addPoint90deg(selectedObject.position.x + rotateX - rotateY, selectedObject.position.y + rotateY - rotateX)
                }

                updatePositionSelectedObject(selectedObjectRotatePoint.getPoint());
            }
        }
    });

    function isRotation()
    {
        return Number.isInteger(selectedObject.rotation.z / Math.PI);
    }

    // Отрисовка тени на сцене
    function setColorGridMesh()
    {
        var widthObject = isRotation() ? selectedObject.geometry.parameters.width : selectedObject.geometry.parameters.height;
        var heightObject = isRotation() ? selectedObject.geometry.parameters.height : selectedObject.geometry.parameters.width;

        var dotsObject = [];
        for (var i = 0; i < widthObject * heightObject; i++)
        {
            var oneDot = Object.create(dot);
            oneDot.addDot(i % widthObject - widthObject / 2 + drift + selectedObject.position.x, Math.floor(i / widthObject) - heightObject / 2 + drift + selectedObject.position.y)
            dotsObject.push(oneDot);
        }

        var cellDot = Object.create(dot);
        gridMesh.children.forEach((cell) => {
            cellDot.addDot(cell.position.x, cell.position.y);
            cell.material.color.set('#808080');
            for (var i = 0; i < dotsObject.length; i++)
            {
                if(isEqualsDots(cellDot, dotsObject[i]))
                {
                    cell.material.color.set('#00FF00');
                    delete dotsObject[i];
                    break;
                }
            }
        })
    }

    // Логика перемещения объекта
    function dragAndDrop(event)
    {
        if (isDragging)
        {
            var intersects = getIntersects(event);

            if (intersects.length > 0) {
                var intersectionPoint = intersects[0].point;

                // Учитываем половину ширины и половину длины объекта при ограничении перемещения
                var halfWidth = selectedObject.geometry.parameters.width / 2;
                var halfHeight = selectedObject.geometry.parameters.height / 2;
                var newX = Math.max(Math.min(intersectionPoint.x, gridSizeX / 2 - drift - halfWidth), -gridSizeX / 2 + drift + halfWidth);
                var newY = Math.max(Math.min(intersectionPoint.y, gridSizeY / 2 - drift - halfHeight), -gridSizeY / 2 + drift + halfHeight);

                var rotateWidth = selectedObjectRotateX ? drift : 0;
                var rotateHeight = selectedObjectRotateY ? drift : 0;

                var coordinate = Object.create(selectedObjectRotatePoint);
                coordinate.addPoint0deg(Math.round(newX) + rotateHeight, Math.round(newY) + rotateWidth);
                coordinate.addPoint90deg(Math.round(newX) + rotateWidth, Math.round(newY) + rotateHeight);
                updatePositionSelectedObject(coordinate.getPoint());

                setColorGridMesh();
            }
        }
    }

    function onMouseDown()
    {
        if(selectedObject)
        {
            isDragging = true;

            const outlineMaterial = new THREE.MeshBasicMaterial({color: 0x000000, side: THREE.BackSide});
            outlineMeshSelectedObject = new THREE.Mesh(selectedObject.geometry, outlineMaterial);
            outlineMeshSelectedObject.scale.set(1.05, 1.05, 1.05);
            selectedObject.add(outlineMeshSelectedObject);

            /*var oneDot = Object.create(dot);
            oneDot.addDot(selectedObject.position.x, selectedObject.position.y)
            updatePositionSelectedObject(oneDot);*/

            selectedObjectRotateX = selectedObject.geometry.parameters.width % 2 === 0;
            selectedObjectRotateY = selectedObject.geometry.parameters.height % 2 === 0;

            controls.enableZoom = false;
        }

        updateCamera(event);
    }

    function onMouseUp()
    {
        isDragging = false;

        if (outlineMeshSelectedObject) {
            selectedObject.remove(outlineMeshSelectedObject);
            outlineMeshSelectedObject = null;
        }

        if (selectedObject)
        {
            var newDot = new dot.addDot(selectedObject.position.x, selectedObject.position.y);
            for(var z = axisZ; z > 0.5; z -= 0.01)
            {
                updatePositionSelectedObject(newDot, z);
            }

            selectedObjectRotateX = false;
            selectedObjectRotateY = false;
            selectedObjectRotatePoint.clear();
        }

        controls.enableZoom = true;
    }

    document.addEventListener('mousemove', dragAndDrop, false);
    document.addEventListener('mousedown', onMouseDown, false);
    document.addEventListener('mouseup', onMouseUp, false);
    document.addEventListener('mousemove', addOutlineOnHover, false);

    //------------------------------------

    function animate()
    {
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