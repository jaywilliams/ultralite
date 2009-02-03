<?php

require_once 'ezsql/db.php';
require_once 'ezsql/db.pdo.php';


// This is how to initialse ezsql for sqlite PDO
$db = new ezSQL_pdo();


/*
	Make sure the file is writable, otherwise php won't will error out, 
	and won't be able to add anyting to the database.
*/
$db->connect('sqlite:./pixelpost.sqlite3');


/*
	CREATE  TABLE  IF NOT EXISTS "main"."pixelpost" ("id" INTEGER PRIMARY KEY  NOT NULL , "title" VARCHAR NOT NULL  DEFAULT 'Untitled', "description" TEXT NOT NULL , "filename" VARCHAR NOT NULL , "published" DATETIME NOT NULL  DEFAULT CURRENT_TIMESTAMP)
*/

$db->query("CREATE  TABLE  IF NOT EXISTS 'main'.'pixelpost' ('id' INTEGER PRIMARY KEY  NOT NULL , 'title' VARCHAR NOT NULL  DEFAULT 'Untitled', 'description' TEXT NOT NULL , 'filename' VARCHAR NOT NULL , 'published' DATETIME NOT NULL  DEFAULT CURRENT_TIMESTAMP)");

/*
	INSERT INTO "main"."pixelpost" ("title","description","filename","published") VALUES ('MyTitle','MyDescription','myimage.jpg',CURRENT_TIMESTAMP)
*/
$db->query("INSERT INTO 'main'.'pixelpost' ('title','description','filename','published') VALUES ('MyTitle','MyDescription','myimage.jpg',CURRENT_TIMESTAMP)");


$rows = $db->get_results("SELECT * FROM pixelpost");

var_dump($rows);


?>