<p>
<a href="index.php">Back To Index</a>
</p>

<p>
Access to the WEB2BB system is controlled by mod_rewrite rules in .htaccess and this needs to be enabled for the system to work correctly. See your web server documentation to see how this is done.
</p>

<p>
The .htaccess file itself provides some simple mod_rewrite conditions to enable the routing to work correctly.
</p>

<pre>
RewriteEngine on

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*)$ index.php?$1 [L,QSA]
</pre>
