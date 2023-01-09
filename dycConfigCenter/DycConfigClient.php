<?php
namespace dycConfigCenter;

class DycConfigClient
{
    private $dycConfigUrl = "http://config-center.dycloud.run:8699/config/get_config_list";
    private $intervalRefreshTime = 60;// 单位s
    private $requestTimeout = 5;// 单位s
    private $fileCacheName = "douYinCloudConfig.php";
    private $oldVersion = "0";
    const StringTag = 5;
    const NoAuth = 2400402;
    const ConfigCenterNotOpen = 2400401;
    const retryTimes = 3;


    public function __construct($config = []) {

        if(!empty($config['interval_refresh_time']) && $config['interval_refresh_time'] >= $this->intervalRefreshTime) {
            $this->intervalRefreshTime = $config['interval_refresh_time'];
        }

        if(!empty($config['request_timeout'])) {
            $this->requestTimeout = $config['request_timeout'];
        }
        $env_url = getenv('DYC_CONFIG_CENTER_URL');
        if(!empty($env_url)){
            $this->dycConfigUrl = $env_url;
        }
        $this->fileCacheName = dirname(__FILE__) . "/douYinCloudConfig.php";
    }

    public function updateConfig(){
        for($i=0;i<self::retryTimes;$i++){
            $data = $this->getConfigFromDycConfig();
            if(!empty($data)){
                break;
            }
        }
        if(empty($data)){
            return false;
        }
        $config = $this->convertDycConfigToMap($data);
        if($data['version'] < $this->oldVersion){
            return;
        }
        $this->oldVersion = $data['version'];
        $text = '<?php return '.var_export($config,true).';';
        file_put_contents($this->fileCacheName,$text);
    }


    public function getConfigFromDycConfig(){
        $data = array('version' => $this->oldVersion);
        $data = http_build_query($data);
        $opts = array(
            'http' => array(
                'method'    => 'POST',
                'header'    => 'Content-type: application/json' . 'Content-Length: ' . strlen($data),
                'content'   => $data,
                'timeout'   => $this->requestTimeout,
            )
        );
        $context = stream_context_create($opts);
        $html = file_get_contents($this->dycConfigUrl, false, $context);
        $res = json_decode($html, true);
        if(is_array($res)){
            if($res["code"] == self::NoAuth){
                echo "Permission denied. You have no permission to access the dyc config center. Please check whether the program " .
                                "is running in dyc cloud or in the ide with dyc plugin.";
                return false;
            }
            if($res["code"] == self::ConfigCenterNotOpen){
                echo "Please check whether the config center is opened in douyin cloud.";
                return false;
            }
            return $res["data"];
        }
    }

    private function convertDycConfigToMap($dycConfig) {
        if(!is_array($dycConfig["kvs"])){
            return;
        }
        $config = array();
        foreach($dycConfig["kvs"] as $item){
            if($item["type"] == self::StringTag) {
                $config[$item["key"]] = $item["value"];
            }
        }
        return $config;
    }

    public function startLoop(){
        try {
            do{
                $this->updateConfig();
                sleep( $this->intervalRefreshTime);
            }while(true);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}