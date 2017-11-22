<?php
/*
    方倍工作室 http://www.cnblogs.com/txw1958/
    CopyRight 2013 www.doucube.com  All Rights Reserved
*/
include 'file_tool.php';

define("TOKEN", "weixin");
traceHttp();
$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
	
	private function getConn(){
		$db_host = 'w.rdc.sae.sina.com.cn';
		$db_user = '1zow3l354z';
		$db_password = '30mxzllx1x4xyihilww1hjxjx5jjzxiz24ykjzm4';
		$db_name = 'app_dabing6688';
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
							//echo "<br/>";
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
	
	private function importLogFile($fromUsername){	
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


	private function simpleQueryFromView1($fromUsername,$view_name) {
		$lines="";
		$query  =  "SELECT * FROM  ".$view_name." limit 10 ;" ;
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
	
	
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
	
	private function getLogonDetail($wx_user,$table_name){
		//主机名
		$db_host = 'w.rdc.sae.sina.com.cn';
		//用户名
		$db_user = '1zow3l354z';
		//密码
		$db_password = '30mxzllx1x4xyihilww1hjxjx5jjzxiz24ykjzm4';
		//数据库名
		$db_name = 'app_dabing6688';
		//端口
		$db_port = '3306';
		$lines = "";
		//$conn = mysql_connect($db_host,$db_user,$db_password) or die("Invalid query: " . mysql_error());
		//mysql_select_db($db_name, $conn) or die("Invalid query: " . mysql_error());
		$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name) or die('连接数据库失败！');
		if ( mysqli_connect_errno ()) {
			printf ( "Connect failed: %s\n" ,  mysqli_connect_error ());
			exit();
		}
		$dir=$wx_user."/log";
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {			
					if ($file != "." and $file != ".." ){
						//$data = $file;			
						if (file_exists($dir."/".$file)){	 
							$fp = fopen($dir."/".$file, 'r');
							while($r = fgets($fp)) {
								$t = join("','", explode(',',$r));
								//echo $t;
								$sql = "insert into ".substr($file,0,strpos($file,"-"))." values ('$t')";
								//mysql_query($sql);
								$conn -> query ( $sql );	
							}
						}
					}
					//echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
				} 
				closedir($dh);
			}
		}
		$query  =  "SELECT * FROM  ".$table_name ." limit 10";
		$result  =  $conn -> query ( $query );	
		
		while($row  = $result -> fetch_array ( MYSQLI_ASSOC )){       
			$lines=$lines.$row['device_type'].",".$row['device_name'].",".$row['device_udid'].",".$row['device_serial_number'].",".$row['logon_time']."\n";
		}
		
		$result -> free (); 
		mysqli_close($conn);
		return $lines;
	}

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            if($keyword == "?" || $keyword == "？")
            {
                $msgType = "text";
                $contentStr = date("Y-m-d H:i:s",time());
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
				
            }elseif($keyword == "pause"|| $keyword == "暂停" ){
				//from:oR2LbwALAA43VxqMtW0dI1H71AqMto:gh_3a4eea335ecc
				if ($fromUsername=="oR2LbwALAA43VxqMtW0dI1H71AqM"){
									
				$fp_write = fopen($fromUsername."/switch_pause/file1.txt","w");
				//fwrite($fp_write,"pause");
				fwrite($fp_write,"pause".",".date("Y-m-d H:i:s",time()));
				 
				fclose($fp_write);
				$msgType = "text";
                $contentStr = "暂停,等待10秒";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
				}else{
					echo "";
					exit;
				}
			}
			elseif($keyword == "continue"|| $keyword == "继续" ){
				if ($fromUsername=="oR2LbwALAA43VxqMtW0dI1H71AqM"){
				$fp_write = fopen($fromUsername."/switch_pause/file1.txt","w");
				fwrite($fp_write,"continue".",".date("Y-m-d H:i:s",time()));
				fclose($fp_write);
				$msgType = "text";
                $contentStr = "继续跑脚本";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
				}else{
					echo "";
					exit;
				}
			}
			elseif($keyword == "php" )
            {
				$lines="";
				if ($fromUsername=="oR2LbwALAA43VxqMtW0dI1H71AqM"){				
					$msgType = "text";   				
					//$lines=$this->getLogonDetail($fromUsername,"logon_device");
					$lines=$this->simpleQueryFromView1($fromUsername,"logon_device")  ;
					$lines.=",from:".$fromUsername.",to:".$toUsername;					
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $lines);				
					echo $resultStr; 
				}else{
					echo "";
					exit;
				}				
			}else{
				echo "";
				exit;
			}
		}
	}	
}

function traceHttp()
{
		$content = date('Y-m-d H:i:s')."\nREMOTE_ADDR:".$_SERVER["REMOTE_ADDR"]."\nQUERY_STRING:".$_SERVER["QUERY_STRING"]."\n\n";
    
		if (isset($_SERVER['HTTP_APPNAME'])){   //SAE
			sae_set_display_errors(false);
			sae_debug(trim($content));
			sae_set_display_errors(true);
		}else {
			$max_size = 100000;
			$log_filename = "log.txt";
			if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
			file_put_contents($log_filename, $content, FILE_APPEND);
		}
}
?>