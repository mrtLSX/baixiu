<?php 
  require_once '../function.php';
  //判断用户是否登录
  xiu_get_current_user();
//执行筛选 两个参数 一个 category id 分类id  一个 status 状态
  $where= '1 = 1';  //相当于没有加条件
    $search='';

  if (!empty($_GET['category']) && $_GET['category']!=='all') {
    $where .=' and posts.category_id = '. $_GET['category'];  
    $search .= '&category='.$_GET['category'];
  }
  if (!empty($_GET['status']) && $_GET['status']!=='all') {
    $where .=" and posts.status = '{$_GET['status']}'";  
    $search .= '&status='.$_GET['status'];
  }

  $size=20; //一页显示条数

  //筛选之前需要获取当前是第几页,默认第一页,根据get提交page
  //$_GET['page']为字符串,需要转int
  $page=empty($_GET['page'])? 1 : (int)$_GET['page'];

  // $page=$page < 1 ? 1 : $page ;  
  //get获取的页码不能小于0 防止直接修改url的方式获取
  if ($page<1) {
    header('Location:/admin/posts.php?page=1'.$search);
  }
  // 同理$page不能大于总页面数
  $total_count = (int)xiu_fetch_one ("select count(1) as num from posts inner join categories on categories.id=posts.category_id
  inner join users on posts.user_id=users.id
  where {$where};") ['num'];
  //获取总页码数
  $total_pages = (int)ceil ($total_count / $size); //这里的数据为float类型需要转换
  // var_dump($total_pages);
  // $page=$page > $total_pages?$total_pages:$page;
  if ($page > $total_pages) {
    header('Location:/admin/posts.php?page='.$total_pages.$search);
  }

//接收筛选参数
//==============================================


  //每次查询跳过多少条数据
  $offset=($page-1)* $size;
  //展示数据筛选
  $posts = xiu_fetch_all("select
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  categories.name as category_name,
  users.nickname as user_name
  from posts
  inner join categories on categories.id=posts.category_id
  inner join users on posts.user_id=users.id
  where {$where}
  order by posts.created desc
  limit {$offset}, {$size};");
  // var_dump($posts); 通过$posts[0][属性值]
  // 
  //处理分页页码
  //处理获取总页数


  $visiables=5;  //一共显示的页码按钮数量
  $regin=($visiables-1)/2;   //2

  $begin=$page-$regin;
  $end=$begin+$visiables-1;  //end=最后页码+1
  //出现$begin不合理$end不合理 $begin>0,$end<=最大页数
  if ($begin<1) {
    $begin = 1;
    $end= $begin + $visiables-1;
  }

  if ($end>$total_pages ) {
    $end = $total_pages ;  //14 15-5=10 11 12 13
    $begin = $end - $visiables+1;  //这里总页数小于5的时候,这样计算$begin会小于1
    $begin=$begin < 1 ? 1 : $begin;  //确保$begin不能小于1
  }



  //处理状态转化为中文
  function convert_satatus($status){
    $dict=array(
      "published"=>"已发布",
      "drafted"=>"草稿",
      "trashed"=>"回收站"
    );
    return isset($dict[$status])?$dict[$status]:'未知';
  };
  //转化时间格式
  function convert_time($created){
    $time=strtotime($created); //转化为时间戳
    return date('y年m月d日 <b\r>H:i:s',$time);
     // return date('Y年m月d日 <b\r> H:i:s', $time);
  }

//查询
  // function get_category($category_id){
  //   return xiu_fetch_one("select name from categories where id ={$category_id};")['name'];
  // }
  // function get_user($user_id){
  //   return xiu_fetch_one("select nickname from users where id ={$user_id};")['nickname'];
  // }
  // 
  //处理分类功能=======================================================
  //查询分类数据
    $categories=xiu_fetch_all('select * from categories;');

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id'] ?>" 
                <?php 
                  echo isset($_GET['category']) && $_GET['category']===$item['id']? 'selected':'' ;
                ?>>
                <?php echo $item['name'] ?>
                  
              </option>
            <?php endforeach ?>
          </select>
          <!-- 状态数据显示 -->
          <select name="status" class="form-control input-sm">
            <option value="all" >所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status']==="drafted"? 'selected':'' ;?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status']) && $_GET['status']==="published"? 'selected':'' ;?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status']==="trashed"? 'selected':'' ;?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <!-- 页码选择框 -->
        <ul class="pagination pagination-sm pull-right">
          <li><a href="?page=<?php echo ($page-1).$search?>">上一页</a></li>
          
          <?php for ($i=$begin; $i <=$end; $i++): ?>
            <li <?php echo $page==$i?'class="active"':'';?>>
            <a href="?page=<?php echo $i.$search?>" ><?php echo $i ?></a></li> 
          <?php endfor ?>
          <li><a href="?page=<?php echo ($page+1).$search?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title'] ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name'] ?></td>
            <td class="text-center"><?php echo convert_time($item['created']); ?></td>
            <td class="text-center"><?php echo convert_satatus($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/post-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>


        </tbody>
      </table>
    </div>
  </div>
<?php $current_page='posts'; ?>
<?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
