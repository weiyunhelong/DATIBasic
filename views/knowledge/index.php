<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Category;
use app\models\Knownset;
use app\models\Knowledge;

/* @var $this yii\web\View */
?>
<script type="text/javascript">
$(function(){
    $("#sixmenu").addClass("active");

    //初始化学科下拉列表
    InitCategorySelect();
    //初始化知识点下拉列表
    InitKnownsetSelect();
})

//初始化学科下拉列表
function InitCategorySelect(){
    $.ajax({
       type:'get',
       url:'/knowledge/category',
       data:{
        id:window.location.search.split('&')[0].split('=')[1]
       },
       success:function(res){
        
        console.log("学科下拉列表:");
        console.log(res);

        $("#cselect").html(res.data);      
       },
     })  
}

//初始化知识点下拉列表
function InitKnownsetSelect(){
    $.ajax({
       type:'get',
       url:'/knowledge/knownset',
       data:{
        cid:window.location.search.split('&')[0].split('=')[1],
        id:window.location.search.split('&')[1].split('=')[1]
       },
       success:function(res){
        
        console.log("知识点下拉列表:");
        console.log(res);

        $("#kselect").html(res.data);      
       },
     }) 
}

//学科改变
function cselectopt(){

   $.ajax({
       type:'get',
       url:'/knowledge/knownset',
       data:{
        cid:$("#cselect").val(),
        id:0
       },
       success:function(res){
        
        console.log("知识点下拉列表:");
        console.log(res);

        $("#kselect").html(res.data);      
       },
     })   
}

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
           url:'/subject/delete',
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
       url:'/subject/delete',
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
    var cid=$("#cselect").val();
    var kid=$("#kselect").val();
    window.document.location.href="/knowledge/index?cid="+cid+'&kid='+kid;
}
</script>



<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">真实学科管理</h1>
  <div class="topoptv">
    <div class='topleftv'>
        <select id="cselect" class="form-control" onchange='cselectopt()' style='width:100px;'></select>
        <select id="kselect" class="form-control" style='width:100px;margin-left:10px;'></select>
        <button id="btn_search" type="button" class="searchbtn" style='height:34px;margin-left:10px;' onclick='Searchopt()'>查询</button>
    </div>
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
                    'label'=>'学科名称',
                    'attribute'=>'categoryid',
                    'value'=>function ($m) {
                        $model=Category::find()->where(['id'=>$m->categoryid])->one();
                        if(empty($model)){
                           return "已删除";
                        }else{
                            return $model->name;
                        }                        
                    }
                  ],                  
                  [
                    'label'=>'知识点集合名称',
                    'attribute'=>'knownsetid',
                    'value'=>function ($m) {
                        $model=Knownset::find()->where(['id'=>$m->knownsetid])->one();
                        if(empty($model)){
                           return "已删除";
                        }else{
                            return $model->name;
                        }                        
                    }
                  ],
                  [
                    'label'=>'知识点名称',
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
