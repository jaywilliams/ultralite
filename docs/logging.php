<p>
<a href="index.php">Back To Index</a>
</p>

<h1>Logging</h1>
<p>

Logging is provided with the logging class. The logging class found in the application/libs/logging.class.php and contains a single static __call() method. This enables the coder to supply an unlimitted amount of logging calls.
</p>
</p>
<div class="codebox">
<?php
$code = '
<?php
logger::auditLog($message, 100, __FILE__, __LINE__ );
logger::errorLog($message, 200, __FILE__, __LINE__ );
logger::debugLog($message, 300, __FILE__, __LINE__ );
?>';
echo highlight_string($code,1);
?>
</div>
<p>
The above shows how diffent functions can be dynamically created to suit the needs of the developer. The second arguement is the log level. The log level is set in the application/config/config.ini.php file. Only those log messages with a log level lower than log level specified in the config file will be logged. This, of course, means if the log level is set to zero, no logging will occur, unless a log level is also set to zero, which should be avoided. There is no sane limit to the number of logging levels you may specify.
</p>
<p>
The log has the option to store the log in a database, or on the file system. If the file option is used, the file MUST exist on the file system and be writeable by the httpd server.
</p>
<p>
The log class configuration options in the logging section of the config.ini.php file are as follows
</p>
<dl>
<dt>log_level</dt>
<dd>The logging level. Only calls with a log level equal to, or below this level will be recored</dd>

<dt>log_handler</dt>
<dd>file</dd>
<dd>database</dd>

<dt>log_file</dt>
</dd>The absolute path to the log file. This file MUST be writable by the web server.</p>
