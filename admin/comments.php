<?php 
  require_once '../function.php';
  //判断用户是否登录
  xiu_get_current_user();

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <style>
      #loading{
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.7);
        z-index: 999;
      }
      .flip-txt-loading {
        font: 26px Monospace;
        letter-spacing: 5px;
        color: #fff;
      }

      .flip-txt-loading > span {
        animation: flip-txt  2s infinite;
        display: inline-block;
        transform-origin: 50% 50% -10px;
        transform-style: preserve-3d;
      }

      .flip-txt-loading > span:nth-child(1) {
        -webkit-animation-delay: 0.10s;
                animation-delay: 0.10s;
      }

      .flip-txt-loading > span:nth-child(2) {
        -webkit-animation-delay: 0.20s;
                animation-delay: 0.20s;
      }

      .flip-txt-loading > span:nth-child(3) {
        -webkit-animation-delay: 0.30s;
                animation-delay: 0.30s;
      }

      .flip-txt-loading > span:nth-child(4) {
        -webkit-animation-delay: 0.40s;
                animation-delay: 0.40s;
      }

      .flip-txt-loading > span:nth-child(5) {
        -webkit-animation-delay: 0.50s;
                animation-delay: 0.50s;
      }

      .flip-txt-loading > span:nth-child(6) {
        -webkit-animation-delay: 0.60s;
                animation-delay: 0.60s;
      }

      .flip-txt-loading > span:nth-child(7) {
        -webkit-animation-delay: 0.70s;
                animation-delay: 0.70s;
      }

      @keyframes flip-txt  {
        to {
          -webkit-transform: rotateX(1turn);
                  transform: rotateX(1turn);
        }
      }
  
  </style>
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php' ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">

        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="140">操作</th>
          </tr>
        </thead>
        <tbody>
          

        </tbody>
      </table>
    </div>
  </div>



<div id="loading" style="display: none">
  <div class="flip-txt-loading" >
  <span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span>
  </div>
</div>
  
<?php $current_page='comments'; ?>
<?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  
  <script id="comments_tmpl" type="text/x-jsrender">

    {{for comments}}  

    <tr{{if status=='held'}} class="warning"{{else status=='rejected'}} class="danger" {{/if}} data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>{{:post_title}}</td>
      <td>{{:created}}</td>
      <td>{{:status}}</td>
      <td class="text-center">
        {{if status=='held'}}
        <a href="post-add.html" class="btn btn-info btn-xs">  批准</a>
        
        <a href="javascript:;" class="btn btn-warning btn-xs">拒绝</a>
        {{/if}}
        <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
      </td>
    </tr>
    {{/for}}
  </script>
    <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>

  <script>
    //发送ajax请求获取数据,
   // $.getJSON('/admin/api/comments.php',{page:2},function (res) {
   //    //请求执行完成过后,自动执行
   //    console.log({comments:res});
      
   //    var html=$('#comments_tmpl').render({comments:res});
   //    console.log(html);
   //    $('tbody').html(html);
   //  }) 
   $(document)
   .ajaxStart(function(){
    NProgress.start()
    $('#loading').fadeIn()
    // $('#loading').css('display','flex')
   })
   .ajaxStop(function(){
    NProgress.done()
    $('#loading').fadeOut()
    // $('#loading').css('display','none')
   })

  var currentPage=1;
    function loadPageDate(page){
      $('tbody').fadeOut();
      $.getJSON('/admin/api/comments.php',{page:page},function (res) {
        //请求执行完成过后,自动执行
        // console.log(res);
        // console.log({comments:res});
        
        // 出现问题,开始的页码删除后大于总页码,直接加载 后面不需要再执行
        if (page>res.total_pages) {
          loadPageDate(res.total_pages);
          return;
        }
        // 渲染数据前先更新页码按钮
        $('.pagination').twbsPagination('destroy');
        $('.pagination').twbsPagination({
            totalPages: res.total_pages,//总页数
            startPage: page,
            visiblePages:5,//显示的页数
            initiateStartPageClick:false,
            first: '首页',
            prev: '前一页',
            next: '下一页',
            last: '尾页',
             //点击请求数据
            onPageClick:function(event,page){
              //page为当前页码 首次加载自动触发一次
              loadPageDate(page);
            }
        });
      //渲染页面数据
      var html=$('#comments_tmpl').render({comments:res.comments});
        // console.log(html);
      $('tbody').html(html).fadeIn();
      currentPage=page;
      })
    }
    // 将数据渲染到页面上
    // 
    loadPageDate(currentPage);
    //删除按钮发送ajax请求,事件委托,给父节点绑定事件,因为删除按钮只有在页面渲染完成后才能生成
    $('tbody').on('click','.btn-delete',function(){
      //没拿到id
      var id=$(this).parent().parent().data('id');
      
      $.get('/admin/api/comment-delete.php',{id:id},function (res) {
        // console.log(typeof(res));
        // if(res){
        //   console.log(111);
        // }
        // // if (!res) return
        // //重新加载页面
        // else{
        //   console.log(222);
        // }
        loadPageDate(currentPage);
      })
    })
    
  </script>


  <script>NProgress.done()</script>
</body>
</html>
