<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'login';
$this->params['breadcrumbs'][] = $this->title;

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="./css/login.css">
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<title><?= Html::encode($this->title) ?> </title>
</head>
<body>
<div class="container">

<?php $form = ActiveForm::begin(
	    ['id'=>'login-form',
	    'class'=>'form-signin',
	     'action'=>['login/login'],
	     'options'=>['class'=>'form-signin'],
	     'fieldConfig'=> ['labelOptions' => ['class' => 'sr-only']],
	     
	]);?>
	
		<h2 class="form-sign-heading">请登录</h2>
		<?= $form->field($model, 'username',['inputOptions' => ['placeholder' => '用户名']])?>
		<?= $form->field($model,'password',['inputOptions'=>['placeholder'=>'密码']])->passwordInput()?>
		<div class="checkbox">
			<label>
			<input type="checkbox" name="remember" vlaue="记住密码">记住密码
			</label>
		</div>
		<button  class="btn btn-lg btn-primary btn-block">登录</button >
	<?php ActiveForm::end();?>
</div>



</body>
</html>