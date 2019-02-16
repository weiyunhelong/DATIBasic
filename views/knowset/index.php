
//在这里面输入任何合法的js语句
layer.open({
  type: 1 //Page层类型
  ,area: ['500px', '300px']
  ,title: '你好，layer。'
  ,shade: 0.6 //遮罩透明度
  ,maxmin: false //允许全屏最小化
  ,anim: 1 //0-6的动画形式，-1不开启
  ,content: '<div style="padding:50px;">这是一个非常普通的页面层，传入了自定义的html</div>'
});  