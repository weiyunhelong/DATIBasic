<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Category;
use app\models\Knownset;
use yii\data\ActiveDataProvider;

class KnownsetController extends Controller
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
        if (Yii::$app->user->isGuest) {
            $this->redirect('/manage/login');
            Yii::$app->end();
        }
        $this->layout='@app/views/layouts/newlayout.php';
        $provider = new ActiveDataProvider([
            'query' => Category::find()->where([]),
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }
       
    public function actionList()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/manage/login');
            Yii::$app->end();
        }
        $this->layout='@app/views/layouts/newlayout.php';
        $categoryid= Yii::$app->request->get('cid');
        $provider = new ActiveDataProvider([
            'query' => Knownset::find()->where(['categoryid'=>$categoryid]),
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('list', [
            'provider' => $provider,
        ]);
    }   

    //编辑知识点集合
    public function actionEdit()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');   
        $model = Knownset::findOne($id);        
        if(!empty($model)){
            return $this->render('edit', [
                'model' => $model,
            ]);
        }else{
            $newmodel=new Knownset();
            return $this->render('edit', [
                'model' => $newmodel,
            ]);
        }
        
    }
    
    //编辑知识点集合
    public function actionAdd()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $newmodel=new Knownset();
        return $this->render('add', [
            'model' => $newmodel,
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

        //通过id得到知识点集合
        $model=Knownset::find()->where(['id'=>$id])->one();
        if(!empty($model)){
            return ['status'=>'success', 'data'=>$model];
        }else{
            $nmodel=new Knownset();
            return ['status'=>'success', 'data'=>$nmodel];
        }
    }


    //保存知识点集合的数据
    public function actionSave()
    {
         // 返回数据格式为 json
         Yii::$app->response->format = Response::FORMAT_JSON;
         // 关闭 csrf 验证
         $this->enableCsrfValidation = false;

        $id= Yii::$app->request->get('id');
        $id=(int)$id;
        $name= Yii::$app->request->get('name');
        $categoryid= Yii::$app->request->get('categoryid');
        $isdifficult= Yii::$app->request->get('isdifficult');

        //通过id得到知识点集合
        $model=Knownset::find()->where(['id'=>$id])->one();
        if(!empty($model)){
            $model->name=$name;
            $model->categoryid=$categoryid;
            $model->isdifficult=$isdifficult;
            $model->update_at=time();
            $model->save();

            if (!$model->save()) {
                return ['status'=>'fail', 'message'=>'保存失败'];
            }else{
                return ['status'=>'success', 'message'=>'保存成功'];
            }
        }else{
            $nmodel=new Knownset();
            $nmodel->name=$name;
            $nmodel->categoryid=$categoryid;
            $nmodel->isdifficult=$isdifficult;
            $nmodel->create_at=time();
            $nmodel->update_at=time();
            $nmodel->save();

            if (!$nmodel->save()) {
                return ['status'=>'fail', 'message'=>'保存失败'];
            }else{
                return ['status'=>'success', 'message'=>'保存成功'];
            }
        }
    }

    //删除数据
    public function actionDelete()
    {
         // 返回数据格式为 json
         Yii::$app->response->format = Response::FORMAT_JSON;
         // 关闭 csrf 验证
         $this->enableCsrfValidation = false;

        $ids= Yii::$app->request->post('ids');
        $delids=explode(',',$ids);

        foreach($delids as $k => $v){
           if($v!=''){
            $delm= Knownset::findOne((int)$v);
            $delm->delete();
           }
        }
        return ['status'=>'success', 'message'=>'保存成功'];
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
