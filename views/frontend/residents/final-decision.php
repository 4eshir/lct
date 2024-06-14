<?php

/** @var $model QuestionDecisionForm */
/** @var $territoryId int */

use app\models\forms\QuestionDecisionForm;
use app\models\forms\QuestionForm;
use app\models\work\AgesIntervalWork;
use yii\helpers\Html;
use yii\jui\SliderInput;
use yii\widgets\ActiveForm;
?>

<style>
    .scene {
        height: 600px;
    }
    .scene canvas {
        border-radius: 15px;
    }
</style>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'decision')->dropDownList([1 => 'Вариант 1', 2 => 'Вариант 2', 3 => 'Вариант 3']) ?>

<div class="form-group">
    <div>
        <?= Html::submitButton('Подтвердить выбор', ['class' => 'btn btn-success', 'name' => 'decision-button']) ?>
    </div>
</div>

<div class="territories">
    <div class="base-weights">
        <h2>Вариант 1</h2>
        <div id="v1" style="display: none;">
            <?= var_dump($model->territoires[0]->getDataForScene($territoryId)) ?>
        </div>
        <div id="scene-container-1" class="scene"></div>
    </div>
    <div class="change-weights">
        <h2>Вариант 2</h2>
        <div id="v2" style="display: none;">
            <?= var_dump($model->territoires[1]->getDataForScene($territoryId)) ?>
        </div>
        <div id="scene-container-2" class="scene"></div>
    </div>
    <div class="votes">
        <h2>Вариант 3</h2>
        <div id="v3" style="display: none;">
            <?= var_dump($model->territoires[2]->getDataForScene($territoryId)) ?>
        </div>
        <div id="scene-container-3" class="scene"></div>
    </div>
</div>


<?php ActiveForm::end() ?>

<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.130.1/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dat-gui/0.7.7/dat.gui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dat.gui"></script>
<script>
    const scenes = [];
    const cameras = [];
    const sceneContainers = [];
    const renderers = [];
    const drift = 0.5;
    var isRotateCameras = [false, false, false];
    var degreeCameras = [0, 0, 0];
    var previousMouseX = [0, 0, 0];
    var id;
    var gridSizeX, gridSizeY, gridSizeZ;

    for (let i = 0; i < 3; i++) {
        const scene = new THREE.Scene();
        scene.background = new THREE.Color('#F0F8FF');
        scenes.push(scene);

        const sceneContainer = document.getElementById(`scene-container-${i+1}`);
        sceneContainers.push(sceneContainer);

        const camera = new THREE.PerspectiveCamera(75, sceneContainer.clientWidth / sceneContainer.clientHeight, 1, 1000);
        camera.position.z = 10;
        camera.position.y = -5;
        camera.rotation.x = 0.5;
        cameras.push(camera);

        const renderer = new THREE.WebGLRenderer();
        renderer.setSize(sceneContainer.clientWidth, sceneContainer.clientHeight);
        sceneContainer.appendChild(renderer.domElement);

        sceneContainer.addEventListener('mousedown', onMouseDown, false);
        sceneContainer.addEventListener('mouseup', onMouseUp, false);
        renderers.push(renderer);

        var date = document.getElementById(`v${i+1}`).innerText;
        init(date, scene, camera);
    }

    function init(date, scene, camera) {
        var dateObj = JSON.parse(date.substring(date.indexOf('{'), date.lastIndexOf('}}}') + 3));
        var gridMesh = new THREE.Group();

        gridSizeX = dateObj.result.matrixCount.width + 1;
        gridSizeY = dateObj.result.matrixCount.height + 1;
        gridSizeZ = dateObj.result.matrixCount.maxHeight + 10;

        var gridColor = new THREE.Color('#808080');

        var edgesMaterial = new THREE.LineBasicMaterial({ color: 0x000000 });
        var driftCellX = gridSizeX % 2 == 0 ? 0 : drift;
        var driftCellY = gridSizeY % 2 == 0 ? 0 : drift;

        for (var i = 0; i < gridSizeX * gridSizeY; i++) {
            var cellGeometry = new THREE.BoxBufferGeometry(1, 1, 0.01);
            var cellMaterial = new THREE.MeshBasicMaterial({ color: gridColor, transparent: true, opacity: 0.5, side: THREE.DoubleSide });
            var cell = new THREE.Mesh(cellGeometry, cellMaterial);
            var edges = new THREE.LineSegments(new THREE.EdgesGeometry(cellGeometry), edgesMaterial);
            cell.position.set(i % gridSizeX - gridSizeX / 2 + driftCellX, Math.floor(i / gridSizeX) - gridSizeY / 2 + driftCellY, 0);
            gridMesh.add(cell);
            cell.add(edges);
        }

        scene.add(gridMesh);
        camera.position.set(0, -(gridSizeY / 2), gridSizeZ);

        for (var i = 0; i < dateObj.result.objects.length; i++) {
            const geometry = new THREE.BoxGeometry(dateObj.result.objects[i].length, dateObj.result.objects[i].width, dateObj.result.objects[i].height);
            const randomColor = Math.floor(Math.random() * 16777215).toString(16);
            const material = new THREE.MeshBasicMaterial({ color: parseInt(randomColor, 16) });
            const oneObject = new THREE.Mesh(geometry, material);

            var rotation = dateObj.result.objects[i].rotate === 0 ? 0 : Math.PI / 2;

            var rotateX = (dateObj.result.objects[i].length % 2 === 0) ? drift : 0;
            var rotateY = (dateObj.result.objects[i].width % 2 === 0) ? drift : 0;

            if (rotation !== 0) {
                var temp = rotateX;
                rotateX = rotateY;
                rotateY = temp;
            }

            oneObject.position.set(dateObj.result.objects[i].dotCenter.x + rotateX, dateObj.result.objects[i].dotCenter.y + rotateY, 0.5);
            oneObject.rotation.z = rotation;
            scene.add(oneObject);
        }
    }

    function directionX(event)
    {
        var currentMouseX = event.clientX;
        var direction = 1;

        if (currentMouseX < previousMouseX[id]) {
            direction *=  -1;
        }

        previousMouseX[id] = currentMouseX;
        return direction;
    }

    function whereGoCamera(event)
    {
        degreeCameras[id] += 90 * directionX(event);
    }

    function updateCamera()
    {
        if (Math.abs(degreeCameras[id]) === 360 || degreeCameras[id] === 0)
        {
            degreeCameras[id] = 0;
            cameras[id].position.set(0, -(gridSizeY / 2), gridSizeZ);
            cameras[id].rotation.set(0.5, 0, 0);
        }
        else if (degreeCameras[id] === 90 || degreeCameras[id] === -270)
        {
            cameras[id].position.set(-(gridSizeX / 2), 0, gridSizeZ);
            cameras[id].rotation.set(0, -0.5, -Math.PI/2);
        }
        else if (Math.abs(degreeCameras[id]) === 180)
        {
            cameras[id].position.set(0, gridSizeY / 2, gridSizeZ);
            cameras[id].rotation.set(-0.5, 0, Math.PI);
        }
        else if (degreeCameras[id] === -90 || degreeCameras[id] === 270)
        {
            cameras[id].position.set(gridSizeX / 2, 0, gridSizeZ);
            cameras[id].rotation.set(0, 0.5, Math.PI/2);
        }

        cameras[id].updateMatrixWorld();
    }

    function getIdScene(event)
    {
        var elem = event.target.parentNode.id.split('-');
        id = elem[elem.length - 1] - 1;
    }

    function onMouseDown(event) {
        getIdScene(event);
        isRotateCameras[id] = true;
        previousMouseX[id] = event.clientX;
    }

    function onMouseUp(event) {
        if (isRotateCameras[id]) {
            isRotateCameras[id] = false;
            whereGoCamera(event);
            updateCamera();
        }
    }

    function animate()
    {
        requestAnimationFrame( animate );
        for (let i = 0; i < 3; i++) {
            renderers[i].render(scenes[i], cameras[i]);
        }
    }
    animate();
</script>