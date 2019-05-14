<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Knownset;
use app\models\Knowledge;
use app\models\Megagame;
use app\models\Megagroup;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class MegagroupController extends Controller
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
            'WebUpload' => [
                'class' => 'moxuandi\webuploader\UploaderAction',
                //可选参数, 参考 UMeditorAction::$_config
                'config' => [
                    'thumbStatus' => true,  // 生成缩略图
                    'thumbWidth' => 150,    // 缩略图宽度
                    'thumbHeight' => 100,   // 缩略图高度
                    // 使用前请导入'database'文件夹中的数据表'upload'和模型类'Upload'
                   'pathFormat' => 'uploads/logo/{yyyy}{mm}/{yy}{mm}{dd}_{hh}{ii}{ss}_{rand:4}',
                    // 上传保存路径, 可以自定义保存路径和文件名格式
                   'saveDatabase' => false,  // 保存上传信息到数据库
                ],
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
        $mid= Yii::$app->request->get('mid');
        $mid=(int)$mid;
        $query=Megagroup::find()->where(['<>','status',-1]);
        if ($mid!=0) {
            $query=$query ->andFilterWhere(['mid'=>$mid]);
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
       
    //编辑
    public function actionEdit()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');
        $model = Megagroup::findOne($id);
        if (!empty($model)) {
            return $this->render('edit', [
                'model' => $model,
            ]);
        } else {
            $newmodel=new Megagroup();
            return $this->render('edit', [
                'model' => $newmodel,
            ]);
        }
    }
    
    //新增
    public function actionAdd()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $newmodel=new Megagroup();
        return $this->render('add', [
           'model' => $newmodel,
           'WebUpload' => [
            'class' => 'moxuandi\webuploader\UploaderAction',
            //可选参数, 参考 UMeditorAction::$_config
            'config' => [
                'thumbStatus' => true,  // 生成缩略图
                'thumbWidth' => 150,    // 缩略图宽度
                'thumbHeight' => 100,   // 缩略图高度
                 // 使用前请导入'database'文件夹中的数据表'upload'和模型类'Upload'
                'pathFormat' => 'uploads/logo/{yyyy}{mm}/{yy}{mm}{dd}_{hh}{ii}{ss}_{rand:4}',
                 // 上传保存路径, 可以自定义保存路径和文件名格式
                'saveDatabase' => false,  // 保存上传信息到数据库
            ],
        ],
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
        $model=Megagroup::find()->where(['id'=>$id])->one();
        //获取分组的数据
        $groups=Megagame::find()->where(['isyear'=>1])->where(['<>','status', -1])->all();
        $phtml='';

        if (!empty($model)) {
            foreach ($groups as $k=>$v) {
                if ($model->mid==$v->id) {
                    $phtml=$phtml . "<option value='".$v->id ."' selected='selected'>".$v->name."</option>";
                } else {
                    $phtml=$phtml . "<option value='".$v->id ."'>".$v->name."</option>";
                }
            }
            return ['status'=>'success', 'data'=>$model,'phtml'=>$phtml];
        } else {
            $nmodel=new Megagame();
            foreach ($groups as $k=>$v) {
                $phtml=$phtml . "<option value='".$v->id ."'>".$v->name."</option>";
            }
            return ['status'=>'success', 'data'=>$nmodel,'phtml'=>$phtml];
        }
    }

    //有分组的大赛
    public function actionMegagame()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;
        
        $mid= Yii::$app->request->get('mid');
        $mid=(int)$mid;
        $list=Megagame::find()->where(['isyear'=>1])->all();

        $html='<option value="0">全部</option>';
        foreach ($list as $k=>$v) {
            if ($mid==$v->id) {
                $html=$html."<option value='".$v->id."' selected='selected'>".$v->name."</option>";
            } else {
                $html=$html."<option value='".$v->id."' >".$v->name."</option>";
            }
        }
        return ['status'=>'success', 'data'=>$html];
    }

    //知识点的树形结构
    public function actionKnowledge()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;
        $ids= Yii::$app->request->get('ids');
        $chkids=[];
        if(!empty($ids)){
            $chkids=explode(',',$ids);
        }
        //return $chkids;
        $ztree=[];
        //通过学科id得到知识点集合
        $parentlist=Knownset::find()->where([])->all();
        foreach ($parentlist as $key =>$val) {
            $ztree[$key]['id']=$val->id;
            $ztree[$key]['pId']=0;
            $ztree[$key]['name']=$val->name;
            $ztree[$key]['checked']=false;
            $ztree[$key]['isParent']=false;
            $ztree[$key]['open']=true;
         
            //通过知识点集合id得到知识点
            $childlist=Knowledge::find()->where(['knownsetid'=>$val->id])->all();
            foreach ($childlist as $k =>$v) {
                $ztree[$key]['children'][$k]['id']=$v->id;
                $ztree[$key]['children'][$k]['pId']=$val->id;
                $ztree[$key]['children'][$k]['name']=$v->name;
                
                foreach($chkids as $ck =>$cv){
                   if($cv==$v->id){
                      $ztree[$key]['children'][$k]['checked']=true;
                   }
                }
                $ztree[$key]['children'][$k]['isParent']=false;
                $ztree[$key]['children'][$k]['open']=true;
            }
        }
        return $ztree;
    }

    //保存数据
    public function actionSave()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $id= Yii::$app->request->post('id');
        $name= Yii::$app->request->post('name');
        $mid= Yii::$app->request->post('mid');
        $tid= Yii::$app->request->post('tid');
        $kids= Yii::$app->request->post('kids');

        //通过id得到题型
        $model=Megagroup::find()->where(['id'=>$id])->one();
        if (!empty($model)) {
            $model->name=$name;
            $model->mid=(int)$mid;
            $model->tid=(int)$tid;
            $model->knownids=$kids;
            $model->update_at=time();

            if ($model->save()) {
                return ['status' => 'success', 'message' =>'保存数据成功'];
            }
            return $model->getErrors();
        } else {
            $nmodel=new Megagroup();
            $nmodel->name=$name;
            $nmodel->mid=(int)$mid;
            $nmodel->tid=(int)$tid;
            $nmodel->knownids=$kids;
            $nmodel->create_at=time();
            $nmodel->update_at=time();
            $nmodel->status=0;

            if ($nmodel->save()) {
                return ['status' => 'success', 'message' =>'保存数据成功'];
            }
            return $nmodel->getErrors();
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
                $delm= Megagroup::findOne((int)$v);
                $delm->status=-1;
                $delm->save();
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
