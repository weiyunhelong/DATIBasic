<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Knownset;

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
       url:'/Knownset/info',
       data:{
        id:$("#knownset-id").val()
       },
       success:function(res){
        
        console.log("详情信息:");
        console.log(res);

        $("#txt_name").val(res.data.name);
        if(res.data.isdifficult==0){
           $('input:radio:first').attr('checked', 'checked');
        } else{
           $('input:radio:last').attr('checked', 'checked');
        }      
       },
     })  
  }

  //保存数据
  function saveopt(){
     //得到参数
     var name=$("#txt_name").val();
     var cid=window.location.search.split('&')[1].split('=')[1];

     if(name==''){
        layer.msg('请输入名称');
     }else{

     $.ajax({
       type:'get',
       url:'/knownset/save',
       data:{
        id:$("#knowmset-id").val(),
        categoryid:cid,
        name:name,
        isdifficult: $("input[name='difficult']:checked").val()
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
  .radiov{
    width: 250px;
    margin-left: 5px;
  }
  .radiov radio{ 
    margin-top: 10px;
  }
</style>

<!--隐藏数据域-->
<?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
    <?=$form->field($model, 'id')->hiddenInput([])->label(false); ?> 
    <?=$form->field($model, 'categoryid')->hiddenInput([])->label(false); ?>         
<?php ActiveForm::end();?>
<!--编辑内容部分-->
<div class="col-sm-9 col-md-4 col-md-4 main">
    <div id="w0">        
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>集合名称:</label>
          <input type="text" id="txt_name" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div>      
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>是否有难易度:</label>
          <div class='radiov'>
            <input type="radio" name="difficult" value="0" checked>是
            <input type="radio" name="difficult" value="1" style="margin-left:30px;">否
          </div>
          <div class="help-block"></div>
      </div>          
       <div class='bottombtnv' style='position: fixed;left: 65%;bottom: 30px;'>
          <button onclick="resetopt()"  class="btn btn-default" name="submit-button">取消</button> 
          <button onclick="saveopt()" class="btn btn-primary" name="submit-button">保存</button>             
        </div>
    </div>
</div>