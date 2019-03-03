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
    $("#eightmenu").addClass("active");

    //初始化学科下拉列表
    InitCategorySelect();
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

//编辑页面
function editopt(id){
   //iframe窗
  layer.open({
    type: 2,
    title: '编辑题目',
    shadeClose: true,
    shade: 0.8,
    area: ['380px', '300px'],
    content: '/tiku/edit?id='+id //iframe的url
  });
}

//新增页面
function Add(){
   window.document.location.href="/tiku/tixing";
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
           url:'/tiku/delete',
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
       url:'/tiku/delete',
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
    window.document.location.href="/tiku/index?cid="+cid;}
</script>


<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">题库审核</h1>
  <div class="topoptv">
    <div class='topleftv'>
        <select id="cselect" class="form-control" onchange='cselectopt()' style='width:100px;'></select>
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
