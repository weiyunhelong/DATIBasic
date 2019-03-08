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
    $("#sevenmenu").addClass("active");
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

  <div class="row placeholders">
  <?= GridView::widget([
            'dataProvider' => $provider,
            'id' => 'grid',
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                  ],
                  [
                    'label'=>'ID',
                    'attribute'=>'id',
                  ],
                  [
                    'label'=>'题目',
                    'attribute'=>'title',
                  ],
                  [
                    'label'=>'答案A',
                    'attribute'=>'optionA',
                  ],
                  [
                    'label'=>'答案B',
                    'attribute'=>'optionB',
                  ],
                  [
                    'label'=>'答案C',
                    'attribute'=>'optionC',
                  ],
                  [
                    'label'=>'答案D',
                    'attribute'=>'optionD',
                  ],
                  [
                    'label'=>'正确答案',
                    'attribute'=>'answer',
                    'value'=>function ($m) {
                        if ($m->answer==1) {
                            return "A";
                        } elseif ($m->answer==2) {
                            return "B";
                        } elseif ($m->answer==3) {
                            return "C";
                        } elseif ($m->answer==4) {
                            return "D";
                        } elseif ($m->answer==5) {
                            return "E";
                        } elseif ($m->answer==6) {
                            return "F";
                        }
                    }
                  ],
                  [
                    'label'=>'难易程度',
                    'attribute' => 'difficult',
                    'value'=>function ($m) {
                        if ($m->difficult==1) {
                            return "易";
                        } elseif ($m->difficult==2) {
                            return "中";
                        } elseif ($m->difficult==3) {
                            return "难";
                        }
                    }
                  ],[
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'template' => ' {update} {delete}',//只需要展示删除{update}
                    'headerOptions' => ['width' => '100'],
                    'buttons' => [
                        "update"=>function ($url, $model, $key) {//print_r($key);exit;
                            return Html::a('修改', 'javascript:;', ['onclick'=>'editopt('.$model->id.',"'.$model->title.'")']);
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('删除', 'javascript:;', ['onclick'=>'deleteopt('.$model->id.')']);
                        },
                    ],
                ],
            ],
       ]) ?>
  </div>

