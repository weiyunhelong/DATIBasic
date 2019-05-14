<?php

namespace app\controllers;

use abei2017\wx\Application;
use app\models\Category;
use app\models\Chance;
use app\models\Knowledge;
use app\models\Knowset;
use app\models\Megagame;
use app\models\Megagroup;
use app\models\Record;
use app\models\Subject;
use app\models\Tiku;
use app\models\Tixing;
use app\models\UserAddress;
use app\models\WechatUser;
use Yii;
use yii\db\Expression;
use yii\httpclient\Client;
use yii\web\Response;
use yii\web\UploadedFile;

//小程序工具类

class ApiController extends \yii\web\Controller
{
    public function init()
    {
        // 返回数据格式为 json
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 关闭 csrf 验证
        $this->enableCsrfValidation = false;
    }

    // 0 小程序授权
    public function actionLogin()
    {
        $code = Yii::$app->request->get('code');
        
        if (!$code) {
            return ['status' => 'fail', 'message' => 'code 不能为空'];
        }
        
        //根据code查询，用户是否存在
        $data = $this->wechatAuth($code);

        if (isset($data['openid']) && isset($data['session_key'])) {
            $openid = $data['openid'];
            $model = WechatUser::findOne(['openid' => $openid]);
            if (empty($model)) {
                return ['status' => 'success', 'openid' => $openid,  'chancenum' =>5,'isnew'=>true];
            } else {
                return ['status' => 'success', 'openid' => $openid,  'chancenum' => $model->chancenum,'isnew'=>false];
            }
        } else {
            $errmsg = isset($data['errmsg']) ? $data['errmsg'] : '授权出错';
            return ['status' => 'fail', 'message' => $errmsg];
        }
    }
    
    //1 用户授权，保存用户信息
    public function actionSaveuser()
    {
        $openid = Yii::$app->request->post('openid');
        $topenid = Yii::$app->request->post('topenid');
        $nickname = Yii::$app->request->post('nickname');
        $gender = Yii::$app->request->post('gender');
        $avatar = Yii::$app->request->post('avatar');
        $country = Yii::$app->request->post('country');
        $province = Yii::$app->request->post('province');
        $city = Yii::$app->request->post('city');
        
        //验证openid是否存在
        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid不能为空'];
        }
        
        //根据openid查询，用户是否存在
        $model = WechatUser::findOne(['openid' => $openid]);
        
        if (empty($model)) {
            $mmodel=new WechatUser();
            $mmodel->openid=$openid;
            $mmodel->topenid=$topenid;
            $mmodel->unionid="-1";
            $mmodel->nickname=$nickname;
            $mmodel->gender=$gender;
            $mmodel->avatar=$avatar;
            $mmodel->country=$country;
            $mmodel->province=$province;
            $mmodel->city=$city;
            $mmodel->chancenum=5;
            $mmodel->create_at= time();
            $mmodel->update_at= time();
            if ($mmodel->save()) {
                return ['status' => 'success',  'message' => "微信用户创建成功"];
            } else {
                return $mmodel->getErrors();
            }
        } else {
            $model->openid=$openid;
            $model->topenid=$topenid;
            $model->nickname=$nickname;
            $model->gender=$gender;
            $model->avatar=$avatar;
            $model->country=$country;
            $model->province=$province;
            $model->city=$city;
            $model->update_at= time();

            if ($model->save()) {
                return ['status' => 'success',  'message' => "微信用户更新成功"];
            } else {
                return $model->getErrors();
            }
        }
    }
    //更新用户答题机会
    private function UpdateChance($openid, $topenid, $matchid)
    {
        $model=Chance::findOne(['openid' => $openid,'topenid' => $topenid,'matchid'=>$matchid]);
        if (empty($model)) {
            $mmodel=new Chance();
            $mmodel->openid=$openid;
            $mmodel->topenid=$topenid;
            $mmodel->matchid=$matchid;
            $mmodel->number=4;
            if ($mmodel->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            $model->number=$model->number-1;
            if ($model->save()) {
                return true;
            } else {
                return false;
            }
        }
    }
    //1-1 保存微信用户信息
    public function actionSavewxuser()
    {
        $openid = Yii::$app->request->post('openid');
        $topenid = Yii::$app->request->post('topenid');
        $tid = Yii::$app->request->post('tid');//大赛的id，没有的值为0
        $nickname = Yii::$app->request->post('nickname');
        $gender = Yii::$app->request->post('gender');
        $avatar = Yii::$app->request->post('avatar');
        $country = Yii::$app->request->post('country');
        $province = Yii::$app->request->post('province');
        $city = Yii::$app->request->post('city');
            
        //验证openid是否存在
        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid不能为空'];
        }
            
        //根据openid查询，用户是否存在
        $model = WechatUser::findOne(['openid' => $openid]);
            
        if (empty($model)) {
            $mmodel=new WechatUser();
            $mmodel->openid=$openid;
            $mmodel->topenid=$topenid;
            $mmodel->matchid=$tid;
            $mmodel->unionid="-1";
            $mmodel->nickname=$nickname;
            $mmodel->gender=$gender;
            $mmodel->avatar=$avatar;
            $mmodel->country=$country;
            $mmodel->province=$province;
            $mmodel->city=$city;
            $mmodel->chancenum=5;
            $mmodel->create_at= time();
            $mmodel->update_at= time();

            $this->UpdateChance($openid, $topenid, $tid);

            if ($mmodel->save()) {
                return ['status' => 'success',  'message' => "微信用户创建成功"];
            } else {
                return $mmodel->getErrors();
            }
        } else {
            $model->openid=$openid;
            $model->topenid=$topenid;
            $model->matchid=$tid;
            $model->nickname=$nickname;
            $model->gender=$gender;
            $model->avatar=$avatar;
            $model->country=$country;
            $model->province=$province;
            $model->city=$city;
            $model->update_at= time();

            $this->UpdateChance($openid, $topenid, $tid);

            if ($model->save()) {
                return ['status' => 'success',  'message' => "微信用户更新成功"];
            } else {
                return $model->getErrors();
            }
        }
    }

    //答题记录
    public function actionRecord()
    {
        //答题记录
        $openid = Yii::$app->request->post('openid');
        $topenid = Yii::$app->request->post('topenid');
        $tid= Yii::$app->request->post('tid');
        $ids= Yii::$app->request->post('ids');
        $rightnum= Yii::$app->request->post('rightnum');
        $wrongnum= Yii::$app->request->post('wrongnum');
        
        //新增用户答题记录
        $record=new Record();
        $record->openid=$openid;
        $record->topenid=$topenid;
        $record->tid=$tid;
        $record->ids=$ids;
        $record->rightnum=$rightnum;
        $record->wrongnum=$wrongnum;
        $record->create_at=time();

        if ($record->save()) {
            return ['status' => 'success',  'message' => "保存答题记录成功"];
        } else {
            return $model->getErrors();
        }
    }
    
    //获取用户的最好成绩
    public function actionMaxlevel()
    {
        //答题记录
        $openid = Yii::$app->request->post('openid');
      
        //新增用户答题记录
        $maxval=Record::find()->select('max(rightnum) as maxrightnum')->where(['=', 'openid', $openid])->asArray()->all();//一个二维数组c
        $level=$maxval[0]['maxrightnum'];
        if ($level==null) {
            $level=0;
        } elseif ($level<6) {
            return ['status' => 'success',  'data' => 1];
        } elseif ($level>5&&$level<12) {
            return ['status' => 'success',  'data' => 2];
        } else {
            return ['status' => 'success',  'data' => 3];
        }
    }

    //获取推星官用户的最好成绩
    public function actionGetresult()
    {
        //答题记录
        $topenid = Yii::$app->request->post('topenid');
        $tid = Yii::$app->request->post('matchid');

        
        $maxval=Record::find()->select('max(rightnum) as maxrightnum')->where(['topenid'=>$topenid,'tid'=>$tid])->asArray()->all();//一个二维数组c
        $level=$maxval[0]['maxrightnum'];
        if ($level==null) {
            return ['status' => 'fail','data' => 0];
        } else{
            return ['status' => 'success','data' => $level];
        }
    }
  
    //获取推行官答题次数
    public function actionTxchance()
    {
        //推行官的openid
        $topenid = Yii::$app->request->post('topenid');
        $matchid = Yii::$app->request->post('matchid');
        //用户答题次数
        $chanceobj=Chance::find()->where(['topenid' => $topenid,'matchid'=>$matchid])->one();
         
        if ($chanceobj==null) {
            return ['status' => 'success',  'data' => 5];
        } else {
            return ['status' => 'success',  'data' =>  $chanceobj->number];
        }
    }
    
    //获取用户的答题次数
    public function actionGetchance()
    {
        //openid
        $openid = Yii::$app->request->post('openid');
        $topenid = Yii::$app->request->post('topenid');
        $matchid = Yii::$app->request->post('matchid');
        //用户答题次数
        $chanceobj=Chance::find()->where(['openid'=> $openid,'topenid' => $topenid,'matchid'=>$matchid])->one();
       
        if ($chanceobj==null) {
            return ['status' => 'success',  'data' => 5];
        } else {
            return ['status' => 'success',  'data' =>  $chanceobj->number];
        }
    }
  
    //获取用户的答题题目
    public function actionGetquestion()
    {
        //参数部分
        $groupid = Yii::$app->request->post('groupid');//分组id
        $mid = Yii::$app->request->post('mid');//大赛id
        //根据分组id获取分组
        $managroup=Megagroup::find()->where(['tid'=>$groupid,'mid'=>$mid])->one();
        
        if ($managroup==null) {
            return ['status' => 'fail','message' => '查询不到相关数据!'];
        } else {
            //获取知识点,根据知识点获取题目
            $kids=$managroup->knownids;
            $kidarry=explode(',', $kids);
            $tikus=[];
            foreach ($kidarry as $key => $val) {
                $tiobj=Tiku::find()->where(['like','knownids',[$val,',']])->asArray()->all();
                $tikus=array_merge($tikus, $tiobj);
            }
            
            //根据题目，去除重复
            $aa = array();
            $bb = array();
            $index=0;
            foreach ($tikus as $k=>$v) {
                if (!in_array($v['id'], $aa)) {
                    $aa[$index]=$v['id'];
                    $bb[$index]=$tikus[$k];
                    $index++;
                }
            }
            //根据困难程度[1-》易，2-》中,3-》难]，分别抽取5道题目
            $yitikus=array_filter($bb, function ($v) {
                return $v["difficult"]=="1";
            });
           
            $zhongtikus=array_filter($bb, function ($v) {
                return $v["difficult"]=="2";
            });
                 
            $nantikus=array_filter($bb, function ($v) {
                return $v["difficult"]=="3";
            });
            
            //数组随机抽取5到题目
            $ytk=shuffle($yitikus);
            $ztk=shuffle($zhongtikus);
            $ntk=shuffle($nantikus);
            //return  $yitikus;
            //组成试卷
            try {
                $testpaper[0]=$this->DealData($yitikus[0]);
                $testpaper[1]=$this->DealData($yitikus[1]);
                $testpaper[2]=$this->DealData($yitikus[2]);
                $testpaper[3]=$this->DealData($yitikus[3]);
                $testpaper[4]=$this->DealData($yitikus[4]);
                    
                $testpaper[5]=$this->DealData($zhongtikus[0]);
                $testpaper[6]=$this->DealData($zhongtikus[1]);
                $testpaper[7]=$this->DealData($zhongtikus[2]);
                $testpaper[8]=$this->DealData($zhongtikus[3]);
                $testpaper[9]=$this->DealData($zhongtikus[4]);
    
                $testpaper[10]=$this->DealData($nantikus[0]);
                $testpaper[11]=$this->DealData($nantikus[1]);
                $testpaper[12]=$this->DealData($nantikus[2]);
                $testpaper[13]=$this->DealData($nantikus[3]);
                $testpaper[14]=$this->DealData($nantikus[4]);

                return ['status' => 'success','message' => '获取题目成功','data'=>$testpaper];
            } catch (Exception $e) {
                return ['status' => 'fail','message' => '缺少题目!'];
            }
        }
    }
    //将数据库赋值给对象
    private function DealData($obj)
    {
        $resobj["type"]=$obj["showtype"];
        $resobj["id"]=$obj["id"];
        $resobj["question"]=$obj["title"];
        $resobj["pic"]=$obj["imgpath"]==null?"":$obj["imgpath"];
        $resobj["answers"][0]=$obj["optionA"];
        $resobj["answers"][1]=$obj["optionB"];
        $resobj["answers"][2]=$obj["optionC"];
        $resobj["answers"][3]=$obj["optionD"];
        $resobj["answers"][4]=$obj["optionE"];
        $resobj["answers"][5]=$obj["optionF"];
        $resobj["correct"]=$obj["answer"]-1;
        $resobj["tixing"]=$obj["tixingid"];
        return $resobj;
    }

    //获取项目的详情
    public function actionProdetail()
    {
       $mid = Yii::$app->request->post('mid');//分组id
        //最新的赛事
        $magagame = Megagame::find()->where(['status'=>1,'id'=>$mid])->one();
        
          
               
        if ($magagame==null) {
            return ['status' => 'fail','message' => '获取赛事详情失败'];
        } else {
            return ['status' => 'success','data' => $magagame];
        }
    }

    // 微信授权获取 openid
    public function wechatAuth($code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code';

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData([
                'appid' => Yii::$app->params['appid'],
                'secret' => Yii::$app->params['secret'],
                'js_code' => $code,
            ])
            ->send();
        if ($response->isOk) {
            $data = $response->data;
        }

        return $data;
    }
}
