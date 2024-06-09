<?php

namespace app\controllers\backend;

use app\models\forms\DownloadXMLForm;
use app\models\work\ObjectWork;
use app\models\search\SearchObjectWork;
use bupy7\xml\constructor\XmlConstructor;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ObjectController implements the CRUD actions for ObjectWork model.
 */
class ObjectController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ObjectWork models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SearchObjectWork();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ObjectWork model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ObjectWork model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ObjectWork();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ObjectWork model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ObjectWork model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionUploadXml()
    {
        $objects = ObjectWork::find()->all();
        $xmlArray = [];

        foreach ($objects as $object) {
            /** @var ObjectWork $object */
            $xmlArray[] = [
                'tag' => 'object',
                'attributes' => [
                    'id' => $object->id,
                    'name' => $object->name,
                    'length' => $object->length,
                    'width' => $object->width,
                    'height' => $object->height,
                    'cost' => $object->cost,
                    'created_time' => $object->created_time,
                    'install_time' => $object->install_time,
                    'worker_count' => $object->worker_count,
                    'object_type' => $object->objectType->name,
                    'creator' => $object->creator,
                    'dead_zone_size' => $object->dead_zone_size,
                    'style' => $object->style,
                    'article' => $object->article,
                    'left_age' => $object->left_age,
                    'right_age' => $object->right_age,
                ]
            ];
        }

        $xml = new XmlConstructor();
        $in = [
            [
                'tag' => 'root',
                'elements' => [
                    [
                        'tag' => 'objects_list',
                        'elements' => $xmlArray,
                    ],
                ],
            ],
        ];
        $xmlString = $xml->fromArray($in)->toOutput();

        header('Content-Type: text/xml');
        header('Content-Disposition: attachment; filename="filename.xml"');

        echo $xmlString;
        exit();
    }

    public function actionDownloadXml()
    {
        $model = new DownloadXMLForm();

        if (Yii::$app->request->isPost) {
            $model->xmlFile = UploadedFile::getInstance($model, 'xmlFile');

            if ($model->importXml()) {
                Yii::$app->session->setFlash('success', 'Данные из XML-файла успешно загружены');
                return $this->redirect(['index']);
            }
        }

        return $this->render('download-xml', ['model' => $model]);
    }

    /**
     * Finds the ObjectWork model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ObjectWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ObjectWork::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
