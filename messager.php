<?php
// recieve message from client
if(isset($_COOKIE['keyword']) && isset($_POST['c']) && substr($_SERVER['HTTP_REFERER'], 0, 37) === 'https://aaron-os-mineandcraft12.c9.io'){
    if(file_exists('USERFILES/'.$_COOKIE['keyword'].'/aOSpassword.txt')){
        if(isset($_COOKIE['password'])){
            if(!password_verify($_COOKIE['password'], file_get_contents('USERFILES/'.$_COOKIE['keyword'].'/aOSpassword.txt'))){
                echo 'Error - Password incorrect.';
                die();
            }
        }else{
            echo 'Error - Password incorrect.';
            die();
        }
    }
    
    // old message writer, insecure
    //fwrite($file, '{n:"'.$_GET['n'].'",c:"'.join('&gt;', explode('>', join('&lt;', explode('<',$_GET['c'])))).'",l:"'.$filenumber.'"}');
    $messageUsername = 'Anonymous '.substr($_COOKIE['keyword'], 0, 4);
    //if(is_dir('USERFILES/'.$_COOKIE['keyword'])){
    //    if(file_exists('USERFILES/'.$_COOKIE['keyword'].'/APP_MSG_CHATNAME.txt')){
    if(file_exists('messageUsernames/n_'.$_COOKIE['keyword'].'.txt')){
        $usernamefile = fopen('messageUsernames/n_'.$_COOKIE['keyword'].'.txt', 'r');
        $messageUsername = fread($usernamefile, filesize('messageUsernames/n_'.$_COOKIE['keyword'].'.txt'));
        fclose($usernamefile);
    }
    
    $outUsername = join('\'\'', explode('"', join('\\\\', explode('\\', join('&gt;', explode('>', join('&lt;', explode('<', $messageUsername))))))));
    $outMessage = join('&quot;', explode('"', join('\\\\', explode('\\', join('&gt;', explode('>', join('&lt;', explode('<', $_POST['c']))))))));
    $outTime = round(microtime(true) * 1000);
    
    $setting = fopen('setting.txt', 'r');
    $filenumber = strval(intval(fread($setting, filesize('setting.txt'))) + 1);
    fclose($setting);
    
    $lastJSON = file_get_contents('USERFILES/!MESSAGE/m'.($filenumber - 1).'.txt');
    $lastMessage = json_decode($lastJSON);
    if($lastMessage->n == $outUsername && $lastMessage->c == $outMessage){
        echo 'Error - Message identical to previous message.';
        die();
    }
    
    $setting = fopen('setting.txt', 'w');
    fwrite($setting, $filenumber);
    fclose($setting);
    
    $file = fopen('USERFILES/!MESSAGE/m'.$filenumber.'.txt', 'w');
    //$file = fopen('USERFILES/!MESSAGE/m'.date('d_m_Y_H_i_s_').$_GET['n'].'.txt', 'w');
    fwrite($file, '{"n":"'.$outUsername.'","c":"'.$outMessage.'","t":"'.$outTime.'","l":"'.$filenumber.'"}');
    fclose($file);
}else{
    echo 'Error - No user specified, or you are not allowed to send messages from your domain.';
}
?>