此项目实现了启动一个本地http服务，可以在一次打开log文件的情况下，多次向文件写入内容。节省了频繁打开关闭文件的时间。
此项目依赖php的swoole插件
启动http服务：
在项目根目录执行如下命令：
php httpServer1.php

使用时只需向此http服务发送post请求，个字段可能的值和含义如下：
action: addFile表示注册一个log文件，addText表示向一个log文件写入内容。
file_path: log文件绝对路径。当action=addFile时使用。
content: 需要写入log文件的内容，当action=addText时使用。
fileId: log文件注册编号，当action=addText时使用。

用ab 测试工具测试使用pool的情况：
在项目根目录执行如下命令：
ab -c 1 -n 1 -T 'application/x-www-form-urlencoded' -p postDataAddFile.txt http://127.0.0.1:9501/
ab -c 1 -n 10000 -T 'application/x-www-form-urlencoded' -p postDataWithPool.txt http://127.0.0.1:9501/

用ab测试工具测试不使用pool的情况：
ab -c 1 -n 10000 -T 'application/x-www-form-urlencoded' -p postDataWithoutPool.txt http://127.0.0.1:9501/

针对多进程的情况，如果执行http post请求action='addText'失败时，只需要先执行一次http post请求action='addFile'

首先需要先执行addFile操作，返回字段res==0表示成功，相应的fileId返回在data字段中（下图红框圈出），post请求细节如下图所示：
![先执行addFile操作](https://github.com/hecomlilong/filePool/blob/master/WX20170602-151156%402x.png)
然后执行addText操作，添加字符串到文件，返回字段res==0表示成功，post请求细节如下图所示：
![然后执行addText操作](https://github.com/hecomlilong/filePool/blob/master/WX20170602-151222%402x.png)