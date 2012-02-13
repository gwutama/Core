<h2>Something's Wrong</h2>
<p class="notice error">
	Exception <em><?php echo $exceptionClass ?></em> thrown:                         
	<?php echo $message ?>
</p>
<?php if($exceptionClass == "TemplateNotFoundException") : ?>
	<p>Please create the template file in <em>views/</em>.</p>
<?php elseif($exceptionClass == "ControllerNotFoundException") : ?>
	<p>Please create the controller class in <em>controllers/</em>. Here's a barely minimum example: </p>  
	<br />
	<div class="code">
		<code>
			class ClassNameController extends Core\Controller {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;public function index() {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
			}<br/>
		</code>           
	</div>
<?php elseif($exceptionClass == "ActionNotFoundException") : ?>
	<p>Please create the action method. Here's a barely minimum example:</p>                                
	<br />                
	<div class="code">
		<code>
			class ClassNameController extends Core\Controller {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;public function someAction() {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
			}<br/>
		</code>
	</div>
<?php elseif($exceptionClass == "TemplateHelperNotFoundException") : ?>
	<p>Please create the template helper class in <em>libs/</em>. Here's a barely minimum example:</p>                                
	<br />
	<div class="code">                
		<code>
			class SomeHelper implements Core\TemplateHelper {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;public function getName() {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return "someHelper";<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;}<br/><br />                    
			&nbsp;&nbsp;&nbsp;&nbsp;public function methodName() {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
			}<br/>
		</code>                
	</div>
<?php endif ?>
<br />
<h3>Need help?</h3>
<ul>
	<li>Read the quick start guide</li>
	<li>Browse the API Documentation</li>
</ul>
