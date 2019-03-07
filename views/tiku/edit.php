<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Tiku;
use app\models\Tixing;

?>

<script type="text/javascript">
//上传验证

  $(function(){    
       
    //获取知识点点击
    InitKnownSet();  
  });
  
  //获取知识点点击
  function InitKnownSet(){
    var cid=window.location.search.split('&')[1].split('=')[1];
    var typeid=window.location.search.split('&')[2].split('=')[1];

    $.ajax({
      type:'post',
      url:'/tiku/knownset',
      data:{
        cid:cid
      },
      success:function(res){
        console.log("获取知识点下拉列表:");
        console.log(res);

        $("#kselect").html(res.data);
      }
    })
  } 

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
          <label class="control-label" style='line-height:34px;'>选择集合:</label>
          <select class="form-control" id="kselect" onchange="kchange()"></select>          
          <div class="help-block"></div>
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>选择知识点:</label>
          <div class="form-control" id="kchildv">
            <input type="checkbox" class='kchilditem'/>知识点A
            <input type="checkbox" class='kchilditem'/>知识点B
            <input type="checkbox" class='kchilditem'/>知识点C
            <input type="checkbox" class='kchilditem'/>知识点D
            <input type="checkbox" class='kchilditem'/>知识点E
            <input type="checkbox" class='kchilditem'/>知识点F
          </div>          
          <div class="help-block"></div>
      </div>  
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>已选知识点:</label>
          <div class="form-control" id="ckchildv">
            <input type="checkbox" class='ckchilditem'/>知识点A
            <input type="checkbox" class='ckchilditem'/>知识点B
          </div>          
          <div class="help-block"></div>
      </div>    
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>选择组合:</label>
          <div class='radiov'>
            <input type="radio" name="istype" value="1" checked>文字-文字
            <input type="radio" name="istype" value="0" style="margin-left:30px;">图片-文字
          </div>
          <div class="help-block"></div>
      </div>   
      <div class="form-group" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>题目:</label>
          <input type="text" id="title" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div> 
      <div class="form-group" style='display:flex;' id="uploadimg">
          <label class="control-label" style='line-height:34px;'>选择图片:</label>
          <?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
           <?=$form->field($model, 'imgpath')->widget('moxuandi\webuploader\SingleImage', [
            'config'=>[
              'fileNumLimit' => 100,
              'fileSizeLimit' => 30*1024*1024,
              'fileSingleSizeLimit' => 30*1024*1024]
           ]);?>          
          <?php ActiveForm::end();?>          
          <div class="help-block"></div>
      </div>  
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>答案A:</label>
          <input type="text" id="optionA" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div>  
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>答案B:</label>
          <input type="text" id="optionB" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>答案C:</label>
          <input type="text" id="optionC" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>答案D:</label>
          <input type="text" id="optionD" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>答案E:</label>
          <input type="text" id="optionE" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>答案F:</label>
          <input type="text" id="optionF" class="form-control" style='width:250px;margin-left:30px;' />
          <div class="help-block"></div>
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>正确答案:</label>
          <div class='radiov'>
            <input type="radio" name="option" value="1" checked>A
            <input type="radio" name="option" value="2" style="margin-left:30px;">B
            <input type="radio" name="option" value="3" style="margin-left:30px;">C
            <input type="radio" name="option" value="4" style="margin-left:30px;">D
            <input type="radio" name="option" value="5" style="margin-left:30px;">E
            <input type="radio" name="option" value="6" style="margin-left:30px;">F
          </div>
          <div class="help-block"></div>
      </div>
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>难易程度:</label>          
          <select class="form-control" id="difficult">
            <option value='1'>易</option>
            <option value='2'>中</option>
            <option value='3'>难</option>
          </select>
          <div class="help-block"></div>
      </div> 
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>习题解析:</label>          
          <textarea  id="marks" class="form-control" style='width:250px;height:100px;margin-left:30px;'></textarea>
          <div class="help-block"></div>
      </div>  
       <div class='bottombtnv' style='position: fixed;left: 65%;bottom: 30px;'>
          <button onclick="resetopt()"  class="btn btn-default" name="submit-button">取消</button> 
          <button onclick="saveopt()" class="btn btn-primary" name="submit-button">保存</button>             
        </div>
    </div>
</div>