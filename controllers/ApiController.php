<?php

namespace app\controllers;

use abei2017\wx\Application;
use app\models\Activity;
use app\models\ActivityAddress;
use app\models\ActivityFee;
use app\models\ActivityImg;
use app\models\Address;
use app\models\BookActivity;
use app\models\Certificate;
use app\models\MyActivity;
use app\models\MyCertificate;
use app\models\UserAddress;
use app\models\UserInfo;
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
        $code = Yii::$app->request->post('code');

        if (!$code) {
            return ['status' => 'fail', 'message' => 'code 不能为空'];
        }
        
        //根据code查询，用户是否存在
        $data = $this->wechatAuth($code);

        if (isset($data['openid']) && isset($data['session_key'])) 
        {
            $openid = $data['openid'];
            $model = WechatUser::findOne(['openid' => $openid]);
            if (empty($model))
            {               
                return ['status' => 'success', 'openid' => $openid,  'isadmin' =>0,'isnew' =>0];
            }else{
                return ['status' => 'success', 'openid' => $openid,  'isadmin' => $model->isadmin,'isnew' =>1];
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
        $wxname = Yii::$app->request->post('wxname');
        $sex = Yii::$app->request->post('sex');
        $touxiang = Yii::$app->request->post('touxiang');
        $country = Yii::$app->request->post('country');
        $province = Yii::$app->request->post('province');        
        $city = Yii::$app->request->post('city');
        
        //验证openid是否存在
        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid不能为空'];
        }
        
        //根据openid查询，用户是否存在
        $model = WechatUser::findOne(['openid' => $openid]);
        
        if (empty($model)){ 
            $mmodel=new WechatUser();
            $mmodel->openid=$openid;
            $mmodel->wxname=$wxname;
            $mmodel->sex=$sex;
            $mmodel->touxiang=$touxiang;
            $mmodel->country=$country;
            $mmodel->province=$province;
            $mmodel->city=$city;             
            $mmodel->update_at= time();  
            $mmodel->isadmin=0;            
            $mmodel->create_at= time();    
            if ($mmodel->save()) {
                return ['status' => 'success',  'message' => "微信用户创建成功"];
            }else{
                return $mmodel->getErrors();  
            }
        }else{
            $model->openid=$openid;
            $model->wxname=$wxname;
            $model->sex=$sex;
            $model->touxiang=$touxiang;
            $model->country=$country;
            $model->province=$province;
            $model->city=$city;             
            $model->update_at= time();  

        if ($model->save()) {
            return ['status' => 'success',  'message' => "微信用户更新成功"];
        }else{
            return $model->getErrors();  
        }
      }
    }

    // 2获取考试列表
    public function actionGetactivity()
    {   
        //获取最新的上线的考试
        $models = Activity::find()->where([
            'status' => 1
        ])->orderBy('create_at desc')->all();

        $data = [];
        foreach ($models as $k => $model) {
            // 考试的基本信息
            $data[$k]['actid'] = $model->id; //考试id
            $data[$k]['cover'] =  $model->cover; //考试封面
            $data[$k]['name'] = $model->name; //考试标题
            $data[$k]['owner'] = $model->owner; //考试所有者
            $data[$k]['date'] = $model->date; //考试日期
            $data[$k]['startdt'] = $model->startdt; //考试开始时间
            $data[$k]['enddt'] = $model->enddt; //考试结束时间
            $data[$k]['status'] = $model->status; //考试结束时间
            $feeobj=ActivityFee::find()->where(['actid' => $model->id,'type'=>1])->one();
            $money=0;
            if(!empty($feeobj)){
                $money=$feeobj->fee;
            }
            $data[$k]['money'] = $money; //报名费用       
          }

        return $data;
    }
    
    //2-0 得到最新的考试
    public function actionGetnewactivity()
    {   
      //获取最新的上线的考试
      $model = Activity::find()->where(['status'=>1])->orderBy('create_at desc')->one();
      
      if(empty($model)){
        return ['status' => 'success', 'message' => '暂无考试','data'=>0];
      }else{
        return ['status' => 'success', 'message' => '获取考试成功','data'=>$model->id];
      }        
    }
    //2-1获取所有的考试
    public function actionGetallactivity()
    {   
      //获取最新的上线的考试
      $models = Activity::find()->where([
         'in','status',[0,1,2]
      ])->orderBy('create_at desc')->all();

      $data = [];
      foreach ($models as $k => $model) {
         // 考试的基本信息
         $data[$k]['actid'] = $model->id; //考试id
         $data[$k]['cover'] =  $model->cover; //考试封面
         $data[$k]['name'] = $model->name; //考试标题
         $data[$k]['owner'] = $model->owner; //考试所有者
         $data[$k]['date'] = $model->date; //考试日期
         $data[$k]['startdt'] = $model->startdt; //考试开始时间
         $data[$k]['enddt'] = $model->enddt; //考试结束时间
         $data[$k]['status'] = $model->status; //考试结束时间
         $feeobj=ActivityFee::find()->where(['actid' => $model->id,'type'=>1])->one();
         $money=0;
         if(!empty($feeobj)){
             $money=$feeobj->fee;
         }
         $data[$k]['money'] = $money; //报名费用       
       }

       return $data;
    }

    // 2-1 清除无效的数据
    public function actionClearactivity()
    {
        //获取无效的考试列表
        $models = Activity::find()->where([
            'status' => -1,
        ])->all();

        //循环删除
        foreach ($models as $k => $model) {
            //删除考试的图片
            $hdimgs =ActivityImg::find()->where(['actid' => $model->id])->all();
            //循环遍历得到考试对应的任务
            foreach ($hdimgs as $i => $hdimg) { //删除考试详情和指引
                $imgpath = $hdimg->imgpath;
                if (is_file($imgpath)) {
                    unlink($imgpath);
                }
                $hdimg->delete();
            }
            //删除考试费用
            $actfees = ActivityFee::find()->where(['actid' => $model->id])->all();
            foreach ($actfees as $i => $item) {
                $item->delete();
            }
            //删除考试地址
            $actaddressss = ActivityAddress::find()->where(['actid' => $model->id])->all();
            foreach ($actaddressss as $i => $item) { 
                $item->delete();
            }
            //删除考试证书
            $certs = Certificate::find()->where(['actid' => $model->id])->all();
            foreach ($certs as $i => $item) { 
                $item->delete();
            }
            //删除考试
            $model->delete();
        }
        return ['status' => 'success', 'message' => '清除数据成功'];
    }

    // 2-2 获取考试基本信息
    public function actionActivityinfo()
    {
        $id = Yii::$app->request->post('id');
        $openid = Yii::$app->request->post('openid');
        //根据考试id，得到
        $model = Activity::findOne(['id' => $id]);
        $data=[];
        if(!empty($model)) {
            // 考试的基本信息
            $data[0]['actid'] = $model->id; //考试id
            $data[0]['cover'] = $model->cover; //考试封面
            $data[0]['name'] = $model->name; //考试标题
            $data[0]['owner'] = $model->owner; //考试所有者
            $data[0]['startdt'] = $model->startdt; //考试开始日期
            $data[0]['enddt'] =$model->enddt; //考试结束日期
            $data[0]['date'] =$model->date; //考试结束日期
            $data[0]['status'] =$model->status; //考试结束日期
            $feeobj=ActivityFee::find()->where(['actid' => $model->id,'type'=>1])->one();
            $money=0;
            if(!empty($feeobj)){
                $money=$feeobj->fee;
            }   
            $data[0]['money'] = $money;  //报名费
        }
             
        //是否存在报名记录
        $isbookobj=BookActivity::find()->where(['actid'=>$id,'openid'=>$openid])->one();
        $isbook=false;
        if(!empty($isbookobj)){
            $data[0]['isbook']=true;
        }else{
            $data[0]['isbook']=false;
        }
        return ['status' => 'success', 'message' => '获取数据成功','data'=>$data];
    }
     
    // 2-3 获取考试基本信息
    public function actionActbaseinfo()
    {
        $id = Yii::$app->request->post('id');
        //根据考试id，得到
        $model = Activity::findOne(['id' => $id]);
        if(!empty($model)) {
            // 考试的基本信息
            $data['actid'] = $model->id; //考试id
            $data['cover'] = $model->cover; //考试封面
            $data['name'] = $model->name; //考试标题
            $data['owner'] = $model->owner; //考试所有者
            $data['startdt'] = $model->startdt; //考试开始日期
            $data['enddt'] =$model->enddt; //考试结束日期
            $data['date'] =$model->date; //考试结束日期
            $data['status'] =$model->status; //考试结束日期
            $feeobj=ActivityFee::find()->where(['actid' => $model->id,'type'=>1])->one();
            $money=0;
            if(!empty($feeobj)){
                $money=$feeobj->fee;
            }   
            $data['money'] = $money;  //报名费

            return ['status' => 'success', 'message' => '获取数据成功','data'=>$data];
        }else{
            return ['status' => 'fail', 'message' => 'actid不能为空'];
        }
    }

    // 3获取考试图片
    public function actionActivityimg()
    {
        $actid = Yii::$app->request->post('actid');
        $type = Yii::$app->request->post('type');
        if (!$actid) {
            return ['status' => 'fail', 'message' => 'actid不能为空'];
        }

        if (!$type) {
            return ['status' => 'fail', 'message' => 'type不能为空'];
        }

        $models = ActivityImg::find()->where([
            'actid' => $actid,
            'type' => $type,
        ])->all();
        $data = [];
        foreach ($models as $k => $model) {
            $data[$k]['id'] = $model->id;
            $data[$k]['imgpath'] =  $model->imgpath;
        }
       
        return ['status' => 'success', 'message' => '获取数据成功','data'=>$data];
    }

    // 4保存用户信息
    public function actionEdituserinfo()
    {
        //参数部分
        $openid = Yii::$app->request->post('openid');
        $name = Yii::$app->request->post('name');
        $namecn = Yii::$app->request->post('namecn');
        $sex = Yii::$app->request->post('sex');     
        $idcode = Yii::$app->request->post('idcode');
        $phone = Yii::$app->request->post('phone');
        $email = Yii::$app->request->post('email');
        $school = Yii::$app->request->post('school');
        $xueyuan = Yii::$app->request->post('xueyuan');
        $banji = Yii::$app->request->post('banji');

        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid不能为空'];
        }
        //根据openid获取得到用户信息
        $model = UserInfo::findOne(['openid' => $openid]);
        
        if(empty($model)){
            //赋值部分
            $mmodel=new UserInfo();           
            $mmodel->openid= $openid;
            $mmodel->name= $name;
            $mmodel->namecn= $namecn;
            $mmodel->sex= $sex;
            $mmodel->idcode= $idcode;
            $mmodel->phone= $phone;       
            $mmodel->email= $email;
            $mmodel->school= $school;
            $mmodel->xueyuan= $xueyuan;
            $mmodel->banji= $banji;
            $mmodel->update_at= time();
            $mmodel->create_at= time();
        
           if ($mmodel->save()) {
            return ['status' => 'success','data'=>$mmodel,'message'=>'创建用户信息成功'];
           }else{
            return $mmodel->getErrors();
           }
        }else{
            //赋值部分
            $model->openid= $openid;
            $model->name= $name;
            $model->namecn= $namecn;
            $model->sex= $sex;
            $model->idcode= $idcode;
            $model->phone= $phone;       
            $model->email= $email;
            $model->school= $school;
            $model->xueyuan= $xueyuan;
            $model->banji= $banji;
            $model->update_at= time();
        
           if ($model->save()) {
            return ['status' => 'success','data'=>$model,'message'=>'更新用户信息成功'];
           }else{
            return $model->getErrors();
          }
        }             
    }

    // 5编辑用户地址
    public function actionEdituseraddress()
    {
        $openid = Yii::$app->request->post('openid');
        $province = Yii::$app->request->post('province');
        $city = Yii::$app->request->post('city');
        $district = Yii::$app->request->post('district');
        $detail = Yii::$app->request->post('detail');
        $name = Yii::$app->request->post('name');
        $phone = Yii::$app->request->post('phone');

        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid不能为空'];
        }
        //赋值部分
        $model = UserAddress::findOne(['openid' => $openid]);
        

        if(empty($model)){
            $mmodel=new UserAddress();
            $mmodel->openid = $openid;
            $mmodel->province = $province;
            $mmodel->city = $city;
            $mmodel->district = $district;
            $mmodel->detail = $detail;
            $mmodel->name = $name;
            $mmodel->phone = $phone;
            $mmodel->update_at = time();
            $mmodel->create_at = time();

            if ($mmodel->save()) { 
                return ['status' => 'success','data'=>$mmodel,'message'=>'创建邮寄地址成功'];
            }else{
                return $mmodel->getErrors();
            }            
        }else{
            $model->openid = $openid;
            $model->province = $province;
            $model->city = $city;
            $model->district = $district;
            $model->detail = $detail;
            $model->name = $name;
            $model->phone = $phone;
            $model->update_at = time();
           if ($model->save()) { 
             return ['status' => 'success','data'=>$model,'message'=>'更新邮寄地址成功'];
           }else{
            return $model->getErrors();
          }
        }
    }

    // 6报名考试
    public function actionBookactivity()
    {
        $openid = Yii::$app->request->post('openid');
        $actid = Yii::$app->request->post('actid');
        $feeids = Yii::$app->request->post('feeids');
        $addressid = Yii::$app->request->post('addressid');
        $sumprice = Yii::$app->request->post('sumprice');

        if (!$openid||!$actid||!$feeids||!$sumprice||!$addressid) {
            return ['status' => 'fail', 'message' => '参数缺失'];
        }
        //活动报名
        $model =new BookActivity();
        $model->openid = $openid;
        $model->actid = $actid;
        $model->feeids = $feeids;
        $model->addressid = $addressid;
        $model->sumprice = $sumprice;
        $model->create_at = time();
        $model->update_at = time();        

        
        //我的报名
        $myact=new MyActivity();
        $myact->actid = $actid;
        $myact->openid = $openid;
        $myact->addressid= $addressid;
        $myact->create_at=time();
        $myact->update_at=time();

        if ($model->save()&&$myact->save()) { 
            return ['status' => 'success','message'=>'报名成功'];
        }else{
            return ['status' => 'fail','message'=>'报名失败!'];
        }
    }

    // 7获取用户基本信息
    public function actionGetuserinfo()
    {
        $openid = Yii::$app->request->post('openid');

        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid参数缺失'];
        }
        //赋值部分
        $model =UserInfo::findOne(['openid'=>$openid]);
        if(empty($model)){
            return ['status' => 'fail','message'=>'查无此用户信息'];
        }else{
            return ['status' => 'success','data'=>$model];
        }
    }

    // 8获取用户邮寄地址
    public function actionGetuseraddress()
    {
        $openid = Yii::$app->request->post('openid');

        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid参数缺失'];
        }
        //赋值部分
        $model =UserAddress::findOne(['openid'=>$openid]);
        if(empty($model)){
            return ['status' => 'fail','message'=>'查无此用户邮寄地址'];
        }else{
            return ['status' => 'success','data'=>$model];
        }
    }

    // 9获取考试地址
    public function actionActivityaddress()
    {
        $actid = Yii::$app->request->post('actid');

        if (!$actid) {
            return ['status' => 'fail', 'message' => 'openid参数缺失'];
        }
        //赋值部分
        $datalist =ActivityAddress::find()->where(['status'=>0])->all();
        $data=[];
        foreach ($datalist as $k => $model) {
            $data[$k]= $model->address;
        }
        return ['status' => 'success','data'=> $data,'message'=>'获取考试地址成功'];
    }

    
    // 9-1获取全部的地址
    public function actionAlladdress()
    {        
        //赋值部分
        $data =Address::find()->where(['status'=>0])->all();
        return ['status' => 'success','data'=> $data,'message'=>'获取地址成功'];
    }
    
    // 9-2获取地址详情
    public function actionGetaddress()
    {
        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['status' => 'fail', 'message' => 'id参数缺失'];
        }
        //赋值部分
        $data =Address::find()->where(['id'=>$id])->one();
        return ['status' => 'success','data'=> $data,'message'=>'获取考试地址成功'];
    }

    // 9-3保存地址
    public function actionEditaddress()
    {
        $id = Yii::$app->request->post('id');
        $province = Yii::$app->request->post('province');
        $city = Yii::$app->request->post('city');
        $district = Yii::$app->request->post('district');
        $detail = Yii::$app->request->post('detail');
        $name = Yii::$app->request->post('name');
        $isfee = Yii::$app->request->post('isfee');

        
        //赋值部分
        $model =Address::find()->where(['id'=>$id])->one();
        if(empty($model)){
            $mmodel =new  Address();                  
            $mmodel->province=$province;
            $mmodel->city=$city;
            $mmodel->district=$district;
            $mmodel->detail=$detail;
            $mmodel->name=$name;
            $mmodel->isfee=$isfee;
            $mmodel->status=0;
            $mmodel->update_at=time();
            $mmodel->create_at=time();
            
            if($mmodel->save()){
              return ['status' => 'success','data'=> $mmodel,'message'=>'创建考试地址成功'];
            }else{
              return $mmodel->getErrors();
            }

        }else{
      
            $model->province=$province;
            $model->city=$city;
            $model->district=$district;
            $model->detail=$detail;
            $model->name=$name;
            $model->isfee=$isfee;
            $model->status=0;
            $model->update_at=time();
            if($model->save()){
              return ['status' => 'success','data'=> $model,'message'=>'更新考试地址成功'];
            }else{
              return $model->getErrors();
            }
        }
    }

    // 9-4删除地址
    public function actionDeleteaddress()
    {
        $id = Yii::$app->request->post('id');
        
        //赋值部分
        $model =Address::find()->where(['id'=>$id])->one();
        if(empty($model)){
            return ['status' => 'fail','message'=>'地址不存在'];  
        }else{
            $model->status=1;            
            if($model->save()){
               return ['status' => 'success','data'=> $model,'message'=>'删除地址成功'];
            }else{
               return $model->getErrors();
            }           
        }
    }

    // 9-5保存考试的地址
    public function actionEditactadd()
    {
        $id = Yii::$app->request->post('id');
        $actid = Yii::$app->request->post('actid');
        $addressid = Yii::$app->request->post('addressid');
        $status = Yii::$app->request->post('status');

        if (!$id) {
            return ['status' => 'fail', 'message' => '参数缺失'];
        }
        //赋值部分
        $model =ActivityAddress::find()->where(['id'=>$id])->one();
        if(empty($model)){
            $mmodel =new  ActivityAddress();                  
            $mmodel->actid=$actid;
            $mmodel->addressid=$addressid;
            $mmodel->status=$status;
            $mmodel->update_at=time();
            $mmodel->create_at=time();
            
            if($mmodel->save()){
              return ['status' => 'success','data'=> $mmodel,'message'=>'创建考试地址成功'];
            }else{
              return $mmodel->getErrors();
            }

        }else{
            $model->actid=$actid;
            $model->addressid=$addressid;
            $model->status=$status;
            $model->update_at=time();
            if($model->save()){
              return ['status' => 'success','data'=> $model,'message'=>'更新考试地址成功'];
            }else{
              return $model->getErrors();
            }
        }
    }

    // 9-6考场学生
    public function actionKaochangdata()
    {
        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['status' => 'fail', 'message' => '参数缺失'];
        }
        //赋值部分
        $datalist =MyActivity::find()->where(['addressid'=>$id])->all();
        $data = [];
        $index=0;
        foreach ($datalist as $k => $dl) {
            // 学生的基本信息
            $data[$k]=$dl->userinfo;               
        }

        return ['status' => 'success','data'=>$data];
    }


    // 10编辑考试地址
    public function actionEditactaddress()
    {
        $id = Yii::$app->request->post('id');
        $actid = Yii::$app->request->post('actid');
        $addressid = Yii::$app->request->post('addressid');
        $status = Yii::$app->request->post('status');

        if (!$actid) {
            return ['status' => 'fail', 'message' => 'openid参数缺失'];
        }

        //考试地址信息
        $model =ActivityAddress::find()->where(['id'=>$id])->one();
        
        if(empty($model)){
            $mmodel=new ActivityAddress();              
            $mmodel->actid=$actid;           
            $mmodel->addressid=$addressid;
            $mmodel->status=$status;            
            $mmodel->create_at=time();  
            $mmodel->update_at=time();
                
            if($mmodel->save()){
                return ['status' => 'success','data'=> $mmodel,'message'=>'创建考试地址成功'];
            }else{
                return $mmodel->getErrors();
            }
        }else{                
            $model->actid=$actid;           
            $model->addressid=$addressid;
            $model->status=$status;
            $model->status=$status;
            $model->update_at=time();
        
            if($model->save()){
               return ['status' => 'success','data'=> $model,'message'=>'更新考试地址成功'];
            }else{
               return $model->getErrors();
            }
        }
        
    }

    // 11-0判断该考点报名费用
    public function actionActivityfee()
    {
        $actid = Yii::$app->request->post('actid');
        $addressid = Yii::$app->request->post('addressid');

        if (!$actid) {
            return ['status' => 'fail', 'message' => 'openid参数缺失'];
        }
        //考试费用
        $data =ActivityFee::find()->where(['actid'=>$actid])->all();

        //考点是否先缴费
        $feeobj =Address::find()->where(['id'=>$addressid])->one();
        $isfee=false;
        
        if(!empty($feeobj)){
            $isfee=$feeobj->isfee==0?false:true;
        }
        return ['status' => 'success', 'data' => $data,'isfee'=>$isfee];
    }

    // 11-1编辑考试费用
    public function actionEditactfee()
    {
        $actid = Yii::$app->request->post('actid');
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->post('type');
        $name = Yii::$app->request->post('name');
        $money = Yii::$app->request->post('money');

        if (!$actid) {
            return ['status' => 'fail', 'message' => 'openid参数缺失'];
        }

        //考试费用
        $model =ActivityFee::find()->where(['id'=>$id])->one();
        
        if(empty($model)){
            $mmodel=new  ActivityFee();
            $mmodel->create_at=time();
            $mmodel->actid=$actid;
            $mmodel->type=$type;
            $mmodel->name=$name;
            $mmodel->fee=$money;
            $mmodel->update_at=time(); 
             
            if($mmodel->save()){
                return ['status' => 'success','data'=> $mmodel,'message'=>'创建考试费用成功'];
            }else{
                return $model->getErrors();
            }
        }else{
               
            $model->actid=$actid;
            $model->type=$type;
            $model->name=$name;
            $model->fee=$money;
            $model->update_at=time();
              
            if($model->save()){
                return ['status' => 'success','data'=> $model,'message'=>'更新考试费用成功'];
            }else{
                return $model->getErrors();
            }
        }
    }

    // 11-2 删除考试费用
    public function actionDeleteactfee(){
       $id = Yii::$app->request->post('id');
       $model =ActivityFee::find()->where(['id'=>$id])->one();
       if(!empty($model)){
          //删除考试
          $model->delete();
          return ['status' => 'success','message' => '删除考试成功'];
       }else{
          return ['status' => 'fail','message' => '查询不到此费用'];
       }
    } 

    // 12编辑考试
    public function actionEditactivity()
    {      
        $id = Yii::$app->request->post('id');
        $cover = Yii::$app->request->post('cover');
        $name = Yii::$app->request->post('name');
        $owner = Yii::$app->request->post('owner');
        $date = Yii::$app->request->post('date');
        $startdt = Yii::$app->request->post('startdt');
        $enddt = Yii::$app->request->post('enddt');
        $status = Yii::$app->request->post('status');
    
       
        //考试地址信息
        $model =Activity::find()->where(['id'=>$id])->one();
        
        if(empty($model)){
            $mmodel=new Activity();
            $mmodel->cover=$cover;
            $mmodel->name=$name;
            $mmodel->owner=$owner;
            $mmodel->date=$date;
            $mmodel->startdt=$startdt;
            $mmodel->enddt=$enddt;
            $mmodel->status=$status;
            $mmodel->update_at=time();
            $mmodel->create_at=time();

            if($mmodel->save()){
                return ['status' => 'success','data'=> $mmodel,'创建考试成功'];
            }else{
                return $mmodel->getErrors();
            }
        }else{
            $model->cover=$cover;
            $model->name=$name;
            $model->owner=$owner;
            $model->date=$date;
            $model->startdt=$startdt;
            $model->enddt=$enddt;
            $model->status=$status;
            $model->update_at=time();

            if($model->save()){
                return ['status' => 'success','data'=> $model,'更新考试成功'];
            }else{
                return $model->getErrors();
            }
        }
    }

    // 13上传图片
    public function actionUploadimg()
    {      
        $upload= UploadedFile::getInstanceByName('file');       
     
        //return $params;
        // 处理文件
        $path = 'uploads/images/' . uniqid() . '.' . $upload->extension;
        if($upload->saveAs($path)){
            $imgpath= Yii::$app->request->hostInfo . '/' . $path;
            return ['status' => 'success', 'message' => '图片上传成功','data'=>$imgpath]; //图片的路径
        }else{
            return ['status' => 'fail', 'message' => '图片保存失败'];
        }
  }

    // 14保存考试图片
    public function actionEditactimg()
    {      
        $id = Yii::$app->request->post('id');
        $actid = Yii::$app->request->post('actid');
        $imgpath = Yii::$app->request->post('imgpath');
        $type = Yii::$app->request->post('type');

        if (!$actid) {
            return ['status' => 'fail', 'message' => 'actid参数缺失'];
        }
        if (!$type) {
            return ['status' => 'fail', 'message' => 'type参数缺失'];
        }

        //考试的图片
        $model =ActivityImg::find()->where(['id'=>$id])->one();
       
        if(empty($model)){
            $mmodel =new ActivityImg();
            $mmodel->actid=$actid;
            $mmodel->imgpath=$imgpath;
            $mmodel->type=$type;
            $mmodel->update_at=time();
            $mmodel->create_at=time();
            if($mmodel->save()){
                return ['status' => 'success','data'=> $mmodel,'message'=>'创建考试图片'];
            }else{
                return $mmodel->getErrors();
            }    
        }else{
            $model->actid=$actid;
            $model->imgpath=$imgpath;
            $model->type=$type;
            $model->update_at=time();
            if($model->save()){
                return ['status' => 'success','data'=> $model,'message'=>'更新考试图片'];
            }else{
                return $model->getErrors();
            }    
        }
    }

    // 14-1删除考试图片
    public function actionDeleteactimg()
    {      
         $id = Yii::$app->request->post('id');
 
         //考试的图片
         $model =ActivityImg::find()->where(['id'=>$id])->one();         
         if($model->delete()){
            return ['status' => 'success','message'=>'删除考试图片成功'];
        }else{
            return $model->getErrors();
        }   
    }   


    // 15我的证书
    public function actionMycertificate()
    {      
        $openid = Yii::$app->request->post('openid');

        if (!$openid) {
            return ['status' => 'fail', 'message' => 'openid参数缺失'];
        }

        //我的证书
        $data =MyCertificate::find()->where(['openid'=>$openid])->all();
        
        return ['status' => 'success', 'message' => '获取证书成功','data'=>$data];
        
    }


    // 16考试证书的编辑
    public function actionEditcetificate()
    {      
        $id = Yii::$app->request->post('id');
        $actid = Yii::$app->request->post('actid');
        $name = Yii::$app->request->post('name');
        $startdate = Yii::$app->request->post('startdate');
        $enddate = Yii::$app->request->post('enddate');
        $minscore = Yii::$app->request->post('minscore');
        $maxscore = Yii::$app->request->post('maxscore');
        $opttype = Yii::$app->request->post('opttype');


        if (!$actid) {
            return ['status' => 'fail', 'message' => 'actid参数缺失'];
        }
        if (!$opttype) {
            return ['status' => 'fail', 'message' => 'opttype参数缺失'];
        }

        //考试的证书
        $model =Certificate::find()->where(['id'=>$id])->one();
        if($opttype!=2){          
            //删除
            $model->delete();
            return ['status' => 'success'];
        }else{
            $model->actid=$actid;
            $model->name=$name;
            $model->startdate=$startdate;
            $model->enddate=$enddate;
            $model->minscore=$minscore;
            $model->maxscore=$maxscore;
            $model->update_at=time();
            if(empty($model)){
                $model->create_at=time();
            }
            if($model->save()){
                return ['status' => 'success','data'=> $model];
            }else{
                return $model->getErrors();
            }

        }
    }

    
    // 17考试证书列表
    public function actionActivitycetlist()
    {      

        $actid = Yii::$app->request->post('actid');

        if (!$actid) {
            return ['status' => 'fail', 'message' => 'actid参数缺失'];
        }

        //考试的证书
        $data =Certificate::find()->where(['actid'=>$actid])->all();
        return ['status' => 'success','data'=> $data];
    }

    // 18我的考试列表
    public function actionMyactlist()
    {
        $openid = Yii::$app->request->post('openid');     
        $type = Yii::$app->request->post('type');

        if (!$openid || is_null($type)) {
            return ['status' => 'fail', 'message' => '参数错误'];
        }
        if ($type == 0) {
            $where = ['myactivity.openid' => $openid];
        }
        if ($type == 1) {
            $where = ['activity.status' => 1, 'myactivity.openid' => $openid];
        }
        if ($type == 2) {
            $where = ['activity.status' => 2, 'myactivity.openid' => $openid];
        }
        $models = MyActivity::find()->joinWith('activity')->where($where)->all();
        $data = [];
        foreach ($models as $k => $model) {
            $data[$k]['actid'] = $model->activity->id;
            $data[$k]['cover'] = $model->activity->cover;
            $data[$k]['title'] = $model->activity->name;
            $data[$k]['owner'] = $model->activity->owner;
            $data[$k]['status'] = $model->activity->status;
            $data[$k]['date'] = $model->activity->date;
            $data[$k]['startdt'] = $model->activity->startdt;
            $data[$k]['enddt'] =$model->activity->enddt;
          
            $feeobj=ActivityFee::find()->where(['actid' => $model->id,'type'=>1])->one();
            $money=0;
            if(!empty($feeobj)){
                $money=$feeobj->fee;
            }
            $data[$k]['money'] = $money; //报名费用
        }
        return ['status' => 'success','data'=> $data];
    }

    //19更新考试的状态
    public function actionUpdateactstatus(){
        $actid = Yii::$app->request->post('actid');     
        $status = Yii::$app->request->post('status');

        if (!$actid) {
            return ['status' => 'fail', 'message' => '参数错误'];
        }
        $model = Activity::find()->where(['id'=>$actid])->one();
        if(empty($model)){
            return ['status' => 'fail', 'message' => '该活动已不存在'];
        }else{
          $model->status=$status;
          if($model->save()){
            return ['status' => 'success', 'message' => '保存成功'];
          }else{
            return ['status' => 'fail', 'message' => '保存失败'];
          }
        }
        
    }

    //20 得到考点的考试
    public function actionGetactaddress(){
        $addressid = Yii::$app->request->post('addressid');   

        if (!$addressid) {
            return ['status' => 'fail', 'message' => '参数错误'];
        }
        $models = ActivityAddress::find()->where(['addressid'=>$addressid])->all();       
        $data=[];         
        foreach($models as $k=>$model){
            $actobj=Activity::find()->where(['id'=>$model->actid])->one();
            $data[$index]=$actobj;
        }
        return ['status' => 'success', 'message' => '获取数据成功','data'=>$data];        
    }

    //21 得到考点考试的报名人数
    public function actionBookactuser(){
        $addressid = Yii::$app->request->post('addressid');   
        $actid = Yii::$app->request->post('actid');   
        if (!$addressid||$actid) {
            return ['status' => 'fail', 'message' => '参数错误'];
        }
        $datalist = MyActivity::find()->where(['addressid'=>$addressid,'actid'=>$actid])->all();       
        //赋值部分        
        $data = [];
        foreach ($datalist as $k => $dl) {
          // 学生的基本信息
          $data[$k]=$dl->userinfo;               
        }
        return ['status' => 'success', 'message' => '获取数据成功','data'=>$data];        
    }

    //22 得到考点考试的报名人数
    public function actionGetbookuser(){
        $addressid = Yii::$app->request->post('addressid');   
        
        //查询获取最新的上线考试
        $model = Activity::find()->where(['status'=>1])->orderBy('create_at desc')->one();  
        //['or' , ['status' => [1,2]];'status'=>2
        $actid =0; 
        if(!empty($model)){
            $actid = $model->id; 
        }
        //return ['status' => 'success', 'message' => '获取数据成功','data'=>$actid];     
        if (!$addressid) {
            return ['status' => 'fail', 'message' => '参数错误'];
        }
        $datalist = MyActivity::find()->where(['addressid'=>$addressid,'actid'=>$actid])->all();       
        //赋值部分        
        $data = [];
        foreach ($datalist as $k => $dl) {
          // 学生的基本信息
          $data[$k]=$dl->userinfo;               
        }
        return ['status' => 'success', 'message' => '获取数据成功','data'=>$data];        
    }
    // 小程序码
    public function actionWxcode()
    {
        $conf = Yii::$app->params['wx']['mini'];
        $app = new Application(['conf' => $conf]);
        $qrcode = $app->driver("mini.qrcode");
        $path = "/pages/index/index";
        $scene = "123456";
        $qrcode->unLimit($scene, $page, $extra = []);
    }

    // 消息模板
    public function actionNewsmuban()
    {

        $conf = Yii::$app->params['wx']['mini'];
        $app = new Application(['conf' => $conf]);
        $template = $app->driver("mini.template");
        $templateId="";//模板ID
        $formId=Yii::$app->request->post('formId'); // formId的值
        $toUser=Yii::$app->request->post('openid'); // openid的值
        $data=[];//模板内容，不填则下发空模板
        $extra = [];//其他参数，都放到$extra数组中，比如page、color、emphasis_keyword
        $template->send($toUser, $templateId, $formId, $data, $extra);
        /*
        $toUser 接收者（用户）的 openid
        $templateId 所需下发的模板消息的id
        $formId 表单提交场景下，为 submit 事件带上的 formId；支付场景下，为本次支付的 prepay_id
        $data 模板内容，不填则下发空模板
        $extra 其他参数，都放到$extra数组中，比如page、color、emphasis_keyword
         */
        return ['status' => 'success', 'message' => '发送成功'];

    }
    
    //微信小程序支付功能
    public function actionPayfor(){
        $conf = Yii::$app->params['wx']['mini'];        
        $app = new Application(['conf' => $conf]);
        $payment = $app->driver("mini.pay");
        $money= Yii::$app->request->post('money'); // money的值
        $openid= Yii::$app->request->post('openid'); // openid的值
        $out_trade_no=time() . mt_rand(1000,1000000);//订单号
        
        $param = array(
            'appid' =>Yii::$app->params['appid'],//小程序id            
            'body' =>"报名", //商品信息  
            'mch_id'=> Yii::$app->params['mch_id'],//商户id
            'notify_url'=>'notice/index.php', //回调通知地址
            'nonce_str'=> $this->createNoncestr(),
            'out_trade_no'=>$out_trade_no,//商户订单编号
            'total_fee'=>$money*100, //总金额
            'openid'=>$openid,//用户openid
            'trade_type'=>'JSAPI',//交易类型  
            'spbill_create_ip'=>"139.129.230.192",//终端ip           
            );

        //通过签名算法计算得出的签名值，详见签名生成算法
        $result = $this->createJsBizPackage($openid, $money, $out_trade_no,'报名费',"notice/index.php", time());
        //return $package;
        //生成小程序签名
        $config="appId=".Yii::$app->params['appid']."&nonceStr=".$this->createNoncestr()."&package=prepay_id=".$result['prepay_id']."&signType=MD5&timeStamp=".time()."&key=".Yii::$app->params['key'];
        //return  $config;
        $String = md5($config);
        //字符转为大写
        $sign = strtoupper($String);
        //$package=$result['package'];
        $package=$result;
        return ['status'=>'success','nonceStr'=>$this->createNoncestr(),'package'=>$package,'sign'=>$sign];
    }


   /*
* 对要发送到微信统一下单接口的数据进行签名
*/
protected function getNewSign($Obj){
    foreach ($Obj as $k => $v){
        $param[$k] = $v;
    }
    //签名步骤一：按字典序排序参数
    ksort($param);
    $String = $this->formatBizQueryParaMap($param, false);
    //签名步骤二：在string后加入KEY
    $String = $String."&key=". Yii::$app->params['key'];
    //签名步骤三：MD5加密
    $String = md5($String);
    //签名步骤四：所有字符转为大写
    $result_ = strtoupper($String);
    return $result_;
}
    // 小程序支付
    public function actionWxpay()
    {
        $conf = Yii::$app->params['wx']['mini'];        
        $app = new Application(['conf' => $conf]);
        $payment = $app->driver("mini.pay");
        $money= Yii::$app->request->post('money'); // money的值
        $openid= Yii::$app->request->post('openid'); // openid的值
        $out_trade_no=time() . mt_rand(1000,1000000);//订单号
        $attributes = [
            'body' => "购买",
            'out_trade_no' => $out_trade_no,
            'total_fee' => $money*100,
            'notify_url' => Yii::$app->urlManager->createAbsoluteUrl(['/order/notify']),
            'openid' => $openid,
        ];
        

        $jsApi = $payment->jsApi($attributes);
        return $jsApi;
        if ($jsApi->return_code == 'SUCCESS' && $jsApi->result_code == 'SUCCESS') {
            $prepayId = $jsApi->prepay_id;
        }
        $result = $payment->configForPayment($prepayId);
        /*
        $result是一个数组，里面包含appId、timeStamp、nonceStr、package、signType、paySign。
         */
        return $result;
    }

    // OPNEID
    public function actionGetopenid()
    {
        $conf = Yii::$app->params['wx']['mini'];
        $app = new Application(['conf' => $conf]);
        $user = $app->driver("mini.user");
        $code = Yii::$app->request->post('code'); // code的值
        $result = $user->codeToSession($code);
        /*
        $result是一个数组，里面包含appId、timeStamp、nonceStr、package、signType、paySign。
         */
        return $result;
    }
    // ******************************** 处理方法 *****************************

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
    //微信支付
    public function createJsBizPackage($openid, $totalFee, $outTradeNo, $orderName, $notifyUrl, $timestamp){
        $config = array(
            'mch_id' => Yii::$app->params['mch_id'],
            'appid' =>  Yii::$app->params['appid'],
            'key' => Yii::$app->params['key'],
        );
        $unified = array(
            'appid' => $config['appid'],
            'attach' => '支付',
            'body' => $orderName,
            'mch_id' => $config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $notifyUrl,
            'openid' => $openid,
            'out_trade_no' => $outTradeNo,
            'spbill_create_ip' => '127.0.0.1',
            'total_fee' => intval($totalFee * 100),
            'trade_type' => 'JSAPI',
        );
        $unified['sign'] = self::getSign($unified, $config['key']);
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($unifiedOrder === false) {
            die('parse xml error');
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            die($unifiedOrder->return_msg);
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            die($unifiedOrder->err_code);
        }
        $arr = array(
            "appId" => $config['appid'],
            "timeStamp" => $timestamp,
            "nonceStr" => self::createNonceStr(),
            "package" => "prepay_id=" . $unifiedOrder->prepay_id,
            "signType" => 'MD5',
        );
        $arr['paySign'] = self::getSign($arr, $config['key']);
        return $arr;
    }
    public static function curlGet($url = '', $options = array()){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public static function curlPost($url = '', $postData = '', $options = array()){
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public static function createNonceStr($length = 16){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i<$length; $i++){
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public static function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key => $val){
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
    public static function getSign($params, $key){
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }
    protected static function formatQueryParaMap($paraMap, $urlEncode = false){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff)>0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}
