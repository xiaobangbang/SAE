<?php

//主机名
$db_host = 'localhost';
//用户名
$db_user = 'root';
//密码
$db_password = 'root';
//数据库名
$db_name = 'test';
//端口
$db_port = '3306';

$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name) or die('连接数据库失败！');


/**
if ( mysqli_connect_errno ()) {
         printf ( "Connect failed: %s\n" ,  mysqli_connect_error ());
        exit();
    }
*/	
    $limit = 10;
    $query  =  "SELECT * FROM personal_info LIMIT ".$limit ;
    $result  =  $conn -> query ( $query );
 
    /* associative array */
    while($row  = $result -> fetch_array ( MYSQLI_ASSOC )){
        echo $row['pi_id'] . "\t";
        echo $row['pi_name'] . "\t";
        echo $row['pi_tel'] . "\t";
        echo $row['pi_qq'] . "\t";
        echo $row['pi_email'] . "\t";
        echo "<br/>". PHP_EOL;
    }
 
    /* free result set */
    $result -> free ();
 
 /* close connection */
    $conn -> close ();
	
	
$path = "/home/httpd/html/index.php"; 
$file = basename($path,".php"); // $file is set to "index" 

$path = "/sdfsf/ssdf/etc/passwd"; 
$file = dirname($path); // $file is set to "/etc" 

echo $file;


$pathinfo = pathinfo("www/test/index.html"); 
var_dump($pathinfo); 

echo '<br/>';
echo $pathinfo['dirname'];
echo '<br/>';
echo $pathinfo['basename'];
echo '<br/>';
echo $pathinfo['extension'];
echo '<br/>';
echo $pathinfo['filename'];
echo '<br/>';

echo filetype('csv_monitor.php'); // file 
echo '<br/>';
echo filetype('..'); // dir 

include 'file_tool.php';

$create1 = new FileUtil();

//$create1 ->createDir('a/1/2/3');

//$create1 ->createFile('b/1/2/3');                    //测试建立文件        在b/1/2/文件夹下面建一个3文件
 //$create1 ->createFile('b/1/2/3.exe');             //测试建立文件        在b/1/2/文件夹下面建一个3.exe文件
 //$create1 ->copyDir('b','d/e');                    //测试复制文件夹 建立一个d/e文件夹，把b文件夹下的内容复制进去
//$create1 ->copyFile('b/1/2/3.exe','b/b/3.exe'); //测试复制文件        建立一个b/b文件夹，并把b/1/2文件夹中的3.exe文件复制进去
 //$create1 ->moveDir('a/','b/c');                   // 测试移动文件夹 建立一个b/c文件夹,并把a文件夹下的内容移动进去，并删除a文件夹
 $create1 ->moveFile('b/1/2/3.exe','b/d/3.exe'); //测试移动文件        建立一个b/d文件夹，并把b/1/2中的3.exe移动进去                   
 //$create1 ->unlinkFile('b/d/3.exe');             //测试删除文件        删除b/d/3.exe文件
 //$create1 ->unlinkDir('d');          
 

?>