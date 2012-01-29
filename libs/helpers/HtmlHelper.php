<?php

/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class HtmlHelper implements Core_TemplateHelper {

    /**
     * (non-PHPdoc)
     * @see TemplateHelper::getName()
     */
    public function getName() {
        return "htmlHelper";
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $timestamp
     */
    public function formatTimestamp($timestamp) {
        return DateTime::createFromFormat("Y-m-d H:i:s", $timestamp)->format("d-m-Y H:i:s");
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $controller
     * @param unknown_type $action
     * @param unknown_type $parameters
     */
    public function urlFor($controller, $action="index", $parameters = array()) {
        $params = "";
        foreach($parameters as $key=>$value) {
            $params .= "$key/$value/";
        }
        if($action == Core_Config::get("default.action")) {
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
     * @param unknown_type $attributes
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
     * @param unknown_type $attributes
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
     * @param unknown_type $attributes
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
     * @param unknown_type $attribute
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