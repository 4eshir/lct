<?php


namespace app\controllers;


use app\models\Appeal;
use Yii;
use yii\httpclient\Client;
use yii\web\Controller;

class ClassificatorController extends Controller
{
    const API_PATH = 'http://127.0.0.1:8000';

    public function actionIndex()
    {
        Yii::$app->session->set('header-active', 'appeal');

        return $this->render('index');
    }

    public function actionSendAppeal()
    {
        Yii::$app->session->set('header-active', 'appeal');

        $model = new Appeal();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            $url = self::API_PATH . '/text-class'; // ваш URL
            $data = [
                'text' => $model->message,
            ];

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Чтобы cURL возвращал ответ в виде строки
            curl_setopt($ch, CURLOPT_POST, true); // Используем POST
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Отправляем данные в JSON формате
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json', // Указываем заголовок
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                // Обработка ошибки
                Yii::error("cURL error: $error_msg");
            }

            curl_close($ch);

            $responseData = json_decode($response, true);

            if ($responseData['data'][0] == Appeal::MTYPE_NORMAL) {
                $url = self::API_PATH . '/text-class-adv'; // ваш URL

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Чтобы cURL возвращал ответ в виде строки
                curl_setopt($ch, CURLOPT_POST, true); // Используем POST
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Отправляем данные в JSON формате
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json', // Указываем заголовок
                ]);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                    // Обработка ошибки
                    Yii::error("cURL error: $error_msg");
                }

                curl_close($ch);

                $responseData = json_decode($response, true);
            }

            $resultMessage = '';
            switch ($responseData['data'][0]) {
                case Appeal::MTYPE_CONNECTION:
                    $resultMessage = 'Ваше обращение успешно зарегистрировано с номером ' . rand(1000, 5000) . '. Обращение будет направлено в отдел по <b>связи и Интернету</b>';
                    break;
                case Appeal::MTYPE_ELECTRIC:
                    $resultMessage = 'Ваше обращение успешно зарегистрировано с номером ' . rand(1000, 5000) . '. Обращение будет направлено в отдел по <b>электроснабжению</b>';
                    break;
                case Appeal::MTYPE_SEWERAGE:
                    $resultMessage = 'Ваше обращение успешно зарегистрировано с номером ' . rand(1000, 5000) . '. Обращение будет направлено в отдел по <b>эксплуатации канализационных систем</b>';
                    break;
                case Appeal::MTYPE_WATER_SUPPLY:
                    $resultMessage = 'Ваше обращение успешно зарегистрировано с номером ' . rand(1000, 5000) . '. Обращение будет направлено в отдел по <b>водоснабжению</b>';
                    break;
                case Appeal::MTYPE_PERSONAL:
                    $resultMessage = 'Ваше обращение успешно зарегистрировано с номером ' . rand(1000, 5000) . '. Обращение будет рассмотрено в <b>индивидуальном порядке</b>';
                    break;
                default:
                    $resultMessage = 'К сожалению, ваше обращение было расценено нашей системой как <b>спам</b>. Повторите попытку, избегайте оскорбительных и лишенных смысловой нагрузки выражений';
            }

            return $this->render('result-message', [
                'result' => $resultMessage
            ]);
        }

        return $this->render('appeal', [
            'model' => $model
        ]);
    }
}