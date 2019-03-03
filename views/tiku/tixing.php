<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Tixing;
use app\models\Category;

/* @var $this yii\web\View */
?>
<script type="text/javascript">
$(function(){
    $("#eightmenu").addClass("active");
})

//创建题目页面
function editopt(id){
  
  //iframe窗
  layer.open({
    type: 2,
    title: '添加题目',
    shadeClose: true,
    shade: 0.8,
    area: ['550px', '600px'],
    content: "/tiku/edit?id=0&cid="+id//iframe的url
  });
}

//返回操作
function Back(){
  window.history.go(-1);
}
</script>
<style>
.topleftv{
  width: 30%;
  height: 50px;
  text-align: left;
  display: flex;
}
.btn-default{
  height: 32px;
  width: 50px;
}
.topcenterv{
  width: 30%;
  text-align: center;
  font-size: 32px;
  line-height: 20px;
  font-weight:700;
}
</style>


<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">习题管理</h1>  
  <div class="topoptv">
    <div class='topleftv'>
     <button type="button" class="btn btn-default" onclick='Back()'>返回</button>
    </div>
    <div class='topcenterv' id='subjecttxt'></div>
    <div class='toprightv'>
    </div>   
  </div>
  <div class="row placeholders">
  <?= GridView::widget([
            'dataProvider' => $provider,
            'id' => 'grid',
            'columns' => [
                  [
                    'label'=>'ID',
                    'attribute'=>'id',
                  ],
                  [
                    'label'=>'名称',
                    'attribute'=>'name'
                  ],    
                  [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'template' => ' {update}',//只需要展示删除{update}
                    'headerOptions' => ['width' => '240'],
                    'buttons' => [
                        "update"=>function ($url, $model, $key) {//print_r($key);exit;
                            return Html::a('添加题目', 'javascript:;', ['onclick'=>'editopt('.$model->id.')']);
                        }
                  ],
                ],
            ],
       ]) ?>
  </div>
</div
