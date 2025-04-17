<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use app\models\UploadForm;
use yii\web\UploadedFile;

class BarcodeController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new \app\models\UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->barcode = Yii::$app->request->post('UploadForm')['barcode'];

            if ($model->uploadAndProcess()) {
                Yii::$app->session->setFlash('success', 'Uploaded and processed successfully!');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to upload.');
            }
        }

        return $this->render('upload', ['model' => $model]);
    }
    
}
