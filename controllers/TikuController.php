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
use app\models\Megagame;
use app\models\Megagroup;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

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
            ]
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

    //页面
    public function actionIndex()
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

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }
    /**
     * Displays homepage.
     *
     * @return string
     */

    //获取所有的学科
    public function actionSubject()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        //通过id得到题型
        $models=Category::find()->where([])->all();
        $html='';
        foreach ($models as $K=>$v) {
            $html= $html. '<option value="'.$v->id.'">'.$v->name.'</option>';
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

    //右侧的页面
    public function actionList()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/manage/login');
            Yii::$app->end();
        }
        $this->layout='@app/views/layouts/layoutpage.php';
        $cid= Yii::$app->request->get('cid');
        $kid= Yii::$app->request->get('kid');
        $ckid= Yii::$app->request->get('ckid');
        $query=Tiku::find()->where([]);
        if ($cid!='0') {
            $query=$query->andFilterWhere(['categoryid'=>(int)$cid]);
        }
        if ($kid!='0') {
            $query=$query->andFilterWhere(['knowsetid'=>(int)$kid]);
        }
        if ($ckid!='0') {
            $query=$query->andFilterWhere(['like','knownids',$ckid.',']);
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['create_at' => 'DESC']],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('list', [
            'provider' => $provider,
        ]);
    }
    
    //上传图片
    public function actionUpload(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $file=UploadedFile::getInstanceByName('Tiku[imgpath]');
        
        //拼装上传文件的路径
        $rootPath = "uploads/tiku";
        $name =uniqid() . '.' . $file->extension;

        if (!file_exists($rootPath)) {
           mkdir($rootPath,true);
       }
       //调用模型类中的方法 保存图片到该路径
       $file->saveAs($rootPath . "/". $name);
       return "/". $rootPath . "/". $name;
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
    
    //获取所有的知识点集合
    public function actionGetks()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $cid= Yii::$app->request->get('cid');

        $klist=Knownset::find()->where(['categoryid'=>$cid])->all();
        $html='';
        foreach ($klist as $k=>$v) {
            $html=$html.'<option value="'.$v->id.'">'.$v->name.'</option>';
        }
        return ['status'=>'success', 'data'=>$html];
    }

    //获取知识点
    public function actionKnownledge()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;

        $kid= Yii::$app->request->get('kid');

        $list=Knowledge::find()->where(['knownsetid'=>$kid])->all();
        
        return ['status'=>'success', 'data'=>$list];
    }
  
    
    //单选题
    public function actionDanxuan()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $newmodel=new Tiku();
        return $this->render('danxuan', [
           'model' => $newmodel           
        ]);
    }
    
    //判断题
    public function actionPanduan()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $newmodel=new Tiku();
        return $this->render('panduan', [
           'model' => $newmodel
        ]);
    }
    public $enableCsrfValidation=false;
    
    //编辑
    public function actionEdit()
    {
        $this->layout='@app/views/layouts/layoutpage.php';
        $id= Yii::$app->request->get('id');
        $model = Tiku::findOne($id);
        return $this->render('edit', [
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

   
    //保存题型的数据
    public function actionSave()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
       // 关闭 csrf 验证
       $this->enableCsrfValidation = false;

        $id= Yii::$app->request->post('id');
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
