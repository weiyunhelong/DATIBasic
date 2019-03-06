<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Subject;
use app\models\Category;

?>


<style>
  body{
    padding-top: 0px;
  }
</style>
<link href="/css/shu.css"  rel="stylesheet" />

<!--隐藏数据域-->
<?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>
    <?=$form->field($model, 'id')->hiddenInput([])->label(false); ?>          
<?php ActiveForm::end();?>

<!--编辑内容部分-->
<div class="col-sm-9 col-md-4 col-md-4 main">
    <div id="w0"> 
    <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='width:80px;text-align: center;line-height:34px;'>大赛名称:</label>
          <select id="mselect" class="form-control" style='width:220px;margin-left:10px;'></select>
          <div class="help-block"></div>
      </div>          
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='width:80px;text-align: center;line-height:34px;'>分组名称:</label>
          <input type="text" id="groupname" class="form-control" style='width:220px;margin-left:10px;' />
          <div class="help-block"></div>
      </div>         
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='width:80px;text-align: center;line-height:34px;'>推行官分组:</label>
          <input type="number" id="tid" class="form-control" style='width:220px;margin-left:10px;' />
          <div class="help-block"></div>
      </div>    
      <div class="form-group field-testpaper-tid" style='display:flex;'>
          <label class="control-label" style='width:80px;text-align: center;line-height:34px;'>知识点:</label>
          <div class="zTreeDemoBackground ztree">
	      	<ul id="treeDemo" class="ztree"></ul>
          </div>
          <div class="help-block"></div>
      </div>         
       <div class='bottombtnv' style='position: fixed;left: 65%;bottom: 30px;'>
          <button onclick="resetopt()"  class="btn btn-default" name="submit-button">取消</button> 
          <button onclick="saveopt()" class="btn btn-primary" name="submit-button">保存</button>             
        </div>
    </div>
</div>

</body>
<script type="text/javascript">
  $(function(){
     //根据id，获取到详情
     InitDetail();
  })

  //根据id，获取到详情
  function InitDetail(){
    $.ajax({
       type:'get',
       url:'/megagroup/info',
       data:{
        id:$("#megagroup-id").val()
       },
       success:function(res){
        
        console.log("详情信息:");
        console.log(res);
        $("#groupname").val(res.data.name); 
        $("#tid").val(res.data.tid);  
        $("#mselect").html(res.phtml);  
        
        
        var setting = {
          async: {
          enable: true,
          url:"/megagroup/knowledge?ids="+res.data.knownids, 
          type: "get"
        },
        check: {
　　　   　enable: true,   //true / false 分别表示 显示 / 不显示 复选框或单选框
　　　   　autoCheckTrigger: true,   //true / false 分别表示 触发 / 不触发 事件回调函数
　　　　   chkStyle: "checkbox",   //勾选框类型(checkbox 或 radio）
　　　　   chkboxType: { "Y": "p", "N": "s" }   //勾选 checkbox 对于父子节点的关联关系
        },
        callback: {
          beforeAsync: beforeAsync,
          onAsyncSuccess: onAsyncSuccess,
          onAsyncError: onAsyncError
         }
        };   
        //初始化树形结构
        $.fn.zTree.init($("#treeDemo"), setting);
          $("#expandAllBtn").bind("click", expandAll);
          $("#asyncAllBtn").bind("click", asyncAll);
          $("#resetBtn").bind("click", reset);

       },
     })  
  }

  //保存数据
  function saveopt(){
     //得到参数
     var name=$("#groupname").val();
     var mid=$("#mselect").val();
     var tid=$("#tid").val();
     
     var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
     var nodes = treeObj.getCheckedNodes(true);
     var kids="";
     for(var i=0;i<nodes.length;i++){
      if(!nodes[i].isParent){
        kids+=nodes[i].id+",";
      }      
     }
     if(name==''){
        layer.msg('请输入名称');
     }else if(mid==0){
        layer.msg('请选择大赛');
     }else if(tid=='0'){
        layer.msg('请输入推行官分组');
     }else if(kids==""){
        layer.msg('选择知识点'); 
     }else{

     $.ajax({
       type:'post',
       url:'/megagroup/save',
       data:{
        id:$("#megagroup-id").val(),
        name:name,
        mid:mid,
        tid:tid,
        kids:kids
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

<!--树形结构-->

<!--初始化树形结构-->
<script type="text/javascript">
	
  var demoMsg = {
    async:"正在进行异步加载，请等一会儿再点击...",
    expandAllOver: "全部展开完毕",
    asyncAllOver: "后台异步加载完毕",
    asyncAll: "已经异步加载完毕，不再重新加载",
    expandAll: "已经异步加载完毕，使用 expandAll 方法"
  }


  function filter(treeId, parentNode, childNodes) {
    if (!childNodes) return null;
    for (var i=0, l=childNodes.length; i<l; i++) {
      childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
    }
    return childNodes;
  }

  function beforeAsync() {
    curAsyncCount++;
  }
  
  function onAsyncSuccess(event, treeId, treeNode, msg) {
    curAsyncCount--;
    if (curStatus == "expand") {
      expandNodes(treeNode.children);
    } else if (curStatus == "async") {
      asyncNodes(treeNode.children);
    }

    if (curAsyncCount <= 0) {
      if (curStatus != "init" && curStatus != "") {
        $("#demoMsg").text((curStatus == "expand") ? demoMsg.expandAllOver : demoMsg.asyncAllOver);
        asyncForAll = true;
      }
      curStatus = "";
    }
  }

  function onAsyncError(event, treeId, treeNode, XMLHttpRequest, textStatus, errorThrown) {
    curAsyncCount--;

    if (curAsyncCount <= 0) {
      curStatus = "";
      if (treeNode!=null) asyncForAll = true;
    }
  }

  var curStatus = "init", curAsyncCount = 0, asyncForAll = false,
  goAsync = false;
  function expandAll() {
    if (!check()) {
      return;
    }
    var zTree = $.fn.zTree.getZTreeObj("treeDemo");
    if (asyncForAll) {
      $("#demoMsg").text(demoMsg.expandAll);
      zTree.expandAll(true);
    } else {
      expandNodes(zTree.getNodes());
      if (!goAsync) {
        $("#demoMsg").text(demoMsg.expandAll);
        curStatus = "";
      }
    }
  }
  function expandNodes(nodes) {
    if (!nodes) return;
    curStatus = "expand";
    var zTree = $.fn.zTree.getZTreeObj("treeDemo");
    for (var i=0, l=nodes.length; i<l; i++) {
      zTree.expandNode(nodes[i], true, false, false);
      if (nodes[i].isParent && nodes[i].zAsync) {
        expandNodes(nodes[i].children);
      } else {
        goAsync = true;
      }
    }
  }

  function asyncAll() {
    if (!check()) {
      return;
    }
    var zTree = $.fn.zTree.getZTreeObj("treeDemo");
    if (asyncForAll) {
      $("#demoMsg").text(demoMsg.asyncAll);
    } else {
      asyncNodes(zTree.getNodes());
      if (!goAsync) {
        $("#demoMsg").text(demoMsg.asyncAll);
        curStatus = "";
      }
    }
  }
  function asyncNodes(nodes) {
    if (!nodes) return;
    curStatus = "async";
    var zTree = $.fn.zTree.getZTreeObj("treeDemo");
    for (var i=0, l=nodes.length; i<l; i++) {
      if (nodes[i].isParent && nodes[i].zAsync) {
        asyncNodes(nodes[i].children);
      } else {
        goAsync = true;
        zTree.reAsyncChildNodes(nodes[i], "refresh", true);
      }
    }
  }

  function reset() {
    if (!check()) {
      return;
    }
    asyncForAll = false;
    goAsync = false;
    $("#demoMsg").text("");
    $.fn.zTree.init($("#treeDemo"), setting);
  }

  function check() {
    if (curAsyncCount > 0) {
      $("#demoMsg").text(demoMsg.async);
      return false;
    }
    return true;
  }

  $(document).ready(function(){
   
  });
  
</script>
