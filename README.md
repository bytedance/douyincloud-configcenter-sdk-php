# douyincloud-configcenter-sdk
本项目是抖音云配置中心的SDK插件，用以访问抖音云配置中心。

## 安装
1. 下载本项目；
2. 将dycConfigCenter目录及其目录下的文件拷贝到自己的项目中；
3. 在启动脚本 /opt/application/run.sh 中，在项目服务的命令启动前添加如下命令：
```
   php ./configStart.php &
```
4. 在项目中需要使用配置中心的地方将dycConfigCenter/DycConfig.php 使用php的require语句加载进项目中 ，之后即可正常使用Php的SDK。
```
    require('/opt/application/dycConfigCenter/DycConfig.php');
```
## 使用
根据key获取单个配置
```
$key = "key1";
$config = new DycConfig();
$value = $config->getStringValue($key, "default");
```
从云端获取配置刷新本地缓存
```
$config = new DycConfig();
$config->refreshConfig();
```


## 使用注意事项
- 使用前确保已在抖音云平台开启配置中心；
- 抖音云配置中心已通过专属的网络链路实现鉴权，您可以直接使用无需关心鉴权的具体逻辑；
- 由于抖音云配置中心有专属的鉴权逻辑，因此，您在开发调试时请使用抖音云的本地调试插件来连接抖音云配置中心。
