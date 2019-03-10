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
use app\models\Knownset;
use app\models\Knowledge;
use yii\data\ActiveDataProvider;

class TikumanageController extends Controller
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

    //页面
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/manage/login');
            Yii::$app->end();
        }
        $this->layout='@app/views/layouts/newlayout.php';
        $cid= Yii::$app->request->get('cid');
        $cid=(int)$cid;
        $query= Tiku::find()->where([]);
        if ($cid!=0) {
            $query=$query->andFilterWhere(['categoryid'=>$cid]);
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

    //获取所有的有分组的大赛
    public function actionCategory()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;
        $cid= Yii::$app->request->get('cid');
        $cid=(int)$cid;

        //通过id得到题型
        $models=Category::find()->where([])->all();
        $html='<option value="0">请选择学科</option>';
        foreach ($models as $K=>$v) {
            if ($v->id==$cid) {
                $html= $html. '<option value="'.$v->id.'" selected="selected">'.$v->name.'</option>';
            } else {
                $html= $html. '<option value="'.$v->id.'">'.$v->name.'</option>';
            }
        }
        return ['status'=>'success', 'data'=>$html];
    }

    //获取学科下所有的知识点集合和知识点
    public function actionKnownset()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;
        $cid= Yii::$app->request->get('cid');
        $cid=(int)$cid;
        $ztree=[];
        //通过学科id得到知识点集合
        $parentlist=Knownset::find()->where(['categoryid'=>$cid])->all();
        foreach ($parentlist as $key =>$val) {
            $ztree[$key]['id']=$val->id;
            $ztree[$key]['pId']=0;
            $ztree[$key]['name']=$val->name;
            $ztree[$key]['@checked']=$key==0?true:false;
            $ztree[$key]['isParent']=true;
            $ztree[$key]['open']=$key==0?true:false;
         
            //通过知识点集合id得到知识点
            $childlist=Knowledge::find()->where(['knownsetid'=>$val->id])->all();
            foreach ($childlist as $k =>$v) {
                $ztree[$key]['children'][$k]['id']=$v->id;
                $ztree[$key]['children'][$k]['pId']=$val->id;
                $ztree[$key]['children'][$k]['name']=$v->name;
                $ztree[$key]['children'][$k]['@checked']=$k==0?true:false;
                $ztree[$key]['children'][$k]['isParent']=false;
                $ztree[$key]['children'][$k]['open']=false;
            }
        }
        return $ztree;
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
    

    //编辑单选题目
    public function actionDanxuan()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');
        $model = Tiku::findOne($id);
        return $this->render('danxuan', [
            'model' => $model
         ]);
    }

    //编辑判断题目
    public function actionPanduan()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');
        $model = Tiku::findOne($id);
        return $this->render('panduan', [
            'model' => $model
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

        //通过id得到题型
        $model=Tiku::find()->where(['id'=>$id])->one();
        if (!empty($model)) {
            return ['status'=>'success', 'data'=>$model];
        } else {
            $nmodel=new Tiku();
            return ['status'=>'success', 'data'=>$nmodel];
        }
    }

    //获取所有的知识点集合
    public function actionGetks()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;
    
        $id= Yii::$app->request->get('cid');
        $cid=Tiku::find()->where(['id'=>$id])->one()->categoryid;
        $klist=Knownset::find()->where([])->all();
        $html='';
        foreach ($klist as $k=>$v) {
            if ($v->id==$cid) {
                $html=$html.'<option value="'.$v->id.'" selected="selected">'.$v->name.'</option>';
            } else {
                $html=$html.'<option value="'.$v->id.'">'.$v->name.'</option>';
            }
        }
        return ['status'=>'success', 'data'=>$html];
    }
    
    //获取已选中知识点
    public function actionKnownledge()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;
 
        $id= Yii::$app->request->get('id');
        $tikuobj=Tiku::find()->where(['id'=>$id])->one();
        $knownids=explode(",",$tikuobj->knownids);
        //return $knownids;
        //循环
        $list=Knowledge::find()->where(['knownsetid'=>$tikuobj->knowsetid])->all();
        
        $html="";
        $vhtml="";
        foreach($list as $k=>$v){
         
            if(in_array($v->id,$knownids)){
               $html=$html ."<div class='kchilditemv'><input name='kchilditem' type='checkbox' class='kchilditem' data-id='".$v->id."' data-name='".$v->name."' onchange='chkknownset()' checked/>".$v->name."</div>";
               $vhtml=$vhtml."<div class='chkknownv'>".$v->name."</div>";
            }else{
               $html=$html ."<div class='kchilditemv'><input name='kchilditem' type='checkbox' class='kchilditem' data-id='".$v->id."' data-name='".$v->name."' onchange='chkknownset()' />".$v->name."</div>";
            } 
       }
        return ['status'=>'success', 'data'=>$html,'vdata'=>$vhtml];
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
