<?php

/**
* <h1>Interface Core_TemplateHelper</h1>
* 
* <p>
* Interface TemplateHelper must be implemented by every template
* helper class.
* </p>
* 
* @example
* <code>
* class SomeHelper implements Core_TemplateHelper {
* 	public function getName() {
* 		return "someHelper";
* 	}
* }
* </code>
* 
* @see libs/helpers/HtmlHelper.php
* 
* @author Galuh Utama
*
*/
interface Core_TemplateHelper {
    // Must return a string as the helper name.
    public function getName();
}

?>