; config file for pagecave 

[application]
default_controller = index
default_action = index
error_controller = error404
error_reporting = E_ALL
display_errors = 1
language = en
timezone = "America/Los_Angeles"

[database]
db_name = web2bb
db_hostname = localhost
db_username = username
db_password = password
db_port = 3306

[template]
template_dir = "templates"
cache_dir = "/tmp/cache"
cache_lifetime = 3600

[mail]
admin_mail = admin@example.com
smtp_server = mail.phpro.org 
smtp_port = 25;
x_mailer = "PHPRO.ORG Mail"
smtp_server = "mail.phpro.org"
smtp_port = 25
smtp_timeout = 30

[logging]
log_level = 200
log_handler = file
log_file = /tmp/web2bb.log

