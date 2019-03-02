<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
$this->title = '微信用户';
?>
<script type="text/javascript">
$(function(){
    $("#onemenu").addClass("active");
})
//编辑页面
function editopt(id,type){
   window.location.href="/manage/edit?id="+id+"&type="+type;
}
</script>

<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">微信用户</h1>
  <div class="row placeholders">
  <?= GridView::widget([
            'dataProvider' => $provider,
            'columns' => [
                  [
                    'class' => 'yii\grid\CheckboxColumn',
                  ],
                  [
                    'label'=>'序号',
                    'value' => function ($model, $key, $index, $grid) { 
                      return $index+1; 
                    }
                  ],
                  [
                    'label'=>'OPENID',
                    'attribute'=>'openid',
                  ],
                  [
                    'label'=>'微信昵称',
                    'attribute'=>'nickname',
                  ],
                  [
                    'label'=>'头像',
                    'attribute'=>'avatar',
                    'format' => [
                        'image', 
                         [
                           'width'=>'50',
                           'height'=>'50'
                         ]
                       ],
                        'value' => function ($model) { 
                           return $model->avatar; 
                       }
                  ],
                  [
                    'label'=>'性别',
                    'attribute'=>'gender',
                    'value' => function($model) {
                        if($model->gender==1){
                            return "男";
                        }else if($model->gender==2){
                            return "女";
                        }else{
                            return "未知";
                        }                    
                    },
                  ],
                  [
                    'label'=>'推行官用户',
                    'attribute'=>'topenid',                    
                    'value' => function($model) {
                      if($model->topenid!='-1'){
                          return "是";
                      }else{
                          return "否";
                      }                    
                  },
                  ],
                  [
                    'label'=>'创建时间',
                    'attribute' => 'create_at',
                    'value'=>function($m){
                       return date("Y-m-d H:i:s",$m->create_at);
                    }
                  ]
            ],
       ]) ?>
  </div>
</div
