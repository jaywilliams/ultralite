# <?php @header("Status: 403"); exit("Access denied."); ?>
---
sql: 
  host: "localhost"
  username: 
  password: 
  database: "./mydb.sqlite3"
  prefix: 
  adapter: "sqlite"
name: "My Ultralite Photoblog"
description: "Guess what, it's open source, and it's ultralite!"
url: "http://192.168.1.200/ultralite"
feed_url: 
email: "user@domain.com"
locale: "en_US"
theme: "default"
posts_per_page: 5
feed_items: 20
clean_urls: true
post_url: "(year)/(month)/(day)/(url)/"
timezone: "America/Chicago"
can_register: true
uploads_path: "/images/"
enabled_modules: 
  0: "markdown"
  2: "smartypants"
  3: "tags"
  5: "swfupload"
  6: "comments"
enabled_feathers: 
  - "photo"
routes: 
  tag/(name)/: "tag"
secure_hashkey: "090cf52270f04c28b7bb0fab1e93d425"
default_comment_status: "denied"
allowed_comment_html: 
  - "strong"
  - "em"
  - "blockquote"
  - "code"
  - "pre"
  - "a"
comments_per_page: 25
defensio_api_key: 
auto_reload_comments: 30
enable_reload_comments: false
my-bool: false
