<?php
$pid = getmypid();
$Root = $_SERVER['DOCUMENT_ROOT']?$_SERVER['DOCUMENT_ROOT']:dirname(dirname(__FILE__)).'/';
require_once $Root . '/confing/common.php';

global $conf;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once $Root . '/PHPMailer/src/Exception.php';
require_once $Root . '/PHPMailer/src/PHPMailer.php';
require_once $Root . '/PHPMailer/src/SMTP.php';

$php_Self = substr($_SERVER['PHP_SELF'], strripos($_SERVER['PHP_SELF'], "/") + 1);
if ($php_Self !== "fs.php") {
    $msg = '%E6%96%87%E4%BB%B6%E9%94%99%E8%AF%AF';
    $msg = urldecode($msg);
    exit(json_encode(['code' => -1, 'msg' => $msg], JSON_UNESCAPED_UNICODE));
}

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");

$mail = new PHPMailer(true); 
$mail->CharSet = "UTF-8"; // Set email encoding
$mail->Mailer = 'SMTP';
$mail->SMTPDebug = 2;
$mail->isSMTP(); // Use SMTP
$mail->Host = $conf["smtp_host"]; // SMTP server
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = $conf["smtp_user"]; // SMTP username
$mail->Password = $conf["smtp_pass"]; // SMTP password
$mail->SMTPSecure = $conf["smtp_secure"]; // Enable TLS or SSL protocol
$mail->Port = $conf['smtp_port']; // Server port 25 or 465 (depends on SMTP server)

function sendMail($mail, $conf, $email, $f_email, $f_title, $f_content, $j_email,$j_name)
{
                    
    global $pid;
    global $DB;
    global $date;
    global $conf;
    try {
        
        $mail->setFrom($f_email, $conf["sitename"]); // Sender email
        
        $mail->addAddress($j_email, $j_name); // Recipient email
        
        $mail->addReplyTo($f_email, $conf["sitename"].' 管理员'); // Reply-to email

        $mail->isHTML(true); // Set content as HTML format
        $mail->Subject = $f_title;
        $mail->Body = $f_content;
        $mail->AltBody = $f_content; // Show this content if the email client does not support HTML
    
        $DB->query("update qingka_wangke_emails set status=1,endtime='{$date}',cpid='{$pid}',status_t='' where eid='{$email['eid']}' ");
       
        $mail->send();
        // 发送间隔
        sleep(10);
    } catch (Exception $e) {
        $error = $mail->ErrorInfo;
        $DB->query("update qingka_wangke_emails set status=-1,endtime='{$date}',status_t='{$error}',cpid='{$pid}' where eid='{$email['eid']}' ");
        sleep(600);
    }
    
    $mail->clearAddresses();
    $mail->ClearAllRecipients(); 
}
function go(){
    global $pid;
    global $DB;
    global $date;
    global $conf;
    global $mail;
    
    $emailsResult = $DB->query("SELECT * FROM qingka_wangke_emails where status!=1 ");

    if(!empty($emailsResult)){
        $emails = [];
        while ($row = $DB->fetch($emailsResult)) {
            $emails[]=$row;
        }
        $lastKey = end(array_keys($emails));// 最后一个
        
        foreach ($emails as $key => $email) {
            
            $user = $DB->get_row("select name,user,uid from qingka_wangke_user where uid='{$email['uid']}' limit 1 ");
            
            if(!empty(count($emails))){
                
                $aa = trim($emails["j"]) == '123456@qq.com' || trim($emails["j"]) == '1@qq.com';
        
                if (empty((float)$email["j"]) || $aa) {
                    // 若是奇怪的账号
                    $j_email =  $email["j"];
                    sendMail($mail, $conf, $email, $email['f'], $email['f_t'], $email['f_c'], $j_email,$user["name"]);
                    if($user['uid'] != 1){
                        $admin_info = $DB->get_row("select qq from qingka_wangke_user where uid=1 limit 1");
                        sendMail($mail, $conf,$email, $email['f'], $email['f_t'], $email['f_c'],$admin_info['qq'].'@qq.com', $conf["sitename"].' 管理员');
                    }
            
                } else {
                    // 如果是正常的账号
                    $j_email =  $email["j"];
                        // sendMail($mail, $conf, $email, $f_email, $f_title, $f_content, $j_email,$j_name)
                        
                        // $email['f_c'] = $lastKey.'我'.$email['f_c'];
                        // $DB->query("update qingka_wangke_emails set f_c='{$email['f_c']}' where eid='{$email['eid']}' ");
            
                        sendMail($mail, $conf, $email, $email['f'], $email['f_t'], $email['f_c'], $j_email,$user["name"]);
                        if( $user['uid'] != 1 ){
                            $admin_info = $DB->get_row("select qq from qingka_wangke_user where uid=1 limit 1");
                            sendMail($mail, $conf,$email, $email['f'], $email['f_t'], $email['f_c'],$admin_info['qq'].'@qq.com', $conf["sitename"].' 管理员');
                        }
                    
                }
            }
        
            $sCount = $DB->count("select count(*) from qingka_wangke_emails where status!=1");
            
            // if($sCount<=0){
            //     posix_kill($pid, SIGTERM);
            //     exec("taskkill /F /PID $pid");
            //     exit();
            // }
            if ($key === $lastKey  ) {
                if($sCount>0){
                    go();
                }
            }
            
        }
    }else{
        posix_kill($pid, SIGTERM);
        exec("taskkill /F /PID $pid");
        exit();
    }
    
};
go();


?>