<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\Image;
class ApiController extends Controller
{
    public function actionImages()
    {
        // GET /api/images - вывод информации о всех загруженных файлах в формате JSON.

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $images = Image::find()->all();

        $data = [];
        foreach ($images as $image) {
            $data[] = [
                'id' => $image->id,
                'filename' => $image->filename,
                'uploaded_at' => $image->uploaded_at,
            ];
        }

        return $data;
    }

    public function actionImage($id)
    {
        // GET /api/image/<id> - получение информации о загруженном файле по его идентификатору в формате JSON.

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $image = Image::findOne($id);

        if ($image !== null) {
            return [
                'id' => $image->id,
                'filename' => $image->filename,
                'uploaded_at' => $image->uploaded_at,
            ];
        } else {
            throw new \yii\web\NotFoundHttpException('Image not found.');
        }
    }
}
