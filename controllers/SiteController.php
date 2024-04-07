<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\ImageForm;
use yii\web\UploadedFile;
use app\models\Image;
use ZipArchive;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
//    public function actionContact()
//    {
//        $model = new ContactForm();
//        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
//            Yii::$app->session->setFlash('contactFormSubmitted');
//
//            return $this->refresh();
//        }
//        return $this->render('contact', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Displays about page.
     *
     * @return string
     */
//    public function actionAbout()
//    {
//        return $this->render('about');
//    }

    public function actionUpload()
    {
        $model = new ImageForm();

        if (Yii::$app->request->isPost) {
            $model->images = UploadedFile::getInstances($model, 'images');
            if ($model->upload()) {
                Yii::$app->session->setFlash('success', 'Images have been uploaded successfully.');
                return $this->redirect(['view']);
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    public function actionView()
    {
        $images = Image::find()->all();
        return $this->render('view', ['images' => $images]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDownload($id = null)
    {
        if ($id !== null) {
            $image = Image::findOne($id);
            if ($image === null) {
                throw new NotFoundHttpException('Image not found.');
            }
            $images = [$image];
        } else {
            $images = Image::find()->all();
        }

        $zip = new ZipArchive();
        $zipFileName = 'images.zip';
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($images as $image) {
                $imagePath = Yii::getAlias('@webroot/uploads/') . $image->filename;
                $zip->addFile($imagePath, $image->filename);
            }
            $zip->close();
            Yii::$app->response->sendFile($zipFileName);
            unlink($zipFileName); // Remove the generated zip file after download
        } else {
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->content = 'Error: Failed to create zip archive.';
            Yii::$app->response->send();
        }
    }
}
