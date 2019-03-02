<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Subject;
use app\models\Category;
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
       url:'/category/info',
       data:{
        id:$("#category-id").val()
       },
       success:function(res){
        
        console.log("详情信息:");
        console.log(res);

        $("#txt_name").val(res.name);  
        $("#subselect").html(res.select);        
       },
     })  
  }

  //保存数据
  function saveopt(){
     //得到参数
     var name=$("#txt_name").val();
     
     if(name==''){
         layer.msg('请输入名称');
     }else{

     $.ajax({
       type:'get',
       url:'/category/save',
       data:{
        id:$("#category-id").val(),
        name:name,
        subjectid:$("#subselect").val(),
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
          <label class="control-label" style='width:80px;text-align: center;line-height:34px;'>学科名称:</label>
          <input type="text" id="txt_name" class="form-control" style='width:220px;margin-left:10px;' />
          <div class="help-block"></div>
      </div>  
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='width:80px;text-align: center;line-height:34px;'>所属真实科目:</label>
          <select id="subselect" class="form-control" style='width:220px;margin-left:10px;'></select>
          <div class="help-block"></div>
      </div>             
       <div class='bottombtnv' style='position: fixed;left: 65%;bottom: 30px;'>
          <button onclick="resetopt()"  class="btn btn-default" name="submit-button">取消</button> 
          <button onclick="saveopt()" class="btn btn-primary" name="submit-button">保存</button>             
        </div>
    </div>
</div>