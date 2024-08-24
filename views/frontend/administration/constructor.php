<?php

/** @var $model ConstructorTerritoryForm */

use app\models\forms\ConstructorTerritoryForm;
use app\models\work\ObjectWork;

?>

<style>
    #scene-container {
        height: 600px;
    }
    #scene-container canvas {
        border-radius: 15px;
    }
    .template-wrapper-desc {
        padding: 15px;
        background-color: white;
        border-radius: 15px;
        margin: 10px 0;
        flex: 4;
    }
    .btn-primary {
        margin: 5px;
    }
</style>

<div class="sticky-block template-wrapper-desc">
    <div><h4>Добро пожаловать в конструктор МАФ!</h4></div>
    <div>🔄 <b>Для поворота сцены:</b> зажмите ЛКМ и смахните в нужную сторону</div>
    <div>🔍 <b>Для приближения/отдаления:</b> прокрутите колесико мыши</div>
    <div>➕ <b>Добавление объектов:</b> нажмите на кнопку (расположенные ниже сцены) с названием соответствующего МАФ</div>
    <div>🖱️ <b>Для перемещения объектов:</b> наведите курсор на объект, зажмите ЛКМ и перетащите объект</div>
    <div>🔄 <b>Для вращения объектов:</b> используйте колесико мыши (не отпуская ЛКМ)</div>
    <div>🗑️ <b>Удаление объектов:</b> выделенный объект можно удалить нажатием ПКМ</div>
    <div>⚠️ <b>Обратите внимание:</b> невозможно установить объект поверх другого. Если объект "завис" в воздухе, повторите описанные действия, чтобы переместить его в нужное место</div>
</div>

<div class="template-wrapper-desc" hidden>
    <h3>Настроения жителей</h3>
    <span><b>Рекреация:</b> <?= $model->averageMindset[ObjectWork::TYPE_RECREATION] ?>% </span><br>
    <span><b>Спорт:</b> <?= $model->averageMindset[ObjectWork::TYPE_SPORT] ?>% </span><br>
    <span><b>Развитие:</b> <?= $model->averageMindset[ObjectWork::TYPE_EDUCATION] ?>% </span><br>
    <span><b>Игры:</b> <?= $model->averageMindset[ObjectWork::TYPE_GAME] ?>% </span>
    <div style="margin-top: 10px;"><b>Стоимость размещения объектов:</b> <span id="cost">0</span> ₽</div>
</div>

<div id="scene-container"></div>

<div style="display:flex; justify-content: space-between">
    <div class="template-wrapper-desc" style="margin-right: 10px; text-align: center;">
        <div style="margin: 10px;"><b>Спортивные объекты:</b></div>
        <div id="btn-sport"></div>
    </div>
    <div class="template-wrapper-desc" style="margin-right: 10px; text-align: center;">
        <div style="margin: 10px;"><b>Рекреационные объекты:</b></div>
        <div id="btn-recreation"></div>
    </div>
    <div class="template-wrapper-desc" style="margin-right: 10px; text-align: center;">
        <div style="margin: 10px;"><b>Игровые объекты:</b></div>
        <div id="btn-game"></div>
    </div>
    <div class="template-wrapper-desc" style="text-align: center;">
        <div style="margin: 10px;"><b>Обучающие объекты:</b></div>
        <div id="btn-educational"></div>
    </div>
</div>

<?php
    $jsonString = ObjectWork::getAllObjectsJson();
    $data = json_decode($jsonString, true);
    $buttons = [];

    foreach ($data['data'] as $item) {
        $colors = [
            1 => ['#9dcae5', '#2b6a92'],
            2 => ['#b7e2ae', '#3b7233'],
            3 => ['#d4a4de', '#823f89'],
            4 => ['#faf0ae', '#74762f'],
        ];

        $color = $colors[$item['type']][0];
        $colorHover = $colors[$item['type']][1];
        $style = 'background-color: '.$color.'!important; border-color: black; color: black; width: 120px;';

        $button = '<button class="btn btn-primary" style="'.$style.'" 
            onmouseover="this.style.backgroundColor=\''.$colorHover.'\'" 
            onmouseout="this.style.backgroundColor=\''.$color.'\'" 
            onclick="addObject(' . $item['id'] . ', ' . $item['width'] . ', ' . $item['length'] . ', ' . $item['height'] . ', \'' . $item['link'] . '\', ' . $item['cost'] . ')">'
            . $item['name'] . '</button>';

        $blockId = [
            1 => 'btn-recreation',
            2 => 'btn-sport',
            3 => 'btn-educational',
            4 => 'btn-game',
        ][$item['type']];

        $buttons[$blockId][] = $button;
    }

    foreach ($buttons as $id => $btnList) {
        echo '<script>
            var block = document.getElementById("'.$id.'");

            if (block) {
                block.innerHTML += `'.implode('', $btnList).'`;
            }
        </script>';
    }
?>

<script>
    var gridSizeX = 20;
    var gridSizeY = 15;
    var gridSizeZ = 10;
</script>

<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/examples/js/loaders/GLTFLoader.js"></script>
<script>
    // Создание сцены
    const scene = new THREE.Scene();
    scene.background = new THREE.Color('#F0F8FF');
    const sceneContainer = document.getElementById('scene-container');

    const camera = new THREE.PerspectiveCamera( 75, sceneContainer.clientWidth / sceneContainer.clientHeight, 1, 1000 );
    camera.position.z = 5;
    camera.position.y = -5;
    camera.rotation.x = 1;

    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(sceneContainer.clientWidth, sceneContainer.clientHeight);
    sceneContainer.appendChild(renderer.domElement);

    //-----------------------------------------------

    const drift = 0.5;

    // Создаем сетку
    var gridGeometry = new THREE.PlaneBufferGeometry(1, 1);
    var gridMesh = new THREE.Group();

    var gridColor = new THREE.Color('#228B22'); // Серый цвет

    var edgesMaterial = new THREE.LineBasicMaterial({ color: 0x000000 }); // Черный цвет для границ
    var driftCellX = gridSizeX % 2 == 0 ? 0 : drift;
    var driftCellY = gridSizeY % 2 == 0 ? 0 : drift;

    const texturesUrl2 = 'https://as2.ftcdn.net/v2/jpg/01/53/35/31/1000_F_153353183_XqeFW50gMRHdTcHWuqlEEkrYPCvI2VOP.jpg';
    const textureLoader2 = new THREE.TextureLoader();
    const texture2 = textureLoader2.load(texturesUrl2);

    for (var i = 0; i < gridSizeX * gridSizeY; i++) {
        var cellGeometry = new THREE.PlaneBufferGeometry(1, 1);
        var cellMaterial = new THREE.MeshBasicMaterial({
            map: texture2,
            transparent: true,
            opacity: 1,
            side: THREE.DoubleSide,
        });
        var cell = new THREE.Mesh(cellGeometry, cellMaterial);
        cell.position.set(i % gridSizeX - gridSizeX / 2 + driftCellX, Math.floor(i / gridSizeX) - gridSizeY / 2 + driftCellY, 0);

        gridMesh.add(cell);
    }

    // Добавили сетку на сцену
    scene.add(gridMesh);

    // Создаем фон
    //const texturesUrl = 'https://st3.depositphotos.com/2419757/31826/v/450/depositphotos_318267308-stock-illustration-street-with-buildings-and-cars.jpg';
    const texturesUrl = '/img/3036.jpg';
    const textureLoader = new THREE.TextureLoader();
    const texture = textureLoader.load(texturesUrl);

    const planesData = [
        { size: [gridSizeX, gridSizeZ / 2], position: [-driftCellY, gridSizeY / 2 + driftCellX, gridSizeZ / 4], rotation: [Math.PI / 2, 0, 0] },
        { size: [gridSizeY, gridSizeZ / 2], position: [-(gridSizeX / 2 + driftCellY), driftCellX, gridSizeZ / 4], rotation: [0, Math.PI / 2, Math.PI / 2] },
        { size: [gridSizeY, gridSizeZ / 2], position: [gridSizeX / 2 - driftCellY, -driftCellX, gridSizeZ / 4], rotation: [0, -Math.PI / 2, -Math.PI / 2] },
        { size: [gridSizeX, gridSizeZ / 2], position: [-driftCellY, -(gridSizeY / 2 + driftCellX), gridSizeZ / 4], rotation: [-Math.PI / 2, 0, Math.PI] }
    ];

    const planes = planesData.map(data => {
        const geometry = new THREE.PlaneGeometry(...data.size);
        const material = new THREE.MeshBasicMaterial({ map: texture });
        const plane = new THREE.Mesh(geometry, material);
        plane.position.set(...data.position);
        plane.rotation.set(...data.rotation);
        scene.add(plane);
    });

    //-----------------------------------------------

    // Добавление объектов
    function addObject(idObject, width, lenght, height, link, name)
    {
        selectedObject = null;
        blockObjectSelection = null;

        const loader = new THREE.GLTFLoader();
        var rotateX = (lenght % 2 === 0) ? drift : 0;
        var rotateY = (width % 2 === 0) ? drift : 0;

        const randomColor = Math.floor(Math.random() * 16777215).toString(16);
        var material = new THREE.MeshBasicMaterial({transparent: true, color: parseInt(randomColor, 16), side: THREE.DoubleSide });

        if (height / 3 > axisZ || gridSizeZ < 10 + height)
        {
            axisZ = height / 3;
            gridSizeZ = height + 10;
        }

        if (!link)
        {
            link = 'models/educational/качели-балансир Бревно пробный.glb';
        }

        loader.load(
            link,
            function (gltf) {
                const model = gltf.scene;
                // Найдем все материалы модели и установим для них текстуры
                model.traverse((child) => {
                    if (child.isMesh) {
                        if (child.material.map)
                        {
                            material = new THREE.MeshBasicMaterial({ map: child.material.map });
                        }
                        child.material = material;
                    }
                });
                model.scale.set(1, 1, 1);
                model.position.set(0 + rotateX, 0 + rotateY, 0);
                model.userData.name = name;

                // Добавляем модель в сцену
                scene.add(model);
                interactiveObjects.push(model);
            },
            undefined,
            function (error) {
                // Если модель отсутствует, то заменяем её на примитивный полигон (параллелепипед)
                const geometry = new THREE.BoxGeometry(lenght, width, height);
                const oneObject = new THREE.Mesh(geometry, material);

                oneObject.position.set(0 + rotateX, 0 + rotateY, height/2);
                oneObject.name = name;
                scene.add(oneObject);
                interactiveObjects.push(oneObject);
                console.error('Error loading 3D model', error);
            }
        );

        const costElement = document.getElementById('cost');
        costElement.textContent = parseFloat(costElement.textContent) + parseFloat(name);
    }

    // Основные механики
    //--------------------------------

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
    var interactiveObjects = [];

    // Переменные для отслеживания перемещения объекта
    var isDragging = false;
    var selectedObject = null;
    var isModel = false;
    var axisZ = 2;   // Высота на которую будем поднимать объекты при перемещении
    var offset = new THREE.Vector3();

    var boxHelper = null;

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
    var blockObjectSelection = null;
    var intersectionPoint = {x: 0, y: 0};

    // переменная для отслеживания поворота камеры
    var isRotateCamera = false;
    var isZoom = false;
    var degreeCamera = 0;
    var previousMouseX = 0;
    var previousMouseY = 0;

    // Направление по оси OX
    function directionX(event)
    {
        var currentMouseX = event.clientX;
        var direction = 1;

        if (currentMouseX < previousMouseX) {
            direction *=  -1;
        }

        previousMouseX = currentMouseX;
        return direction;
    }

    // Направление по оси OY
    function directionY(event)
    {
        var currentMouseY = event.clientY;
        var direction = 1;

        if (currentMouseY > previousMouseY) {
            direction *= -1;
        }

        previousMouseY = currentMouseY;
        return direction;
    }

    // Обновляем угол поворота камеры
    function whereGoCamera(event)
    {
        degreeCamera += 90 * directionX(event);
    }

    // Обновляем данные каеры для поворота
    function updateCamera()
    {
        if (Math.abs(degreeCamera) === 360 || degreeCamera === 0)
        {
            degreeCamera = 0;
            camera.position.set(0, -(gridSizeY / 2), gridSizeZ / 2);
            camera.rotation.set(1, 0, 0);
        }
        else if (degreeCamera === 90 || degreeCamera === -270)
        {
            camera.position.set(-(gridSizeY / 2), 0, gridSizeZ / 2);
            camera.rotation.set(0, -1, -Math.PI/2);
        }
        else if (Math.abs(degreeCamera) === 180)
        {
            camera.position.set(0, gridSizeX / 2, gridSizeZ / 2);
            camera.rotation.set(-1, 0, Math.PI);
        }
        else if (degreeCamera === -90 || degreeCamera === 270)
        {
            camera.position.set(gridSizeX / 2, 0, gridSizeZ / 2);
            camera.rotation.set(0, 1, Math.PI/2);
        }

        camera.updateMatrixWorld();
    }

    // Масштабирование камеры
    function zoom(event) {
        if (isZoom)
        {
            camera.position.z += event.deltaY > 0 ? 1 : -1;
            event.preventDefault();
        }
    }

    //---------------------------------------------

    function getIntersects(event)
    {
        var mouse = new THREE.Vector2();
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        var raycaster = new THREE.Raycaster();
        raycaster.params.PointsCloud = { threshold: 10 };
        raycaster.setFromCamera(mouse, camera);

        return raycaster.intersectObjects(interactiveObjects, true);
    }

    function findRootParent(obj) {
        let currentObj = obj;

        // Пока у текущего объекта есть родитель, двигаемся вверх по цепочке
        while (currentObj.parent) {
            if (currentObj.type === 'Group')
                break;
            currentObj = currentObj.parent;
        }

        return currentObj;
    }

    // Функция для добавления границ на объект при наведении
    function addOutlineOnHover(event)
    {
        if (!isDragging)
        {
            var intersects = getIntersects(event);

            selectedObject = null;
            isModel = false;

            if (boxHelper)
            {
                scene.remove(boxHelper);
                boxHelper = null;
            }

            if (intersects.length > 0) {
                selectedObject = intersects[0].object;

                if (selectedObject.type === 'undefined')
                {
                    selectedObject = selectedObject[0];
                }

                selectedObject = findRootParent(selectedObject);

                if (blockObjectSelection) {
                    if (blockObjectSelection !== selectedObject) {
                        intersects = null;
                        selectedObject = null;
                        isModel = false;
                    }
                }

                if (selectedObject)
                {
                    updateBoxHelper();
                }
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
        boxHelper.position.set(newDot.x, newDot.y, newZ);

        setColorGridMesh(); // Обновляем тени
    }

    // Поворот объектов вокруг своей оси
    document.getElementById('scene-container').addEventListener('wheel', (event) => {
        if (selectedObject && isDragging)
        {
            event.preventDefault();
            const direction = event.deltaY > 0 ? 1 : -1;
            selectedObject.rotation.z += (Math.PI / 2) * direction;

            if (Math.abs(selectedObject.rotation.z / Math.PI) === 2)
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

            updateBoxHelper();
        }
    });

    function isRotation()
    {
        return Number.isInteger(selectedObject.rotation.z / Math.PI);
    }

    function getWidthSelectedObject()
    {
        const boundingBox = new THREE.Box3().setFromObject(selectedObject);
        return Math.ceil(boundingBox.max.x - boundingBox.min.x);
    }

    function getLenghtSelectedObject()
    {
        const boundingBox = new THREE.Box3().setFromObject(selectedObject);
        return Math.ceil(boundingBox.max.y - boundingBox.min.y);
    }

    // Отрисовка тени на сцене
    function setColorGridMesh()
    {
        var widthObject = getWidthSelectedObject();
        var heightObject = getLenghtSelectedObject();

        var dotsObject = [];
        for (var i = 0; i < widthObject * heightObject; i++)
        {
            var oneDot = Object.create(dot);
            var x = i % widthObject - widthObject / 2 + driftCellX + selectedObject.position.x;
            var y = Math.floor(i / widthObject) - heightObject / 2 + driftCellY + selectedObject.position.y;

            oneDot.addDot(x, y);
            dotsObject.push(oneDot);
        }

        var color = '#00FF00';

        if (isFreedomPosition())
        {
            blockObjectSelection = null;
        }
        else
        {
            blockObjectSelection = selectedObject;
            color = '#FF0000';
        }

        var cellDot = Object.create(dot);
        gridMesh.children.forEach((cell) => {
            cellDot.addDot(cell.position.x, cell.position.y);
            cell.material.color.set(gridColor);
            for (var i = 0; i < dotsObject.length; i++)
            {
                if(isEqualsDots(cellDot, dotsObject[i]))
                {
                    cell.material.color.set(color);
                    delete dotsObject[i];
                    break;
                }
            }
        })
    }

    // Проверка пересечения
    function doSegmentsIntersect(firstSegmentMin, firstSegmentMax, secondSegmentMin, secondSegmentMax) {
        if (firstSegmentMax > secondSegmentMin && firstSegmentMin < secondSegmentMin
            || firstSegmentMax > secondSegmentMax && firstSegmentMin < secondSegmentMax)
        {
            return true;
        }

        return false;
    }

    // Определяет свободно ли поле для размещения
    function isFreedomPosition()
    {
        // Рассчитываем ограничивающий параллелипипед для объекта
        const boundingBox = new THREE.Box3().setFromObject(selectedObject);

        for (let i = 0; i < interactiveObjects.length; i++) {
            if (selectedObject.parent != interactiveObjects[i])
            {
                const boundingBoxOtherObject = new THREE.Box3().setFromObject(interactiveObjects[i]);

                // Сравниваем пресечение границ объектов
                if (doSegmentsIntersect(boundingBox.min.x, boundingBox.max.x, boundingBoxOtherObject.min.x, boundingBoxOtherObject.max.x)
                    && doSegmentsIntersect(boundingBox.min.y, boundingBox.max.y, boundingBoxOtherObject.min.y, boundingBoxOtherObject.max.y)) {
                    return false;
                }
            }
        }

        return true;
    }

    // Отрисовка границ объекта
    function updateBoxHelper()
    {
        scene.remove(boxHelper);
        boxHelper = new THREE.BoxHelper(selectedObject, 0x0fff00);
        scene.add(boxHelper);

        // Рассчет общих границ для всех объектов в группе
        var boundingBox = new THREE.Box3();
        selectedObject.traverse(function(obj) {
            if (obj.geometry) {
                obj.geometry.computeBoundingBox();
                boundingBox.expandByPoint(obj.geometry.boundingBox.min);
                boundingBox.expandByPoint(obj.geometry.boundingBox.max);
            }
        });

        // Подгоняем размеры и позицию оболочки
        boxHelper.scale.setFromMatrixScale(selectedObject.matrixWorld);
        boxHelper.position.set((boundingBox.min.x + boundingBox.max.x) / 2, (boundingBox.min.y + boundingBox.max.y) / 2, (boundingBox.min.z + boundingBox.max.z) / 2);
        //boxHelper.rotation.z = selectedObject.rotation.z;
        boxHelper.update();
    }

    // Логика перемещения объекта
    function dragAndDrop(event)
    {
        if (isDragging)
        {
            var intersects = getIntersects(event);

            // Учитываем половину ширины и половину длины объекта при ограничении перемещения
            var halfWidth = getWidthSelectedObject() / 2;
            var halfHeight = getLenghtSelectedObject() / 2;
            var maxX = gridSizeX / 2 - drift - halfWidth;
            var minX = -gridSizeX / 2 + drift + halfWidth;
            var maxY = gridSizeY / 2 - drift - halfHeight;
            var minY = -gridSizeY / 2 + drift + halfHeight;

            if (Math.abs(degreeCamera) === 360 || degreeCamera === 0) {
                intersectionPoint.x = Math.max(Math.min(intersectionPoint.x + directionX(event) * 0.1, maxX), minX);
                intersectionPoint.y = Math.max(Math.min(intersectionPoint.y + directionY(event) * 0.1, maxY), minY);
            } else if (Math.abs(degreeCamera) === 180) {
                intersectionPoint.x = Math.max(Math.min(intersectionPoint.x - directionX(event) * 0.1, maxX), minX);
                intersectionPoint.y = Math.max(Math.min(intersectionPoint.y - directionY(event) * 0.1, maxY), minY);
            } else if (degreeCamera === 270 || degreeCamera === -90) {
                intersectionPoint.x = Math.max(Math.min(intersectionPoint.x - directionY(event) * 0.1, maxX), minX);
                intersectionPoint.y = Math.max(Math.min(intersectionPoint.y + directionX(event) * 0.1, maxY), minY);
            } else {
                intersectionPoint.x = Math.max(Math.min(intersectionPoint.x + directionY(event) * 0.1, maxX), minX);
                intersectionPoint.y = Math.max(Math.min(intersectionPoint.y - directionX(event) * 0.1, maxY), minY);
            }

            var newX = intersectionPoint.x;//> maxX ? maxX : intersectionPoint.x;
            var newY = intersectionPoint.y; //> maxY ? maxY : intersectionPoint.y;

            var rotateWidth = selectedObjectRotateX ? drift : 0;
            var rotateHeight = selectedObjectRotateY ? drift : 0;

            var coordinate = Object.create(selectedObjectRotatePoint);
            coordinate.addPoint0deg(Math.round(newX) + rotateHeight, Math.round(newY) + rotateWidth);
            coordinate.addPoint90deg(Math.round(newX) + rotateWidth, Math.round(newY) + rotateHeight);
            updatePositionSelectedObject(coordinate.getPoint());

            updateBoxHelper();
            setColorGridMesh();
        }
    }

    function onMouseDown()
    {
        if(selectedObject)
        {
            isDragging = true;
            updateBoxHelper();

            selectedObjectRotateX = getWidthSelectedObject() % 2 === 0;
            selectedObjectRotateY = getLenghtSelectedObject() % 2 === 0;

            isZoom = false;
        }
        else
        {
            isRotateCamera = true;
            previousMouseX = event.clientX;
        }
    }

    function onMouseUp()
    {
        isDragging = false;

        if (selectedObject)
        {
            if (!blockObjectSelection)
            {
                var newDot = new dot.addDot(selectedObject.position.x, selectedObject.position.y);
                for(var z = axisZ; z > 0.5; z -= 0.01)
                {
                    updatePositionSelectedObject(newDot, z);
                }
            }

            selectedObjectRotateX = false;
            selectedObjectRotateY = false;
            selectedObjectRotatePoint.clear();

            gridMesh.children.forEach((cell) => {
                cell.material.color.set(gridColor);
            });
            /*selectedObject = null;
            isModel = false;

            if (boxHelper)
            {
                scene.remove(boxHelper);
                boxHelper = null;
            }*/


        }
        else if (isRotateCamera )
        {
            isRotateCamera = false;
            whereGoCamera(event);
            updateCamera();
        }

        isZoom = true;
    }

    // Обработчик события для нажатия правой кнопки мыши
    function onMouseRightClick(event) {
        if (selectedObject) {
            var cost = parseFloat(selectedObject.name);
            if (selectedObject.type === 'Group')
            {
                selectedObject = selectedObject.parent;
                cost = selectedObject.userData.name;
            }

            const costElement = document.getElementById('cost');
            costElement.textContent = parseFloat(costElement.textContent) - cost;

            scene.remove(selectedObject);
            gridMesh.children.forEach((cell) => {
                cell.material.color.set(gridColor);
            });

            selectedObjectRotateX = false;
            selectedObjectRotateY = false;
            selectedObjectRotatePoint.clear();

            for (let i = 0; i < interactiveObjects.length; i++)
            {
                if (interactiveObjects[i] === selectedObject)
                {
                    interactiveObjects.splice(i, 1);
                    break;
                }
            }

            selectedObject = null;
            blockObjectSelection = null;
            scene.remove(boxHelper);
            //setColorGridMesh();

            //onMouseUp();
            event.preventDefault(); // Отключаем стандартное контекстное меню
        }
    }

    // Добавляем обработчик события на клик правой кнопкой мыши
    sceneContainer.addEventListener('contextmenu', onMouseRightClick, false);
    sceneContainer.addEventListener('mousemove', dragAndDrop, false);
    sceneContainer.addEventListener('mousedown', onMouseDown, false);
    sceneContainer.addEventListener('mouseup', onMouseUp, false);
    sceneContainer.addEventListener('mousemove', addOutlineOnHover, false);
    sceneContainer.addEventListener('wheel', zoom, false);

    //------------------------------------

    function animate()
    {
        requestAnimationFrame( animate );
        renderer.render( scene, camera );
    }
    animate();

</script>