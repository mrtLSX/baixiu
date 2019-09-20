<?php 
// session_start();
// //查询$_SESSION['current_login_user']=$user;
// //如果没有这个数据,没有登录,跳到登录界面
// if (empty($_SESSION['current_login_user'])) {
//   header('Location: /admin/login.php');

  //这里的代码每个展示的页面都要进行判断
  require '../function.php';

  //获取登录信息一定要在最前面
  xiu_get_current_user();

  

  // var_dump($posts_count[0]['count(1)']); 帅选数量 作为 num属性
  $posts_count = xiu_fetch_one('select count(1) as num from posts;')['num'];

  $categories_count = xiu_fetch_one('select count(1) as num from categories;')['num'];

  $comments_count = xiu_fetch_one('select count(1) as num from comments;')['num'];

 // var_dump(xiu_fetch_one('select count(1) as num from comments;')['num']);

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php' ?>
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.html" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php  echo $posts_count ?></strong>篇文章（<strong>2</strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php  echo $categories_count ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php  echo $comments_count ?></strong>条评论（<strong>1</strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
          <div id="main" style="width: 600px;height:400px;"></div>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

<?php $current_page='index'; ?>
<?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chartjs/echarts.min.js"></script>

  <script>
    
    
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '站点内容统计'
            },
            tooltip: {},
            legend: {
                data:['销量']
            },
            xAxis: {
                data: ["文章","分类","评论"]
            },
            yAxis: {},
            series: [{
                name: '数量',
                type: 'bar',
                data: [5, 20, 36, 10, 10, 20]
            }]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
  </script>

  <script>NProgress.done()</script>
</body>
</html>
