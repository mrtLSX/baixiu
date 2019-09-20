<?php 
  //载入配置文件
  require_once '../config.php';
  session_start();
function login(){
  //1.接收数据
  //2.持久化
  //3.响应
  //$GLOBALS 定义 全局变量存储错误信息
  if (empty($_POST['email'])) {

    $GLOBALS['message']='请填写邮箱';
    return;
  }
  if (empty($_POST['password'])) {
    # code...
    $GLOBALS['message']='请输入密码';
    return;
  }
  if (empty($_POST['email'])) {
    # code...
    $GLOBALS['message']='请填写邮箱';
    return;
  }
  //通过post获取账号密码
  $email=$_POST['email'];
  $password=$_POST['password'];
  //判断账号密码是否匹配
  // if ($email!=='admin@sample.com') {
  //   $GLOBALS['message'] = '邮箱和密码不匹配';
  //   return;
  // }
  // if ($password!=='admin') {
  //   $GLOBALS['message'] = '邮箱和密码不匹配';
  //   return;
  // }
  // 数据库校验
  $conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);

  if (!$conn) {
    exit('<h1>连接数据库失败</h1>');
  }

  $query=mysqli_query( $conn, "select * from users where email = '{$email}' limit 1;");

  if (!$query) {
    $GLOBALS['message']='登录失败,重试';
    return;
  }

//取到用户在数据库中的登录信息   mysqli_fetch_assoc获取查询到的结果的第一条,返回一个对象
  $user=mysqli_fetch_assoc($query);

  if (!$user) {
    //用户名不存在
    $GLOBALS['message']='用户不存在';
    return;
  }

  if ($user['password']!==$password) {
    //密码不正确
    $GLOBALS['message'] = '邮箱与密码不匹配';
    return;
  }

  //登陆成功后将user登录信息写入session,方便登陆成功后获取
    $_SESSION['current_login_user']=$user;
  
  //yiqie ok 调转 
  header('Location: /admin/');



}
if ($_SERVER['REQUEST_METHOD']==='POST') {
  # 接收本页面传递的 post 请求,执行 login()函数
  login() ;
}
if ($_SERVER['REQUEST_METHOD']==='GET' && isset ($_GET['action']) && $_GET['action'] ==='logut') {
  //删除用户信息 session   删除登录标识
  unset($_SESSION['current_login_user']);
}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate%20.css">
  
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  

</head>
<body>
  <div class="login">
    <!-- 处理表单标签 在本页面处理php业务逻辑 -->
    <form class="login-wrap <?php echo isset($message)?'shake animated':'' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete='off'>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.min.js"></script>
  <script>
    $(function ($) {
      //单独作用域
      //确保页面加载过后再执行
      
      //目标:用户输入自己的邮箱过后拿到页面上展示头像
      //实现:
      //时机:邮箱文本框失去焦点的时候,拿到文本中的邮箱时
      //-事情:获取这个文本框中填写的邮箱对应的头像的地址展示到上面的img元素上
      var emailFormat=/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/ ;
      $('#email').on('blur',function () {
        var value=$(this).val();

        if (!value|| !emailFormat.test(value)) {
          console.log('规则不匹配');
          return
        };
        //拿到email$(this).val(); 判断符合邮箱规则吗,js通过ajax请求php页面,获取头像地址
        $.get('/admin/api/avaer.php',{email:value},function (res) {
          if (!res) return
            $('.avatar').attr('src',res);
        });
      })

    })
  </script>
</body>
</html>
