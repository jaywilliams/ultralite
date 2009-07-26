; config file for pagecave 

[application]
default_controller = index
default_action = index
error_controller = error404
error_reporting = E_ALL
display_errors = 1
language = en

[database]
db_name = web2bb_lite
db_hostname = localhost
db_username = username
db_password = password
db_port = 3306

[template]
template_dir = "templates"
cache_dir = "cache"
caching = true
cache_lifetime = 3600

[mail]
admin_mail = admin@example.com
smtp_server = mail.phpro.org 
smtp_port = 25;
x_mailer = "PHPRO.ORG Mail"
smtp_server = "mail.phpro.org"
smtp_port = 25
smtp_timeout = 30
