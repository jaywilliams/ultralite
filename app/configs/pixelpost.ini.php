; <?php die('Direct Access Forbidden'); ?>

[ultralite]

title      = "My Ultralite Photoblog"
tagline    = "Guess what, it's open source, and it's ultralite!"
url        = "http://192.168.1.200/ultralite/" ; Don't forget the last backslash!

template   = "grayspace"

; The language used on your photoblog:
; Specify Off to disable
locale     = en-us

; Time Zone:
; For a list of supported timezones please see: http://php.net/manual/timezones.php
timezone   = "America/Chicago"

; How may image thumbnails per page:
; Specify Off to disable
pagination = 24

[plugins]

0 = "Example"
1 = "MediaRSS"

[database]

type = "sqlite"

[mysql]

hostname = "localhost"
database = "ultralite"
username = "root"
password = ""

[sqlite]

database = "./pixelpost.sqlite3"