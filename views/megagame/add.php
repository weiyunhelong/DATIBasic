<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Megagame;
use app\models\Megagroup;

?>

<script type="text/javascript">
//上传验证

  $(function(){    
    $("#ninemenu").addClass("active"); 
    
    $('input[name=isgroup]').change(function() {
        if (this.value == '1') {
           $("#groupv").show();
           $("#addbtn").show();
        }
        else  {
           $("#groupv").hide();
           $("#groupv").html('');
           $("#addbtn").hide();
        }
    });
  });
  

  //保存数据
  function saveopt(){
     //得到参数
     var mename=$("#mename").val();
     var isgroup=$("input[name='isgroup']:checked").val();
     var bishiname=$("#bishiname").val();
     var logo=$(".imgWrap img").attr("src");
     var rule=$("#rule").val();
     var islevel=$("input[name='islevel']:checked").val();

     if(mename==''){
        layer.msg('请输入大赛名称');
     }else if(bishiname==''){
        layer.msg('请输入笔试名称');
     }else if(rule==''){
        layer.msg('请填写规则');
     }else if(rule==''){
        layer.msg('请填写规则');
     }else if(islevel==undefined){
        layer.msg('请选择通过等级');
     }else{

     $.ajax({
       type:'post',
       url:'/megagame/save',
       data:{
          id:0,
          name:mename,
          isyear:isgroup,
          isanswer:$("input[name='showanswer']:checked").val(),
          showname:bishiname,
          logo:logo,
          rule:rule,
          level:islevel
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
  .control-label{
    line-height: 34px;
    text-align: right;
    width: 75px;
  }
  .radiov{
    margin-left: 30px;
    padding-top: 5px;
  }
</style>


<!--编辑内容部分-->
<div class="col-sm-9 col-md-4 col-md-4 main">
    <div id="w0">        
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>大赛名称:</label>
          <input type="text" id="mename" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div>     
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>是否分年级组:</label>
          <div class='radiov'>
            <input type="radio" name="isgroup" value="1" checked>是
            <input type="radio" name="isgroup" value="0" style="margin-left:30px;">否
            <button class='btn btn-primary' onclick='addgroup()' id='addbtn' style='display:none;'>添加组</button>
          </div>
          <div class="help-block"></div>
      </div>      
      <div class="form-group field-testpaper-tid" style='display:flex;' id="groupv">
      
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>是否显示答案:</label>
          <div class='radiov'>
            <input type="radio" name="showanswer" value="1" checked>是
            <input type="radio" name="showanswer" value="0" style="margin-left:30px;">否
          </div>
          <div class="help-block"></div>
      </div>   
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>笔试名称:</label>
          <input type="text" id="bishiname" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div>  
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>系统logo:</label>
          <?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
           <?=$form->field($model, 'logo')->widget('moxuandi\webuploader\SingleImage', [
            'config'=>[
              'fileNumLimit' => 100,
              'fileSizeLimit' => 30*1024*1024,
              'fileSingleSizeLimit' => 30*1024*1024]
           ]);?>          
          <?php ActiveForm::end();?>          
          <div class="help-block"></div>
      </div>
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>笔试规则:</label>
          <textarea  id="rule" class="form-control" style='width:250px;height:100px;margin-left:30px;'></textarea>
          <div class="help-block"></div>
      </div>
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>通过等级:</label>
          <div class='radiov'>
            <input type="radio" name="islevel" value="0" />优秀（120-150分）
            <input type="radio" name="islevel" value="1" style="margin-left:10px;">进阶（60-110分）
            <input type="radio" name="islevel" value="2" style="margin-left:10px;">入门（10-50分）
          </div>
          <div class="help-block"></div>
      </div>
       <div class='bottombtnv' style='margin: 30px 0px 30px 300px;display:flex;'>
          <button onclick="resetopt()"  class="btn btn-default" name="submit-button">取消</button> 
          <button onclick="saveopt()" class="btn btn-primary" name="submit-button">保存</button>             
        </div>
    </div>
</div>