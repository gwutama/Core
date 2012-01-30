<h2>SSF - The Barely Minimum Framework</h2>
<p class="notice success">
	This is the main page of the project.
</p>
<p class="notice success">
	To change the appearance of this page, please modify the template file
	<em>views/home.index.tpl</em>.
</p>
<h3>Where to go from here?</h3>
<ul>
	<li>Read the quick start guide</li>
	<li>Browse the API Documentation</li>
</ul>
<br />
<h3>What is this all about?</h3>
<p>
	In essence, SSF is not really a framework. It is actually more of a very simple
	project layout structure that can be understood by everyone, even by a
	novice PHP programmer. I try to keep the core file small, preferably under 10 KB.
	Despite of its small kernel, it is aimed to be able to achieve as much functions
	as possible.
</p>
<h4>Target users</h4>
<p>
	Although you can definitely do much with this framework, it was intended as a 
	learning experience for myself. SSF is thoughtfully developed for :
</p>
<ul>
	<li>Small projects where the use of a full blown framework would be overkill</li>            
	<li>PHP programmers who already know about OOP and want to expand their knowledge
		on building basic framework structure</li>
	<li>introduction to MVC pattern in web development, particularly for novices who 
		still think that frameworks are only adding overheads and complexity
		and thus prefer to do spagetti code</li>
</ul>
<br />
<h4>Features</h4>
<p>
	SSF provides you with:
</p>
<ul>
	<li>a basic routing system</li>
	<li>a very simple pretty URL system</li>
	<li>a minimal templating system (template helper system included)</li>
	<li>separation between application logic and template (Controller-View approach)</li>
</ul>
<br />
<p>That's it. However, you'd be surprised that these things are sufficient for most small projects.</p>
<p>
	And here's what SSF can't do for you:
</p>
<ul>
	<li>Sanitizing inputs from SQL injections, XSS and other security features</li>
	<li>ORM layer for your database</li>
	<li>Caching</li>
	<li>Unit Testing</li>
	<li>Internationalization</li>
	<li>Access Control List</li>
	<li>Plugins for [put anything here]</li>
	<li>...</li>
</ul>
<br />
<p>For any of those things you have to implement it by yourself, or you can also put
third party libraries into <em>libs/</em> and include it in your controller.</p>
<h4>Requirements</h4>
<ul>
	<li>PHP 5.1 or better</li>
	<li>Apache Webserver with mod_rewrite (although rewriting the rules for any webserver
	should be straightforward)</li>
</ul>