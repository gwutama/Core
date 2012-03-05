<?php

namespace Helpers;

use Core\Template\TemplateHelper;

/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class Html implements TemplateHelper {

    /**
     * (non-PHPdoc)
     * @see TemplateHelper::getName()
     * @return string
     */
    public function getName() {
        return "htmlHelper";
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $timestamp
     * @return mixed
     */
    public function formatTimestamp($timestamp) {
        return DateTime::createFromFormat("Y-m-d H:i:s", $timestamp)->format("d-m-Y H:i:s");
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $controller
     * @param \Helpers\unknown_type|string $action
     * @param array|\Helpers\unknown_type $parameters
     * @return string
     */
    public function urlFor($controller, $action="index", $parameters = array()) {
        $params = "";
        foreach($parameters as $key=>$value) {
            $params .= "$key/$value/";
        }
        if($action == Config::get("default.action")) {
            $link = sprintf("%s/%s/%s", RELATIVE_URL, strtolower($controller), strtolower($params));
        }
        else {
            $link = sprintf("%s/%s/%s/%s", RELATIVE_URL, strtolower($controller),
                        strtolower($action), strtolower($params));
        }
        return $link;
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $file
     * @param array|\Helpers\unknown_type $attributes
     * @return string
     */
    public function image($file, $attributes=array()) {
        $file = sprintf("%s/images/%s", RELATIVE_URL, $file);
        $attributes = $this->buildAttributes($attributes);
        $html = "<img src=\"$file\" $attributes/>\n";
        return $html;
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $file
     * @param array|\Helpers\unknown_type $attributes
     * @return string
     */
    public function style($file, $attributes=array()) {
        $file = sprintf("%s/css/%s", RELATIVE_URL, $file);
        $attributes = $this->buildAttributes($attributes);
        $html = "<link type=\"text/css\" rel=\"stylesheet\" href=\"$file\" $attributes/>\n";
        return $html;
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $file
     * @param array|\Helpers\unknown_type $attributes
     * @return string
     */
    public function script($file, $attributes=array()) {
        $file = sprintf("%s/js/%s", RELATIVE_URL, $file);
        $attributes = $this->buildAttributes($attributes);
        $html = "<script type=\"text/javascript\" src=\"$file\" $attributes></script>\n";
        return $html;
    }


    /**
     *
     * Enter description here ...
     * @param array|\Helpers\unknown_type $attribute
     * @return string
     */
    private function buildAttributes($attribute=array()) {
        $attr = "";
        foreach( (array) $attribute as $key=>$value) {
            $attr .= "$key=\"$value\" ";
        }
        return $attr;
    }
}

?>