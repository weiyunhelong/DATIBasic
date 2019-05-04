<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>推星官活动平台</title>
    <?php $this->head() ?>
    <!--引用资源-->
    <link href="/css/common.css" rel="stylesheet" />
    <script src="/jquery/jquery-2.1.1.min.js"></script>
    <script src="/bootstrap/bootstrap.js"></script>
    <link href="/bootstrap/bootstrap.min.css" rel="stylesheet" />
    <link href="/layer/skin/layer.css" rel="stylesheet" />
    <script src="/layer/layer.js"></script>
    <!--树形结构的数据-->
<!--<script src="/zTree/js/jquery-1.4.4.min.js" type="text/javascript"></script>-->
<script src="/zTree/js/jquery.ztree.all.js" type="text/javascript"></script>
<script src="/zTree/js/jquery.ztree.core.js" type="text/javascript"></script>
<script src="/zTree/js/jquery.ztree.excheck.js" type="text/javascript"></script>
<script src="/zTree/js/jquery.ztree.exhide.js" type="text/javascript"></script>

    <style type="text/css">
     .nav .nav-sidebar li a{
         height:50px;
         line-height:50px;
         color:#fff;
     }
     .nav .nav-sidebar li .active a{
         height:50px;
         line-height:50px;
         color:#fff;
     }

     .container-fluid {
    padding-right: 20px;
    padding-left: 0px; 
    margin-right: auto;
    margin-left: auto;
    }
     
    .menu{
       color:#fff;
       margin-left:0px;
    }

    .topoptv{
        display: flex;
    justify-content: space-around;
    margin-bottom:0px;
    box-sizing: border-box;
    font-size: 12px;
    line-height: 1.42857143;
    }

    .topleftv{
        width:60%;
        height:50px;
        text-align:left;
        display:flex;
    }

    .searchbtn{
       color: #fff;
       background-color: #337ab7;
       height: 34px;
       margin-left: 10px;
       border: 0;
       padding: 10px 20px;
       border-radius: 5px;
    }
    .toprightv{
        width:40%;
        height:50px;
        text-align:right;
    }
    li{ list-style: none;}
    </style>
</head>
<body>
<?php $this->beginBody() ?>
     
    <!--左侧的菜单-->
    <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#" style='color:#fff;margin-left:0px;'>推星官活动平台</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
        <li>
            <?=
            Html::beginForm(['/manage/logout'], 'post');
            Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            );
            echo "<button style='margin-top:7px' class='btn btn-primary'>Admin 退出</button>";
            Html::endForm();
            ?>
        </li>
        </ul>
    </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
     <div class="col-sm-3 col-md-2 sidebar" id="menuv">
        <ul class="nav nav-sidebar">
          <li id="onemenu"><a href="/manage/index"       class='menu'>微信用户</a></li>
          <li id="twomenu"><a href="/subject/test?name="      class='menu'>真实科目管理</a></li>
          <li id="threemenu"><a href="/category/index"   class='menu'>学科管理</a></li>
          <li id="fourmenu"><a href="/tixing/index?name="      class='menu'>题型管理</a></li>
          <li id="fivemenu"><a href="/knownset/index"     class='menu'>知识点集合</a></li>
          <li id="sixmenu"><a href="/knowledge/index?categoryid=0&knownsetid=0"    class='menu'>知识点管理</a></li>
          <li id="sevenmenu"><a href="/tiku/index" class='menu'>习题管理</a></li>
          <li id="eightmenu"><a href="/tikumanage/index?cid=0&kid=0&lid=0"       class='menu'>习题审核</a></li>
          <li id="ninemenu"><a href="/megagame/index"    class='menu'>赛事管理</a></li>
          <li id="teenmenu"><a href="/megagroup/index?mid=0"    class='menu'>赛事分组</a></li>
        </ul>
     </div>

    <!--右侧的内容-->
    <?= $content ?>

   </div>
</div>

<script type="text/javascript">
//页面的初始化函数
$(function(){
   //移除其他的选中的样式
   $(".nav .nav-sidebar li").each(function(){
      $(this).removeClass("active"); 
   })
   //计算菜单的高度
   var winh=$(document).height()-50;
   $("#menuv").attr('style','height:'+winh+"px;");

})
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>