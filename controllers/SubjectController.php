<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Subject;
use yii\data\ActiveDataProvider;

class SubjectController extends Controller
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
            'query' => Subject::find()->where([]),
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }
       
    //编辑学科
    public function actionEdit()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');   
        $model = Subject::findOne($id);        
        if(!empty($model)){
            return $this->render('edit', [
                'model' => $model,
            ]);
        }else{
            $newmodel=new Subject();
            return $this->render('edit', [
                'model' => $newmodel,
            ]);
        }
        
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

        //通过id得到学科
        $model=Subject::find()->where(['id'=>$id])->one();
        if(!empty($model)){
            return ['status'=>'success', 'data'=>$model];
        }else{
            $nmodel=new Subject();
            return ['status'=>'success', 'data'=>$nmodel];
        }
    }

    
    public function actionTest()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/manage/login');
            Yii::$app->end();
        }
        $this->layout='@app/views/layouts/newlayout.php';
        $name= Yii::$app->request->get('name');
        $provider = new ActiveDataProvider([
            'query' => Subject::find()->where([])->andFilterWhere(['like', 'name', $name]),
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('test', [
            'provider' => $provider,
        ]);
    }

    //根据ID获取到基本信息
    public function actionPagedata()
    {
        // 返回数据格式为 json
        //Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        //$this->enableCsrfValidation = false;
    
        $page= Yii::$app->request->post('page');
        //$rows= Yii::$app->request->post('rows');
        //$name= Yii::$app->request->post('name');
        return ['status'=>'success', 'data'=>$page];

        //通过id得到学科
        $datalist=Subject::find();

        //搜索查询
        if($name!=''){
            $datalist= $datalist->where(['like','name',$name]);
        }
            
        //记录总数
        $rowscount = $datalist->count();
            
        //分页
        $datalist = $datalist->offset(($page - 1) * $rows)->limit($rows)->all();
            
        //分页数据
        $list=[];
        foreach($datalist as $k=>$v){
            $list[$k]['ID']=$v["id"];
            $list[$k]['Name']=$v["name"];
            $list[$k]['CreateTime']=date('Y-m-d H:i:s',$v["update_at"]);
        }

        //分页数据
        $result["total"]=$rowscount;
        $result["rows"]= $list;

        return ['status'=>'success', 'data'=>$result];
    }

    //保存学科的数据
    public function actionSave()
    {
         // 返回数据格式为 json
         Yii::$app->response->format = Response::FORMAT_JSON;
         // 关闭 csrf 验证
         $this->enableCsrfValidation = false;

        $id= Yii::$app->request->get('id');
        $id=(int)$id;
        $name= Yii::$app->request->get('name');

        //通过id得到学科
        $model=Subject::find()->where(['id'=>$id])->one();
        if(!empty($model)){
            $model->name=$name;
            $model->update_at=time();
            $model->save();

            if (!$model->save()) {
                return ['status'=>'fail', 'message'=>'保存失败'];
            }else{
                return ['status'=>'success', 'message'=>'保存成功'];
            }
        }else{
            $nmodel=new Subject();
            $nmodel->name=$name;
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
            $delm= Subject::findOne((int)$v);
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
