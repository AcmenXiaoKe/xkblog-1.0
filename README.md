## Xkblog

采用ThinkPHP5.1  + mysql 开发的个人博客程序

目录结构

```
├──application                  应用目录
│  ├──admin                     后台模块
|  |  ├──controller             控制器目录
|  |  ├──model                  模型目录     
|  |  ├──view                   模板目录     
│  ├──index                     前台模块 
|  |  ├──controller             控制器目录
|  |  ├──model                  模型目录     
|  |  ├──view                   模板目录     
│  ├──http                      中间件   
│  ├──install                   安装模块
|  |  ├──controller             控制器目录
|  |  ├──model                  模型目录     
|  |  ├──view                   模板目录 
│  ├──404.html                  404页面模板
│  ├──common.php                公共模块
├──config                       应用目录
│  ├──template                  前台模块配置目录
│  ├──app.php                   应用配置
│  ├──cache.php                 缓存配置
│  ├──cookie.php                Cookie配置
│  ├──database.php              数据库配置
│  ├──log.php                   日志配置
│  ├──session.php               Session配置
│  ├──template.php              模板引擎配置
│  ├──trace.php                 Trace配置
├──public                       WEB目录（对外访问目录）
│  ├──static                    静态文件存储目录
│  ├──uploads                   文件上传目录
│  ├──index.php                 应用入口
├──route                        路由定义目录
├──runtime                      路由定义目录
├──thinkphp                     框架系统目录
```

[线上版](https://github.com/AcmenXiaoKe/xkblog-1.0)：整包：1.79MB 全站采用 Jsdelivr 全球加速 ( 静态文件全部采用 Jsdelivr 加速 ) 

[开发版](https://github.com/AcmenXiaoKe/xkblog)：静态文件采用本地