<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;
use app\models\Knownset;
use app\models\Knowledge;
?>

<script type="text/javascript">
  $(function(){
     //根据id，获取到详情
     InitDetail();
  })

  //根据id，获取到详情
  function InitDetail(){
    $.ajax({
       type:'get',
       url:'/knowledge/info',
       data:{
        id:$("#knowledge-id").val()
       },
       success:function(res){
        
        console.log("详情信息:");
        console.log(res);

        $("#txt_name").val(res.data.name);    
        $("#cselect").html(res.chtml);    
        $("#kselect").html(res.khtml);      
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

  //保存数据
  function saveopt(){
     //得到参数
     var name=$("#txt_name").val();
     
     if(name==''){
         layer.msg('请输入名称');
     }else if($("#cselect").val()==0){
         layer.msg('请选择学科');
     }else if($("#kselect").val()==0){
         layer.msg('请选择知识点集合');
     }else{

     $.ajax({
       type:'get',
       url:'/knowledge/save',
       data:{
        id:$("#knowledge-id").val(),
        name:name,
        categoryid:$("#cselect").val(),
        knownsetid:$("#kselect").val()
       },
       success:function(res){
         if(res.status=='success'){          
           parent.window.document.location.reload();
           parent.layer.closeAll();
         }else{
           layer.msg("保存失败");
         }        
       },
     })   
    }
  }

  //重置数据
  function resetopt(){     
    parent.layer.closeAll();
  }
</script>

<style>
  body{
    padding-top: 0px;
  }
</style>

<!--隐藏数据域-->
<?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
    <?=$form->field($model,'id')->hiddenInput([])->label(false); ?>          
<?php ActiveForm::end();?>
<!--编辑内容部分-->
<div class="col-sm-9 col-md-4 col-md-4 main">
    <div id="w0"> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;width: 63px;'>学科名称:</label>
          <select type="text" id="cselect" class="form-control" style='width:250px;margin-left:30px;' onchange='cselectopt()'></select>
          <div class="help-block"></div>
      </div>  
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;width: 63px;'>知识点集合:</label>
          <select type="text" id="kselect" class="form-control" style='width:250px;margin-left:30px;'></select>
          
          <div class="help-block"></div>
      </div>   
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;width: 63px;'>知识点名称:</label>
          <input type="text" id="txt_name" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div>           
       <div class='bottombtnv' style='position: fixed;left: 65%;bottom: 30px;'>
          <button onclick="resetopt()"  class="btn btn-default" name="submit-button">取消</button> 
          <button onclick="saveopt()" class="btn btn-primary" name="submit-button">保存</button>             
        </div>
    </div>
</div>