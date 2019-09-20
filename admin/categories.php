<?php 
  require_once '../function.php';
  //判断用户是否登录
  xiu_get_current_user();
  //修改操作和查询操作在一起,一定先做修改再查询
  //
  //添加数据操作 获取提交的值,赋值给变量,在数据库的表中添加数据,判断提交是否成功(根据返回的修改行数判断,成功后对应提示信息修改)
  //$GLOBALS['message']错误显示警告信息  $GLOBALS['success']添加成功标志 true
  function add_categories(){
      if(empty($_POST['name'])||empty($_POST['slug'])){
        $GLOBALS['message']='请填写完整表单';
        $GLOBALS['success']=false;
        return;
      }
      $name=$_POST['name'];
      $slug=$_POST['slug'];
      //返回修改的行数
      $row=xiu_execute("insert into categories values (null,'{$slug}','{$name}');");

      $GLOBALS['success']=$row>0; //对应message成功
      $GLOBALS['message']=$row<=0?'添加错误':'添加成功';             
  }
  //这个函数之后post请求发送后才执行 编辑分类数据
  function edit_categories(){
    //必须在href中?id=xx 传递了才能拿到 如果函数执行,才显示表单
    global $current_edit_categories;  //全局变量获取 分类数据
    // $_GET['id']
    // 提交update更新数据库
    $name=empty($_POST['name'])?$current_edit_categories['name']:$_POST['name'];
    $current_edit_categories['name']=$name;
    $slug=empty($_POST['slug'])?$current_edit_categories['slug']:$_POST['slug'];
    $current_edit_categories['slug']=$slug;
    $id=$current_edit_categories['id'];
    //执行数据库修改操作 调用函数 更新数据库
    $row=xiu_execute("update categories set slug='{$slug}', name='{$name}' where id={$id} ");
    $GLOBALS['success']=$row>0; //对应message成功
    $GLOBALS['message']=$row<=0?'更新错误':'更新成功'; 
  }


  //编辑页面通过post请求获取 id=""的形式获取 id写在action中会自动拼接在url后面
  if (empty($_GET['id'])) {
    //post提交执行添加
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      add_categories();
  }
  }else{
  //执行编辑 操作 先拿到筛选的结果
    $current_edit_categories=xiu_fetch_one('select * from categories where id='.$_GET['id']);
    if ($_SERVER['REQUEST_METHOD']=='POST') {
      edit_categories();
    }
  }
  // 查寻数据并且呈现,得到一个数组  右边的展示功能写在最后,上面的模块只需要把数据写进数据库
  $categories=xiu_fetch_all('select * from categories;');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    .category_cat {
      position: relative;
      padding-top: 40px;
    }
    .category_cat #btn_delete{
      position: absolute;
      left: 15px;
      top: 0;
      display: none;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php' ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <?php if ($success): ?>
        <div class="alert alert-success">
        <strong>成功！</strong><?php echo $message ?>
      </div>
      <?php else : ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message ?>
      </div>
      <?php endif ?>
      <?php endif ?>
      
      <div class="row">
        <!-- 编辑显示的界面 区别在 保存和添加按钮的不同-->
        <div class="col-md-4">
        <?php if (isset($current_edit_categories)): ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_categories['id']; ?>" method='post'>
            <h2>编辑《<?php echo $current_edit_categories['name'] ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_categories['name'] ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_categories['slug'] ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
        <?php else: ?>
          <!-- 分类显示界面  -->
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        <?php endif ?>
          


        </div>
        <div class="col-md-8 category_cat">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id='btn_delete' class="btn btn-danger btn-sm" href="/admin/dele_categories.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $key) :?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $key['id']; ?>"></td>
                <td><?php echo $key['name']; ?></td>
                <td><?php echo $key['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $key['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/dele_categories.php?id=<?php echo $key['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php $current_page='categories'; ?>
<?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($){
      var $tbodyCheckbox=$('tbody input');
      var $btnDelete=$('#btn_delete');
      var moreCheckeds=[];
      // 全选 选中
      var allSelect=$('thead input');
      // console.log(allSelect);
      allSelect.on('change',function() {
        // alert(1);
        var selected = allSelect.prop('checked'); //获取顶部盒子的选中状态
        $tbodyCheckbox.prop('checked',selected);//设置所有分类的状态
        $tbodyCheckbox.trigger('change'); //触发change事件
      });
      $tbodyCheckbox.on('change',function () {
        console.log(1);
        var id=$(this).data('id');
        //判断选中还是未选中
        if ($(this).prop('checked')) {
          moreCheckeds.push(id);
        }else{
          moreCheckeds.splice(moreCheckeds.indexOf(id),1);
        }
        //数组长度>0有选中 显示
        moreCheckeds.length?$btnDelete.fadeIn():$btnDelete.fadeOut();
        // $btnDelete.attr('href','/admin/dele_categories?id='+ moreCheckeds);
        $btnDelete.prop('search','?id=' +moreCheckeds);
      })

    })

  </script>
  <script>NProgress.done()</script>
</body>
</html>
