<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
?>
<script type="text/javascript">
$(function(){
    $("#fivemenu").addClass("active");
})

//进入知识点集合列表页面
function editopt(id,name){
  window.document.location.href="/knownset/list?cid="+id+'&name='+name;
}

</script>



<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">知识点集合管理</h1>
  <div class="row placeholders">
  <?= GridView::widget([
            'dataProvider' => $provider,
            'id' => 'grid',
            'columns' => [
                  
              [
                'label'=>'序号',
                'value' => function ($model, $key, $index, $grid) { 
                  return $index+1; 
                }
              ],
                  [
                    'label'=>'名称',
                    'attribute'=>'name',
                  ],[
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'template' => ' {update}',//只需要展示删除{update}
                    'headerOptions' => ['width' => '240'],
                    'buttons' => [
                        "update"=>function ($url, $model, $key) {//print_r($key);exit;
                            return Html::a('进入管理', 'javascript:;', ['onclick'=>'editopt('.$model->id.',"'.$model->name.'")']);
                        },
                    ],
                ],
            ],
       ]) ?>
  </div>
</div
