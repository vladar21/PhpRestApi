<?php

$pdo = new PDO(
    "mysql:host=localhost;",
    $user='root',
    $pass='root',
    array( PDO::ATTR_PERSISTENT => true )
);

$cmd1 = "create database moviesdb;";
$cmd2 = "use moviesdb;";
$cmd3 = "create table movies(
    id int not null auto_increment primary key,
    title varchar(128),
    director varchar(128) null,
    year int null,
    liked int null,
    dislike int null,
    poster varchar(300) null) default charset = 'utf8';";
$cmd4 = "insert into movies(title, director, year, liked, dislike) values
('Once Upon a Time in Hollywood', 'Quentin Tarantino','2019','100','5'),
('Bright Star', 'Ben Whishaw', '2009', '200','10'),
('The Dark Knight', 'Christopher Nolan', '2008', '300', '15'),
('Fahrenheit 9/11', 'Michael Moore', '2004', '400', '100'),
('Private Life', 'Kathryn Hahn', '2018', '500', '300');";

try{
    $pdo->query($cmd1);
}
catch(Exception $e){
    echo 'create db error:'.$e.'<br>';
}

try{
    $pdo->query($cmd2);
}
catch(Exception $e){
    echo 'select db error:'.$e.'<br>';
}

try {
    $pdo->query($cmd3);
}
catch(Exception $e){
    echo 'create table error:'.$e.'<br>';
}

try {
    $pdo->query($cmd4);
}
catch(Exception $e){
    echo 'create table error:'.$e.'<br>';
}

?>
