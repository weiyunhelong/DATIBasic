<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Tiku;
use app\models\Tixing;

?>

<script type="text/javascript">


  $(function(){           
    //获取知识点点击
    InitKnownSet(); 

    //获取题目的详情
    InitDetail(); 

    //获取已经选择知识点
    InitChkKnownSet(); 
  });
  //获取知识点点击
  function InitKnownSet(){
    var cid=window.location.search.split('&')[0].split('=')[1];

    $.ajax({
      type:'get',
      url:'/tikumanage/getks',
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
  //获取题目的详情
  function InitDetail(){
      var id=window.location.search.split('&')[0].split('=')[1];
     $.ajax({
       type:'get',
       url:'/tikumanage/info?id='+id,
       data:"",
       success:function(res){
          console.log("获取判断详情:");
          console.log(res); 

          //赋值部分
          $("#categoryid").val(res.data.categoryid);
          $("#tixingid").val(res.data.tixingid);
          $("#chkknownsetids").val(res.data.knownids);

          $("#title").val(res.data.title);
          $("#marks").val(res.data.mark);
          $("#difficult").val(res.data.difficult);
          if(res.data.answer==1){
            $("input[name='option'").eq(0).attr("checked",true);
          }else{
            $("input[name='option'").eq(1).attr("checked",true);
          }
          $("#title").val(res.data.title);
          $("#title").val(res.data.title);
          $("#title").val(res.data.title);

       }
     })
  }

  
  //获取已经选择知识点
  function InitChkKnownSet(){
    var cid=window.location.search.split('&')[0].split('=')[1];

    $.ajax({
      type:'get',
      url:'/tikumanage/knownledge',
      data:{
       id:cid
      },
      success:function(res){
        console.log("获取已经选择知识点:");
        console.log(res);

        $("#kchildv").html(res.data);
        $("#ckchildv").html(res.vdata);
      }
    })
  } 
  //选中知识点集合,获取知识点
  function kchange(){

    $.ajax({
      type:'get',
      url:'/tiku/knownledge',
      data:{
        kid:$("#kselect").val()
      },
      success:function(res){
        console.log("获取知识点列表:");
        console.log(res);       
        var html='';
        for(var i=0;i<res.data.length;i++){
          html+="<div class='kchilditemv'><input name='kchilditem' type='checkbox' class='kchilditem' data-id='"+res.data[i].id+"' data-name='"+res.data[i].name+"' onchange='chkknownset()' />"+res.data[i].name+"</div>";
        }
        $("#kchildv").html(html);
      }
    })
  }
  
  //知识点选中
  function chkknownset(){

    //知识点选择
    var chkknownsetids="";
    var chkknownsetname=[];

    var obj = document.getElementsByName("kchilditem");
    var check_val = [];
    for(k in obj){
      if(obj[k].checked){
        chkknownsetids+=obj[k].dataset.id+",";
        chkknownsetname.push(obj[k].dataset.name);
      }
    }
    var html="";
    for(var i=0;i<chkknownsetname.length;i++){
      html+="<div class='chkknownv'>"+chkknownsetname[i]+"</div>";
    }

    $("#chkknownsetids").val(chkknownsetids);
    $("#ckchildv").html(html);
  }
  

  //保存数据
  function saveopt(){
     //得到参数
     var kids=$("#chkknownsetids").val();//知识点
     var showtype="1";//题型
     var title=$("#title").val();//题目
     var imgpath="";//图
     var optionA="是";//选项A
     var optionB="否";//选项B
     var optionC="";//选项C
     var optionD="";//选项D
     var optionE="";//选项E
     var optionF="";//选项F
     var answer=$("input[name='option']:checked").val();//正确答案
     var difficult=$("#difficult").val();//难易程度
     var marks=$("#marks").val();//习题解析

     if(kids==''){
        layer.msg('请选择知识点');
     }else if(title==''){
        layer.msg('请输入题目');
     }else{

     $.ajax({
       type:'post',
       url:'/tiku/save',
       data:{   
        id:window.location.search.split('&')[0].split('=')[1],
        categoryid:$("#categoryid").val(),
        tixingid:$("#tixingid").val(),      
        knowsetid:$("#kselect").val(),
        knownids:kids,//知识点
        showtype:showtype,//题型
        title:title,//题目
        imgpath:"",//图
        optionA:"正确",//选项A
        optionB:"错误",//选项B
        optionC:"",//选项C
        optionD:"",//选项D
        optionE:"",//选项E
        optionF:"",//选项F
        answer:answer,//正确答案
        difficult:difficult,//难易程度
        mark:marks//习题解析
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
    width: 100px;
  }
  .radiov{
    margin-left: 30px;
    padding-top: 5px;
  }
  .form-control{
    width: 300px;
    margin-left: 30px;
  }
  .form-controlv{
    width: 300px;
    margin-left: 30px;
    border:none;
    display: flex;
  }
  #kchildv{
    display:flex;
    border:none;
    height:auto;
    flex-wrap:wrap;
  }
  .kchilditemv{
    margin-right:20px;
    display:flex;
  }
  #ckchildv{    
    display:flex;
    border:none;
    height:auto;
    flex-wrap:wrap;
  }
  .chkknownv{
    background: rgba(0, 153, 255, 1);
    color:#fff;
    padding:10px 20px;
    margin-right:20px;
    margin-bottom:10px;
  }
  .addbtn{
    width:130px;
    height:48px;
    margin-left: 28px;
  }
  .form-group {
    margin-bottom: 15px;
    display: flex;
  }
  .answerv{
    width:40px;
    display:none;
  }
</style>


<!--隐藏数据域-->
<input type='hidden' id="categoryid" />
<input type='hidden' id="tixingid" />
<input type='hidden' id="chkknownsetids" />
<?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
    <?=$form->field($model, 'id')->hiddenInput([])->label(false); ?>          
<?php ActiveForm::end();?>

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
          <div class="form-control" id="kchildv"></div>          
          <div class="help-block"></div>
      </div>  
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>已选知识点:</label>
          <div class="form-control" id="ckchildv"></div>          
          <div class="help-block"></div>
      </div>    
      <div class="form-group" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>题目:</label>
          <input type="text" id="title" class="form-control"  />
          <div class="help-block"></div>
      </div>       
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='line-height:34px;'>正确答案:</label>
          <div class='form-controlv'>
            <input type="radio" name="option" value="1" checked>正确
            <input type="radio" name="option" value="2" style="margin-left:10px;">错误           
          <div class="help-block"></div>
      </div>
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
          <textarea  id="marks" class="form-control" style='width:300px;height:100px;margin-left:30px;'></textarea>
          <div class="help-block"></div>
      </div>  
       <div class='bottombtnv' style='margin: 30px 0px 30px 300px;display:flex;'>
          <button onclick="resetopt()"  class="btn btn-default" name="submit-button">取消</button> 
          <button onclick="saveopt()" class="btn btn-primary" name="submit-button" style="margin-left:30px;">保存</button>             
        </div>
    </div>
</div>