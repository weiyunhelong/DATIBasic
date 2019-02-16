<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
?>
<script type="text/javascript">
$(function(){
    $("#twomenu").addClass("active");
})
//编辑页面
function editopt(id){
   //iframe窗
  layer.open({
    type: 2,
    title: '新建学科',
    shadeClose: true,
    shade: 0.8,
    area: ['380px', '300px'],
    content: '/subject/edit?id='+id //iframe的url
  });
}

//新增页面
function Add(){
  //iframe窗
  layer.open({
    type: 2,
    title: '新建学科',
    shadeClose: true,
    shade: 0.8,
    area: ['380px', '300px'],
    content: '/subject/edit?id=0' //iframe的url
  });
}

//批量删除
function DeleteAll(){

}

//单个删除
function deleteopt(){
    
}
</script>

<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">真实学科管理</h1>
  <div class="topoptv">
    <div class='topleftv'></div>
    <div class='toprightv'>
     <button type="button" class="btn btn-success" onclick='Add()'>新建</button>
     <button type="button" class='btn btn-warning' onclick='DeleteAll()'>批量删除</button>
    </div>   
  </div>
  <div class="row placeholders">
  <?= GridView::widget([
            'dataProvider' => $provider,
            'columns' => [
                  [
                    'class' => 'yii\grid\CheckboxColumn',
                  ],
                  [
                    'label'=>'ID',
                    'attribute'=>'id',
                  ],
                  [
                    'label'=>'名称',
                    'attribute'=>'name',
                  ],                  
                  [
                    'label'=>'创建时间',
                    'attribute' => 'create_at',
                    'value'=>function($m){
                       return date("Y-m-d H:i:s",$m->create_at);
                    }
                  ],[
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作', 
                    'template' => ' {update}',//只需要展示删除{update}
                    'headerOptions' => ['width' => '240'],
                    'buttons' => [
                        "update"=>function ($url, $model, $key) {//print_r($key);exit;
                            return Html::a('修改', 'javascript:;', ['onclick'=>'editopt('.$model->id.',"'.$model->name.'")']);                                                   
                        }, 
                        'delete' => function ($url, $model, $key) {
                            return Html::a('删除', 'javascript:;', ['onclick'=>'deleteopt('.$model->id.')']);
                        },                          
                    ],
                ],
            ],
       ]) ?>
  </div>
</div
