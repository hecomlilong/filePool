此项目实现了启动一个本地http服务
此项目依赖php swoole插件
启动http服务：
在项目根目录执行如下命令：
php httpServer1.php
用ab 测试工具测试使用pool的情况：
在项目根目录执行如下命令：
ab -c 1 -n 1 -T 'application/x-www-form-urlencoded' -p postDataAddFile.txt http://127.0.0.1:9501/
ab -c 1 -n 10000 -T 'application/x-www-form-urlencoded' -p postDataWithPool.txt http://127.0.0.1:9501/

用ab测试工具测试不使用pool的情况：
ab -c 1 -n 10000 -T 'application/x-www-form-urlencoded' -p postDataWithoutPool.txt http://127.0.0.1:9501/

针对多进程的情况，如果执行http post请求action='addText'失败时，只需要先执行一次http post请求action='addFile'


![先执行addFile操作](https://raw.githubusercontent.com/username/projectname/branch/path/to/img.png)