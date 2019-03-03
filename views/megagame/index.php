<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
?>
<script type="text/javascript">
$(function(){
  $("#ninemenu").addClass("active");
})

//编辑页面
function editopt(id){
   //iframe窗
   window.document.location.href="/megagame/add?id="+id;

     //iframe窗
  layer.open({
    type: 2,
    title: '查看大赛',
    shadeClose: true,
    shade: 0.8,
    area: ['530px', '600px'],
    content: '/megagame/edit?id='+id//iframe的url
  });

}

//新增页面
function Add(){
  layer.open({
    type: 2,
    title: '新建大赛',
    shadeClose: true,
    shade: 0.8,
    area: ['530px', '600px'],
    content: '/megagame/add?id=0'//iframe的url
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
           url:'/megagame/delete',
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
       url:'/megagame/delete',
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



<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">赛事管理</h1>
  <div class="topoptv">
    <div class='topleftv'>
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
                    'label'=>'名称',
                    'attribute'=>'name',
                  ], 
                  [
                    'label'=>'笔试名称',
                    'attribute'=>'showname',
                  ],                    
                  [
                    'label'=>'赛事状态',
                    'attribute' => 'status',
                    'value'=>function ($m) {
                        if($m->status==-1){
                          return "已删除";
                        } else if($m->status==0){
                          return "未开始";
                        }else if($m->status==1){
                          return "上线";
                        }else {
                          return "已结束";
                        }
                    }
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
</div>
