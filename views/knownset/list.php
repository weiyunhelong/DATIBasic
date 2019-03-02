<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
?>
<script type="text/javascript">

$(function(){
    $("#fivemenu").addClass("active");
    var params=window.location.search;
    var names=params.split('&')[1];
    var name=params.split('=')[2];
    $("#subjecttxt").html(decodeURIComponent(name));
})

//返回操作
function Back(){
  window.history.go(-1);
}

//编辑页面
function editopt(id){
  var params=window.location.search;
  var names=params.split('&')[1];
  var cid=params.split('=')[1];
   //iframe窗
  layer.open({
    type: 2,
    title: '编辑知识点集合',
    shadeClose: true,
    shade: 0.8,
    area: ['380px', '300px'],
    content: '/knownset/edit?id='+id+'&cid='+cid //iframe的url
  });
}

//新增页面
function Add(){
  var params=window.location.search;
  var names=params.split('&')[1];
  var cid=params.split('=')[1];

  //iframe窗
  layer.open({
    type: 2,
    title: '添加知识点集合',
    shadeClose: true,
    shade: 0.8,
    area: ['380px', '300px'],
    content: '/knownset/add?id=0'+'&cid='+cid //iframe的url
  });
}

//批量删除
function Delete(){

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
           url:'/knownset/delete',
           data:{
            ids:ids+","
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
       url:'/knownset/delete',
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
  <h1 class="page-header">知识点集合管理</h1>
  <div class="topoptv">
    <div class='topleftv'>
     <button type="button" class="btn btn-default" onclick='Back()'>返回</button>
    </div>
    <div class='topcenterv' id='subjecttxt'>学科一</div>
    <div class='toprightv'>
     <button type="button" class="btn btn-success" onclick='Add()'>新建</button>
     <button type="button" class="btn btn-warning" onclick='Delete()'>批量删除</button>
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
                    'label'=>'名称',
                    'attribute'=>'name',
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
                    'template' => ' {update} {delete}',//只需要展示删除{update}
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
