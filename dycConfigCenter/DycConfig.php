<?php
namespace app;

class DycConfig{
    private $config = [];

    public function __construct() {
        $this->config = include("douYinCloudConfig.php");
    }

    public  function getStringValue($key, $defaultKey){
        if(isset($this->config[$key])){
            return $this->config[$key];
        }
        return $defaultKey;
    }

    public function refreshConfig() {
        $client = new DycConfigClient();
        $client->updateConfig();
    }
}