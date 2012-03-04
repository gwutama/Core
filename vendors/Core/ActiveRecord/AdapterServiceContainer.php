<?php

namespace Core\ActiveRecord;
use \Core\Service\ServiceContainer;
use \Core\Storage\Config;

class AdapterServiceContainer extends ServiceContainer {

    public function __construct() {
        // Register adapter services from database.yml
        $databaseProfiles = Config::get("database");

        foreach($databaseProfiles as $profile) {
            $profileName = $profile->getKey();

            // production or debug?
            if(Config::get("global.debug")) {
                $profileCfg = $profile->getChild("debug")->getChildren();
            }
            else {
                $profileCfg = $profile->getChild("production")->getChildren();
            }

            $config = array();
            foreach($profileCfg as $cfg) {
                $config[$cfg->getKey()] = $cfg->getValue();
            }

            $this->register("\\Core\\ActiveRecord\\Adapter\\".$config["adapter"],
                $config, array(), $profileName);
        }
    }

}

?>