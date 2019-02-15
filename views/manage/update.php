<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;
use app\models\Subject;

$this->title = '答题项目';
$arr = ['A', 'B', 'C', 'D'];
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><?= $this->title; ?></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
        </ul>
    </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
    <div class="col-sm-3 col-md-2 sidebar">
      <ul class="nav nav-sidebar">
        <li class="">
          <a href="<?= Url::to(['category/index']); ?>">分类管理</a>
        </li>
        <li class="">
          <a href="<?= Url::to(['subject/index']); ?>">主题管理</a>
        </li>
        <li class="active">
          <a href="<?= Url::to(['manage/index']); ?>">试卷管理</a>
        </li>
        <li class="">
          <a href="<?= Url::to(['manage/records']); ?>">答题记录</a>
        </li>
      </ul>
    </div>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h3 class="page-header">试卷管理</h3>
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($paper, 'cate')->dropDownList(
                  Category::find()->select(['name','id'])->indexBy('id')->column(),
                  [
                    'onchange'=>'
                       $.post("/manage/getsubject?id='.'"+$(this).val(),function(data){                   
                           $("select#paper-type").html(data);
                      });',
                  ]
                ); ?>
                <?= $form->field($paper, 'type')->dropDownList(
                  Subject::find()->where(["cid"=>1])->select(['name','id'])->indexBy('id')->column(),
                  [
                  ]
                ); ?>
                <?= $form->field($paper, 'status')->dropDownList(['无效', '有效']); ?>

                <div class="form-group">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
        <h3 class="page-header">
            题目管理
            <?php if(!$paper->isNewRecord): ?>
            <a href="<?= Url::to(['/manage/updatequestion', 'pid'=>$paper->id]); ?>" title="添加新题目" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-plus"></span></a>            
            <?php endif; ?>
        </h3>
        <div class="col-md-12">
            <?php foreach($questions as $k => $question): ?>
                <h3>
                    <?= $k+1 .'、'. $question->title; ?>
                    <a href="<?= Url::to(['/manage/updatequestion', 'qid'=>$question->id]); ?>" title="编辑题目" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                </h3>
                <?php foreach($question->options as $k2 => $option){
                    echo '<p>' .$arr[$k2] .'，'. $option->title. '</p>';
                } ?>
            <?php endforeach; ?>
        </div>
    </div>
    </div>
</div>


<style>
        /*
    * Base structure
    */

    /* Move down content because we have a fixed navbar that is 50px tall */
    body {
    padding-top: 50px;
    }


    /*
    * Global add-ons
    */

    .sub-header {
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    }

    /*
    * Top navigation
    * Hide default border to remove 1px line.
    */
    .navbar-fixed-top {
    border: 0;
    }

    /*
    * Sidebar
    */

    /* Hide for mobile, show later */
    .sidebar {
    display: none;
    }
    @media (min-width: 768px) {
    .sidebar {
        position: fixed;
        top: 51px;
        bottom: 0;
        left: 0;
        z-index: 1000;
        display: block;
        padding: 20px;
        overflow-x: hidden;
        overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
        background-color: #f5f5f5;
        border-right: 1px solid #eee;
    }
    }

    /* Sidebar navigation */
    .nav-sidebar {
    margin-right: -21px; /* 20px padding + 1px border */
    margin-bottom: 20px;
    margin-left: -20px;
    }
    .nav-sidebar > li > a {
    padding-right: 20px;
    padding-left: 20px;
    }
    .nav-sidebar > .active > a,
    .nav-sidebar > .active > a:hover,
    .nav-sidebar > .active > a:focus {
    color: #fff;
    background-color: #428bca;
    }


    /*
    * Main content
    */

    .main {
    padding: 20px;
    }
    @media (min-width: 768px) {
    .main {
        padding-right: 40px;
        padding-left: 40px;
    }
    }
    .main .page-header {
    margin-top: 0;
    }


    /*
    * Placeholder dashboard ideas
    */

    .placeholders {
    margin-bottom: 30px;
    text-align: center;
    }
    .placeholders h4 {
    margin-bottom: 0;
    }
    .placeholder {
    margin-bottom: 20px;
    }
    .placeholder img {
    display: inline-block;
    border-radius: 50%;
    }
</style>