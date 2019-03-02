<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Knowledge;
use app\models\Knownset;
use app\models\Category;
use yii\data\ActiveDataProvider;

class KnowledgeController extends Controller
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
        $categoryid= Yii::$app->request->get('categoryid');
        $knownsetid= Yii::$app->request->get('knownsetid');
        $query=Knowledge::find()->where([]);
        if ($categoryid!='0') {
            $query=$query->andWhere(['categoryid'=>(int)$categoryid]);
        } elseif ($knownsetid!='0') {
            $query=$query->andWhere(['knownsetid'=>(int)$knownsetid]);
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
      
    //编辑知识点
    public function actionEdit()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');
        $model = Knowledge::findOne($id);
        if (!empty($model)) {
            return $this->render('edit', [
                'model' => $model,
            ]);
        } else {
            $newmodel=new Knowledge();
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
        //学科集合
        $clist=Category::find()->where([])->all();
        $chtml="<option value='0'>请选择</option>";

        //知识点集合
        $klist=Knownset::find()->where([])->all();
        $khtml="<option value='0'>请选择</option>";
        //通过id得到知识点
        $model=Knowledge::find()->where(['id'=>$id])->one();
        if (!empty($model)) {

            //学科集合
            foreach ($clist as $k=>$v) {
                if ($v->id=$model->categoryid) {
                    $chtml=$chtml ."<option value='".$v->id."' selected='selected'>".$v->name."</option>";
                } else {
                    $chtml=$chtml ."<option value='".$v->id."' >".$v->name."</option>";
                }
            }

            //通过id得到知识点
            foreach ($klist as $k=>$v) {
                if ($v->id=$model->knownsetid) {
                    $khtml=$khtml ."<option value='".$v->id."' selected='selected'>".$v->name."</option>";
                } else {
                    $khtml=$khtml ."<option value='".$v->id."' >".$v->name."</option>";
                }
            }
            return ['status'=>'success', 'data'=>$model,'chtml'=>$chtml,'khtml'=>$khtml];
        } else {
            $nmodel=new Knowledge();

            //学科集合
            foreach ($clist as $k=>$v) {
                if ($v->id=$model->categoryid) {
                    $chtml=$chtml ."<option value='".$v->id."' selected='selected'>".$v->name."</option>";
                } else {
                    $chtml=$chtml ."<option value='".$v->id."' >".$v->name."</option>";
                }
            }
            return ['status'=>'success', 'data'=>$nmodel,'chtml'=>$chtml,'khtml'=>$khtml];
        }
    }

    //根据ID获取到全部的学科
    public function actionCategory()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $id= Yii::$app->request->get('id');
        $id=(int)$id;

        $html="<option value='0'>请选择</option>";
        //全部的知识点
        $clist=Category::find()->where([])->all();
        foreach ($clist as $k=>$v) {
            if ($v->id==$id) {
                $html=$html ."<option value='".$v->id."' selected='selected'>".$v->name."</option>";
            } else {
                $html=$html ."<option value='".$v->id."' >".$v->name."</option>";
            }
        }
        return ['status'=>'success', 'data'=>$html];
    }

    //根据ID获取到全部的知识点
    public function actionKnownset()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $id= Yii::$app->request->get('id');
        $id=(int)$id;
        $cid= Yii::$app->request->get('cid');
        $cid=(int)$cid;

        $html="<option value='0'>请选择</option>";


        //通过id得到知识点
        $clist=Knownset::find()->where(['categoryid'=>$cid])->all();
        foreach ($clist as $k=>$v) {
            if ($v->id==$id) {
                $html=$html ."<option value='".$v->id."' selected='selected'>".$v->name."</option>";
            } else {
                $html=$html ."<option value='".$v->id."' >".$v->name."</option>";
            }
        }
        return ['status'=>'success', 'data'=>$html];
    }

    //保存知识点的数据
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
        $knownsetid= Yii::$app->request->get('knownsetid');

        //通过id得到知识点
        $model=Knowledge::find()->where(['id'=>$id])->one();
        if (!empty($model)) {
            $model->name=$name;
            $model->categoryid=$categoryid;
            $model->knownsetid=$knownsetid;
            $model->update_at=time();
            $model->save();

            if (!$model->save()) {
                return ['status'=>'fail', 'message'=>'保存失败'];
            } else {
                return ['status'=>'success', 'message'=>'保存成功'];
            }
        } else {
            $nmodel=new Knowledge();
            $nmodel->name=$name;
            $nmodel->categoryid=$categoryid;
            $nmodel->knownsetid=$knownsetid;
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
                $delm= Knowledge::findOne((int)$v);
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
