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

    //获取学科的下拉列表
    InitCategory();
    //获取集合的下拉列表
    InitKnowset();
    //获取知识点的下拉列表
    InitKnowledge();
})

//获取学科的下拉列表
function  InitCategory(){
  var params=window.location.search;
  var names=params.split('&')[0];
  var cid=params.split('=')[1];

   $.ajax({
    type:'get',
    url:'/tikumanage/category',
    data:{
      cid:cid
    },
    success:function(res){
      console.log("学科列表:");
      console.log(res);
      
      $("#cselect").html(res.data);
    }
  })
}
//获取集合的下拉列表
function InitKnowset(){
  var params=window.location.search;
  var cnames=params.split('&')[0];
  var cid=cnames.split('=')[1];
  var knames=params.split('&')[1];
  var kid=knames.split('=')[1];

   $.ajax({
    type:'get',
    url:'/tikumanage/knownsetselect',
    data:{
      cid:cid,
      kid:kid
    },
    success:function(res){
      console.log("集合列表:");
      console.log(res);
      
      $("#jselect").html(res.data);

      InitKnowledge();
    }
  })
}

//获取知识点的下拉列表
function InitKnowledge(){
  var params=window.location.search;
  var cnames=params.split('&')[0];
  var cid=cnames.split('=')[1];
  var knames=params.split('&')[1];
  var kid=knames.split('=')[1];
  var lnames=params.split('&')[2];
  var lid=lnames.split('=')[1];
  
   $.ajax({
    type:'get',
    url:'/tikumanage/knowledgeselect',
    data:{
      cid:cid,
      kid:kid,
      lid:lid
    },
    success:function(res){
      console.log("知识点列表:");
      console.log(res);
      
      $("#zselect").html(res.data);
    }
  })
}
//学科改变
function categorychange(){

  var cid=$("#cselect").val();
  var kid=0;

   $.ajax({
    type:'get',
    url:'/tikumanage/knownsetselect',
    data:{
      cid:cid,
      kid:kid
    },
    success:function(res){
      console.log("集合列表:");
      console.log(res);
      
      $("#jselect").html(res.data);

      jihechange();
    }
  })
}
//集合改变
function jihechange(){

  var cid=$("#cselect").val();
  var kid=$("#jselect").val();
  var lid=0;
  
   $.ajax({
    type:'get',
    url:'/tikumanage/knowledgeselect',
    data:{
      cid:cid,
      kid:kid,
      lid:lid
    },
    success:function(res){
      console.log("知识点列表:");
      console.log(res);
      
      $("#zselect").html(res.data);
    }
  })
}
//搜索数据
function Searchopt(){
  var cid=$("#cselect").val();
  var kid=$("#jselect").val();
  var lid=$("#zselect").val();
  window.document.location.href="/tikumanage/index?cid="+cid+"&kid="+kid+"&lid="+lid;
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
         url:'/tikumanage/delete',
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

//编辑
function editopt(id,type){
 //iframe窗
 var path="";
 if(type==1){
    path='/tikumanage/danxuan?id='+id;
 }else{
    path='/tikumanage/panduan?id='+id;
 }
 console.log("跳转链接:"+path);
 layer.open({
    type: 2,
    title: '修改习题',
    shadeClose: true,
    shade: 0.8,
    area: ['500px', '600px'],
    content: path //iframe的url
  });
}

//删除
function deleteopt(id){

$.ajax({
   type:'post',
   url:'/tikumanage/delete',
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

<style type="text/css">
.topleftv{
  width: 100%;
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
  <h1 class="page-header">习题审核</h1>
  <div class="topoptv">
    <div class='topleftv'>
      <select id="cselect" class="form-control" style='width:150px;' onchange="categorychange()"></select>
      <select id="jselect" class="form-control" style='width:150px;margin-left:10px;' onchange="jihechange()"></select>
      <select id="zselect" class="form-control" style='width:150px;margin-left:10px;'></select>
      <button id="btn_search" type="button" class="searchbtn" style='height:34px;margin-left:10px;' onclick='Searchopt()'>查询</button>
   </div>
    <div class='toprightv'>
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
                    'label'=>'序号',
                    'value' => function ($model, $key, $index, $grid) { 
                      return $index+1; 
                    }
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
                            return Html::a('修改', 'javascript:;', ['onclick'=>'editopt('.$model->id.','.$model->tixingid.')']);
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('删除', 'javascript:;', ['onclick'=>'deleteopt('.$model->id.')']);
                        },
                    ],
                ],
            ],
       ]) ?>
  </div>
</div>

