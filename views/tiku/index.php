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
       url:'/tiku/subject',
       data:{},
       success:function(res){
        
        console.log("学科下拉列表:");
        console.log(res);
        $("#cselect").html(res.data);

        var setting = {
          async: {
          enable: true,
          url:"/tiku/knownset?cid="+$('#cselect').val(), 
          type: "get"
        },
        callback: {
          onClick:ztreeOnClick,
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
 //树形结构的点击事件       
 function ztreeOnClick(event, treeId, treeNode){
   if (treeNode.id == "1") {
      return;
   }else{
    $("#kid").val(treeNode.pId);
    $("#ckid").val(treeNode.id);
    var path="/tiku/list?cid="+$('#cselect').val()+'&kid='+treeNode.pId+'&ckid='+treeNode.id;
    
    $("#datalist").attr('src',path);
   }
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
  window.document.location.href="/tiku/tixing?cid="+$("#cselect").val();
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

    //默认点击第一个子节点
    var roletree = $.fn.zTree.getZTreeObj("treeDemo");
    var node = roletree.getNodes()[0];
    console.log("第一个节点:");
    console.log(node);
    $("#kid").val(node.id);
    $("#ckid").val(node.children[0].id);
    var path="/tiku/list?cid="+$('#cselect').val()+'&kid='+node.id+'&ckid='+node.children[0].id;
    
    $("#datalist").attr('src',path);
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

<link href="/css/shu.css"  rel="stylesheet" />

<input type="text" id="kid" style='display:none;' value=''/>
<input type="text" id="ckid" style='display:none;' value=''/>

<div class="col-sm-9 main" style='width:83%;'>
  <h1 class="page-header">题库管理</h1>
  <div class="topoptv">
    <div class='topleftv'>
    </div>
    <div class='toprightv'>
      <button type="button" class="btn btn-success" onclick='Add()'>新增</button>
      <button type="button" class="btn btn-warning" onclick='Delete()'>批量删除</button>
    </div>   
  </div>
  <div class="row placeholders">
   <div class="col-sm-3 ztreev">
     <select name="" id="cselect" class='form-control'></select>
     <div class="zTreeDemoBackground ztree">
	  	<ul id="treeDemo" class="ztree"></ul>
   	</div>
   </div> 
   <div class="col-sm-9" style='padding-right: 0;'>
     <iframe src="" frameborder="0" id="datalist" style='width:100%;height:80vh;'></iframe>
   </div>  
  </div>
</div
