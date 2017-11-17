<?php
$conn = mysql_connect('127.0.0.1','root','root') or die("Invalid query: " . mysql_error());
mysql_select_db('test', $conn) or die("Invalid query: " . mysql_error());
 
//$content = file_get_contents("abc.txt");
//$contents= explode("\n",$content);//explode()函数以","为标识符进行拆分

//echo  $contents;

echo "<br/>";

$data = 'abc.txt';
$fp = fopen($data, 'r');
while($r = fgets($fp)) {
  $t = join("','", explode(',', $r));
  $sql = "insert into serial_number(serial_number,msg2) values ('$t')";
   mysql_query($sql);
}


 $result = mysql_query("select * from serial_number");
 $fp_write = fopen("data.txt","w");
while($row = mysql_fetch_array($result,MYSQL_NUM))
{
    fwrite($fp_write,$row[0].",".$row[1].",".$row[2]);
} 
fclose($fp_write);
 
//read from file
$fp_read=fopen("data.txt","r"); 
while(!feof($fp_read))
{
    $line = fgets($fp_read, 255); 
    echo $line;
}
fclose($fp_read);
 

?>
