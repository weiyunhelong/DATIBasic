<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Tixing;
use app\models\Tiku;
use app\models\Category;
use yii\data\ActiveDataProvider;

class TikuController extends Controller
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
        $cid= Yii::$app->request->get('name');
        $query=Tiku::find()->where([]);
        if ($cid!='0') {
            $query=$query->andFilterWhere(['categoryid'=> $cid]);
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }
    
    /**
     * Displays homepage.
     *
     * @return string
     */
    
    public function actionTixing()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/manage/login');
            Yii::$app->end();
        }
        $this->layout='@app/views/layouts/newlayout.php';

        $provider = new ActiveDataProvider([
            'query' => Tixing::find()->where([]),
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('tixing', [
            'provider' => $provider,
        ]);
    }

    //编辑题型
    public function actionEdit()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');
        $model = Tiku::findOne($id);
        if (!empty($model)) {
            return $this->render('edit', [
                'model' => $model,
            ]);
        } else {
            $newmodel=new Tiku();
            return $this->render('edit', [
                'model' => $newmodel,
            ]);
        }
    }
    
    //学科列表
    public function actionCategory()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $categorys=Category::find()->where([''])->all();

        return ['status'=>'success', 'data'=>$categorys];
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

        //通过id得到题型
        $model=Tiku::find()->where(['id'=>$id])->one();
        if (!empty($model)) {
            return ['status'=>'success', 'data'=>$model];
        } else {
            $nmodel=new Tiku();
            return ['status'=>'success', 'data'=>$nmodel];
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
        $categoryid= Yii::$app->request->post('categoryid');
        $tixingid= Yii::$app->request->post('tixingid');
        $knowsetid= Yii::$app->request->post('knowsetid');
        $knownids= Yii::$app->request->post('knownids');
        $showtype= Yii::$app->request->post('showtype');
        $title= Yii::$app->request->post('title');
        $imgpath= Yii::$app->request->post('imgpath');
        $optionA= Yii::$app->request->post('optionA');
        $optionB= Yii::$app->request->post('optionB');
        $optionC= Yii::$app->request->post('optionC');
        $optionD= Yii::$app->request->post('optionD');
        $optionE= Yii::$app->request->post('optionE');
        $optionF= Yii::$app->request->post('optionF');
        $answer= Yii::$app->request->post('answer');
        $difficult= Yii::$app->request->post('difficult');
        $mark= Yii::$app->request->post('mark');

        //通过id得到题型
        $model=Tiku::find()->where(['id'=>$id])->one();
        if (!empty($model)) {
            $model->categoryid= $categoryid;
            $model->tixingid= $tixingid;
            $model->knowsetid= $knowsetid;
            $model->knownids= $knownids;
            $model->showtype=$showtype;
            $model->title= $title;
            $model->imgpath=$imgpath;
            $model->optionA= $optionA;
            $model->optionB= $optionB;
            $model->optionC= $optionC;
            $model->optionD=$optionD;
            $model->optionE=$optionE;
            $model->optionF= $optionF;
            $model->answer= $answer;
            $model->difficult= $difficult;
            $model->mark= $mark;
            $model->update_at=time();
            $model->save();

            if (!$model->save()) {
                return ['status'=>'fail', 'message'=>'保存失败'];
            } else {
                return ['status'=>'success', 'message'=>'保存成功'];
            }
        } else {
            $nmodel=new Tiku();
            $nmodel->categoryid= $categoryid;
            $nmodel->tixingid= $tixingid;
            $nmodel->knowsetid= $knowsetid;
            $nmodel->knownids= $knownids;
            $nmodel->showtype=$showtype;
            $nmodel->title= $title;
            $nmodel->imgpath=$imgpath;
            $nmodel->optionA= $optionA;
            $nmodel->optionB= $optionB;
            $nmodel->optionC= $optionC;
            $nmodel->optionD=$optionD;
            $nmodel->optionE=$optionE;
            $nmodel->optionF= $optionF;
            $nmodel->answer= $answer;
            $nmodel->difficult= $difficult;
            $nmodel->mark= $mark;
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
                $delm= Tiku::findOne((int)$v);
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
