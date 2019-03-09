<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Megagame;
use app\models\Megagroup;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class MegagameController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
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
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/manage/login');
            Yii::$app->end();
        }
        $this->layout='@app/views/layouts/newlayout.php';
       
        $provider = new ActiveDataProvider([
            'query' => Megagame::find()->where(['<>','status',-1]),
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }
       
    //编辑
    public function actionEdit()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');
        $model = Megagame::findOne($id);
        if (!empty($model)) {
            return $this->render('edit', [
                'model' => $model,
            ]);
        } else {
            $newmodel=new Megagame();
            return $this->render('edit', [
                'model' => $newmodel,
            ]);
        }
    }
    
    //新增
    public function actionAdd()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $newmodel=new Megagame();
        return $this->render('add', [
           'model' => $newmodel
        ]);
    }

    //根据ID获取到基本信息
    public function actionInfo()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $id= Yii::$app->request->get('id');
        $id=(int)$id;

        //通过id得到
        $model=Megagame::find()->where(['id'=>$id])->one();


        if (!empty($model)) {

            //获取分组的数据
            $groups=Megagroup::find()->where(['mid'=>$model->id])->all();

            return ['status'=>'success', 'data'=>$model,'groups'=>$groups];
        } else {
            $nmodel=new Megagame();
            return ['status'=>'success', 'data'=>$nmodel,'groups'=>[]];
        }
    }

    //保存题型的数据
    public function actionSave()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $id= Yii::$app->request->post('id');
        $id=(int)$id;
        $name= Yii::$app->request->post('name');
        $isyear= Yii::$app->request->post('isyear');
        $isanswer= Yii::$app->request->post('isanswer');
        $showname= Yii::$app->request->post('showname');
        $logo= Yii::$app->request->post('logo');
        $rule= Yii::$app->request->post('rule');
        $level= Yii::$app->request->post('level');
       
        //return ['status'=>'fail', 'message'=>$logo];
        //通过id得到题型
        $model=Megagame::find()->where(['id'=>$id])->one();
        if (!empty($model)) {
            $model->name=$name;
            $model->isyear=$isyear;
            $model->isanswer=$isanswer;
            $model->showname=$showname;
            $model->logo=$logo;
            $model->rule=$rule;
            $model->level=$level;
            $model->update_at=time();
            $model->save();

            if (!$model->save()) {
                return ['status'=>'fail', 'message'=>'保存失败'];
            } else {
                return ['status'=>'success', 'message'=>'保存成功'];
            }
        } else {
            $nmodel=new Megagame();
            $nmodel->name=$name;
            $nmodel->isyear=$isyear;
            $nmodel->isanswer=$isanswer;
            $nmodel->showname=$showname;
            $nmodel->logo=$logo;
            $nmodel->rule=$rule;
            $nmodel->level=$level;
            $nmodel->status=0;
            $nmodel->create_at=time();
            $nmodel->update_at=time();
            $nmodel->save();

            if (!$nmodel->save()) {
                return ['status'=>'fail', 'message'=>'保存失败'];
            } else {
                return ['status'=>'success', 'message'=>'保存成功'];
            }
        }
    }
    
    //保存分组的数据
    public function actionStatus()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $id= Yii::$app->request->post('id');
        $id=(int)$id;
        $status= Yii::$app->request->post('status');
        $status=(int)$status;
        $model=Megagame::find()->where(['id'=>$id])->one();
        //return $model;
        //更新开始
        $mlist=Megagame::find()->where([])->all();
        foreach($mlist as $k=>$v){
            if($v->status==1){
                $v->status=0;
                $v->save();
            }
        }
        $model->status=1;
        if ($model->save()) {
            return ['status' => 'success', 'message' =>'保存数据成功'];
        }
        return $model->getErrors();
    }
    //删除数据
    public function actionDelete()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $ids= Yii::$app->request->post('ids');
        $delids=explode(',', $ids);

        foreach ($delids as $k => $v) {
            if ($v!='') {
                $delm= Megagame::findOne((int)$v);
                $delm->status=-1;
                $delm->save();
            }
        }
        return ['status'=>'success', 'message'=>'保存成功'];
    }
    
    public $enableCsrfValidation=false;
    //上传图片
    public function actionUpload(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $file=UploadedFile::getInstanceByName('Megagame[logo]');
        
        //拼装上传文件的路径
        $rootPath = "uploads/logo";
        $name =uniqid() . '.' . $file->extension;

        if (!file_exists($rootPath)) {
           mkdir($rootPath,true);
       }
       //调用模型类中的方法 保存图片到该路径
       $file->saveAs($rootPath . "/". $name);
       return "/". $rootPath . "/". $name;
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout='@app/views/layouts/blank.php';
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
}
