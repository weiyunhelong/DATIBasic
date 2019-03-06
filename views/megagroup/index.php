<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Megagame;
use app\models\Megagroup;
/* @var $this yii\web\View */
?>
<script type="text/javascript">
$(function(){
    $("#teenmenu").addClass("active");
    //初始化大赛列表
    InitMagagroup();
})

//初始化大赛列表
function InitMagagroup(){

  var params=window.location.search;
  var mid=params.split('&')[0].split('=')[1];

  $.ajax({
    type:'get',
    url:'/megagroup/megagame',
    data:{
      mid:mid
    },
    success:function(res){
      console.log(res);
      $("#mselect").html(res.data);
    },
   })  
}

//编辑页面
function editopt(id){
   //iframe窗
  layer.open({
    type: 2,
    title: '编辑赛事分组',
    shadeClose: true,
    shade: 0.8,
    area: ['380px', '500px'],
    content: '/megagroup/edit?id='+id //iframe的url
  });
}

//新增页面
function Add(){
  //iframe窗
  layer.open({
    type: 2,
    title: '新建赛事分组',
    shadeClose: true,
    shade: 0.8,
    area: ['380px', '500px'],
    content: '/megagroup/edit?id=0' //iframe的url
  });
}

//批量删除
function DeleteAll(){

//获取选中的id
var ids = $("#grid").yiiGridView("getSelectedRows");
console.log("Ids:"+ids);
if(ids==""){
    layer.msg("请至少选择一项");
}else{   

    //询问框
    layer.confirm('确定要删除嘛?', {
      btn: ['确定','取消'] //按钮
    }, function(){

       $.ajax({
         type:'post',
         url:'/megagroup/delete',
         data:{
          ids:ids+","
         },
         success:function(res){
      
        if(res.status=='success'){
          layer.msg("删除成功！");
          window.document.location.reload();
        }else{
          layer.msg("删除失败！");
        }
        layer.closeAll();
     },
   })  
}, function(){
layer.closeAll();
});
  
}
}

//单个删除
function deleteopt(id){

  $.ajax({
     type:'post',
     url:'/megagroup/delete',
     data:{
      ids:id+","
     },
     success:function(res){
      
      console.log("删除的结果:");
      console.log(res);
      
      if(res.status=='success'){
          layer.msg("删除成功！");
          window.document.location.reload();
      }else{
          layer.msg("删除失败！");
      }
     },
   })  
}

//搜索操作
function Searchopt(){
  var mid=$("#mselect").val();
  window.document.location.href="/megagroup/index?mid="+mid;
}
</script>

<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">赛事分组</h1>
  <div class="topoptv">
    <div class='topleftv'>
       <select id="mselect" class="form-control" style='width:150px;'></select>
       <button id="btn_search" type="button" class="searchbtn" style='height:34px;margin-left:10px;' onclick='Searchopt()'>查询</button>    
    </div>
    <div class='toprightv'>
     <button type="button" class="btn btn-success" onclick='Add()'>新建</button>
     <button type="button" class='btn btn-warning' onclick='DeleteAll()'>批量删除</button>
    </div>   
  </div>
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
                    'label'=>'大赛名称',
                    'attribute'=>'mid',
                    'value'=>function ($m) {
                        $item=Megagame::find()->where(['id'=>$m->mid])->one();
                        if (empty($item)) {
                            return "已删除";
                        } else {
                            return $item->name;
                        }
                    }
                  ],                  
                  [
                    'label'=>'分组名称',
                    'attribute'=>'name',
                  ],                  
                  [
                    'label'=>'推行官分组',
                    'attribute'=>'tid',
                  ],
                  [
                    'label'=>'创建时间',
                    'attribute' => 'create_at',
                    'value'=>function ($m) {
                        return date("Y-m-d H:i:s", $m->create_at);
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
