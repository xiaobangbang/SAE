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
	
	private function getLogonDetail(){
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

$conn = mysql_connect($db_host,$db_user,$db_password) or die("Invalid query: " . mysql_error());
mysql_select_db($db_name, $conn) or die("Invalid query: " . mysql_error());
//$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name) or die('连接数据库失败！');
/**
if ( mysqli_connect_errno ()) {
         printf ( "Connect failed: %s\n" ,  mysqli_connect_error ());
        exit();
    }
*/	
    //$limit = 10;
    //$query  =  "SELECT * FROM personal_info LIMIT ".$limit ;
    //$result  =  $conn -> query ( $query );	
 
    /* associative array */
    /**
	while($row  = $result -> fetch_array ( MYSQLI_ASSOC )){
        echo $row['pi_id'] . "\t";
        echo $row['pi_name'] . "\t";
        echo $row['pi_tel'] . "\t";
        echo $row['pi_qq'] . "\t";
        echo $row['pi_email'] . "\t";
        echo "<br/>". PHP_EOL;
    }
	*/
    /* free result set */
    //$result -> free (); 
 $data = 'run_log.txt';
 if (file_exists($data)){
	 
 
$fp = fopen($data, 'r');
while($r = fgets($fp)) {
  $t = join("','", explode(',',$r));
  echo $t;
  $sql = "insert into logon_device values ('$t')";
   mysql_query($sql);
}
}
$lines="";
 $result = mysql_query("select * from logon_device");
 //$fp_write = fopen("data.txt","w");
while($row = mysql_fetch_array($result,MYSQL_NUM))
{
   // fwrite($fp_write,$row[0].",".$row[1].",".$row[2].",".$row[3].",".$row[4]."\n");
   $lines=$lines.$row[0].",".$row[1].",".$row[2].",".$row[3].",".$row[4]."\n";
} 
//fclose($fp_write);
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
				$fp_write = fopen("file1.txt","w");
				fwrite($fp_write,"pause");
				fclose($fp_write);
				$msgType = "text";
                $contentStr = "暂停,等待10秒";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
			}
			elseif($keyword == "continue"|| $keyword == "继续" ){
				$fp_write = fopen("file1.txt","w");
				fwrite($fp_write,"continue");
				fclose($fp_write);
				$msgType = "text";
                $contentStr = "继续跑脚本";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
			}
			elseif($keyword == "php" )
            {
				//$lines="";
                $msgType = "text";   
				$fileutil = new FileUtil();				
				
				/**
				$fp_read=fopen("run_log.txt","r"); 
				while(!feof($fp_read))
				{
					$line = fgets($fp_read, 255); 					
					$lines=$lines.$line;
				}
				fclose($fp_read); 
				*/
				$lines=$this->getLogonDetail();
				$lines=$lines.",from:".$fromUsername."to:".$toUsername;
				$fileutil ->moveFile('run_log.txt','wxpay/run_log.txt');
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $lines);
                echo $resultStr;          
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
index_debug.php