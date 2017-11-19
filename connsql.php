<META http-equiv="content-type" content="text/html; charset=UTF-8"> 
<?php

 //header("Content-Type:text/html;charset=UTF-8");
include 'file_tool.php';
class Test
{
	
private function getConn(){
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'test';
$db_port = '3306';
$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name) or die('连接数据库失败！');
if ( mysqli_connect_errno ()) {
         printf ( "Connect failed: %s\n" ,  mysqli_connect_error ());
        exit();
    }
return $conn;
}

private function genSqlFromLogFile($fromUsername){
$sql="";
$dir=$fromUsername."/log";
//$fileutil = new FileUtil();
	if (is_dir($dir)) {
	if ($dh = opendir($dir)) {		
		while (($file = readdir($dh)) !== false) {			
			if ($file != "." and $file != ".." ){			
			if (file_exists($dir."/".$file)){	 
				$fp = fopen($dir."/".$file, 'r');
				$sql .= "insert into ".substr($file,0,strpos($file,"-"))." values ";				
				echo "<br/>";
				while($r = fgets($fp)) {
					$t = join("','", explode(',',$r));					
					$sql .= "('$t')".",";					
				}
				$sql= rtrim($sql, ",").";" ;
				//$fileutil ->moveFile($dir."/".$file,$fromUsername.'/bak_log/'.$file);
			}
			}
		} 
		closedir($dh);
	}
	}
	return $sql;
}	


private function moveLogFile($fromUsername){
	$fileutil = new FileUtil();
	$dir=$fromUsername."/log";					
	if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if ($file != "." and $file != ".." ){					
			if (file_exists($dir."/".$file)){	 				
				$fileutil ->moveFile($dir."/".$file,$fromUsername.'/bak_log/'.$file);
			}
			}
		} 
		closedir($dh);
	}
	}		
}
	
public function importLogFile($fromUsername){
	
	$conn = $this->getConn();
	$this->importLogFile2($conn,$fromUsername);
	mysqli_close($conn);
}


private function importLogFile2($conn,$fromUsername){
	$sql = $this->genSqlFromLogFile($fromUsername);
	$query_e = explode(';',$sql);	
	foreach ($query_e as $k =>$v)
	{
		//echo $query_e[$k];
		if (strlen($query_e[$k]) >1){
			$conn -> query ($query_e[$k]);	
		}
	}
	$this->moveLogFile($fromUsername);
}


public function simpleQueryFromView1($view_name,$fromUsername) {
	$lines="";
	$query  =  "SELECT * FROM  ".$view_name ;
	$conn = $this->getConn();
    $this->importLogFile2($conn,$fromUsername);
	$result  =  $conn -> query ( $query );	
   
	while($row  = $result -> fetch_array ( MYSQLI_ASSOC )){       
		$lines.=$row['device_type'].",".$row['device_name'].",".$row['device_udid'].",".$row['device_serial_number'].",".$row['logon_time']."\n";
    }
	   
    $result -> free (); 
	mysqli_close($conn);
	
	return $lines;
}


}

$test1 = new Test();
$result_str = $test1->simpleQueryFromView1("logon_device","oR2LbwALAA43VxqMtW0dI1H71AqM");	
echo $result_str;
?>