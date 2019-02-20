<?php

namespace app\controllers;

use abei2017\wx\Application;
use app\models\Category;
use app\models\Knowledge;
use app\models\Knowset;
use app\models\Managame;
use app\models\Managroup;
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
                return ['status' => 'success', 'openid' => $openid,  'chancenum' =>5,'isnew'=>true];
            }else{
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
        
        if (empty($model)){ 
            $mmodel=new WechatUser();
            $mmodel->openid=$openid;
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
            }else{
                return $mmodel->getErrors();  
            }
        }else{
            $model->openid=$openid;
            $model->nickname=$nickname;
            $model->gender=$gender;
            $model->avatar=$avatar;
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
    //用户的答题次数减少
    public function actionUsechance()
    {
        //答题记录
        $openid = Yii::$app->request->post('openid');

        //用户的答题次数减少1
        $model= WechatUser::findOne(['openid' => $openid]);
        $model->chancenum=$model->chancenum-1;

        if ($model->save()) {
            return ['status' => 'success',  'message' => "答题次数减少"];
        }else{
            return $model->getErrors();  
        }
    }

    //答题记录
    public function actionRecord()
    {
        //答题记录
        $openid = Yii::$app->request->post('openid');
        $tid= Yii::$app->request->post('tid');
        $ids= Yii::$app->request->post('ids');
        $rightnum= Yii::$app->request->post('rightnum');
        $wrongnum= Yii::$app->request->post('wrongnum');
        
        //新增用户答题记录
        $record=new Record();
        $record->openid=$openid;
        $record->tid=$tid;
        $record->ids=$ids;
        $record->rightnum=$rightnum;
        $record->wrongnum=$wrongnum;
        $record->create_at=time();

        if ($record->save()) {
            return ['status' => 'success',  'message' => "保存答题记录成功"];
        }else{
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
        if($level==null){
            $level=0;
        }
        
        if($level<6){
            return ['status' => 'success',  'data' => 1]; 
        }else if($level>5&&$level<12){
            return ['status' => 'success',  'data' => 2]; 
        }else{
            return ['status' => 'success',  'data' => 3]; 
        }

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
