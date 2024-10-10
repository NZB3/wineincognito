<?php
namespace Sendmail;
if(!defined('IS_XMODULE')){
    exit();
}
require_once 'config.php';
require_once 'phpmailer.php';
require_once ABS_PATH.'/interface/main.php';
class Main extends \AbstractNS\Main{
    private $email;
    private $subject;
    private $loglevel = 0;

    function __construct(){
        parent::__construct();
        $this->email = new PHPMailer();
        // $this->email->IsSMTP();
        // $this->email->SMTPAuth  = true;
        // $this->email->Host      = \SENDMAIL\SMTP_HOST;
        // $this->email->Port      = \SENDMAIL\SMTP_PORT;
        // $this->email->Username  = \SENDMAIL\EMAIL_ADDRESS;
        // $this->email->Password  = \SENDMAIL\EMAIL_PASS;

        $this->email->From      = \SENDMAIL\FROM;
        $this->email->addReplyTo(\SENDMAIL\FROM, \SENDMAIL\FROM_NAME);
        $this->email->FromName  = \SENDMAIL\FROM_NAME;
        $this->email->CharSet   = 'utf-8';

        // $this->email->SMTPDebug = 3;
    }

    public function setLogLevel($loglevel){
        $this->loglevel = (int)$loglevel;
        return true;
    }

    public function addAddress($email, $name = ''){
        $this->email->AddAddress($email,$name);
    }

    public function reset(){
        $this->email->clearAllRecipients();
        $this->email->clearAttachments();
        $this->email->clearCustomHeaders();
        $this->setSubject(langTranslate('sendmail','sendmail','Default Subject','Wine Incognito'));
        $this->email->Body = '';
        $this->email->isHTML(false);
    }

    public function setSubject($subject){
        $this->email->Subject   = '=?UTF-8?B?'.base64_encode(trim($subject)).'?=';
    }

    public function setBody($message, $is_html = FALSE, $title = null, $full = true, $default_wrapper = false){
        if($is_html){
            $this->email->msgHTML($this->XM->sendmail->get_wrapped_letter($message,$full,$title,$default_wrapper));
            // $this->email->msgHTML($message);
        } else {
            $this->email->Body = $message;
        }
    }

    public function addFileAttachment($filepath,$name){
        $this->email->AddAttachment($filepath, $name);
    }

    public function addStringAttachment($content,$name){
        $this->email->addStringAttachment($content, $name);
    }

    public function get_wrapped_letter($content,$full,$title,$default_wrapper){
        $mail_settings = $default_wrapper?$this->XM->user->get_default_mail_settings():$this->XM->user->get_mail_settings($this->XM->user->getCompanyId());
        $letter = $this->XM->view->load('sendmail/wrap',array('content'=>$content, 'header_logo_url'=>$mail_settings['header_logo_url'], 'footer_logo_url'=>$mail_settings['footer_logo_url'], 'header_background_color'=>$mail_settings['header_background_color'], 'footer_background_color'=>$mail_settings['footer_background_color'], 'full'=>$full, 'title'=>$title),true);
        if($mail_settings['text_color']!='4d4d4d'){
            $letter = preg_replace('#([^a-z]color:\s*\#)4d4d4d#i', '${1}'.$mail_settings['text_color'], $letter);    
        }
        if($mail_settings['anchor_color']!='f46c31'){
            $letter = preg_replace('#([^a-z]color:\s*\#)f46c31#i', '${1}'.$mail_settings['anchor_color'], $letter);
        }
        return $letter;
    }

    public function send(){
        // if(!$this->email->Send()){
        //     $err = $this->email->ErrorInfo;
        //     $this->reset();
        //     return FALSE;
        // }
        // $this->reset();
        // return TRUE;
        if(!$this->email->preSend()){
            return false;
        }
        return $this->__custom_mail($this->email->getToAddresses(),$this->email->Subject,$this->email->getMIMEBody(),$this->email->getMIMEHeader());
    }

    //custom mail
    private function __custom_mail($recipients,$subject,$message,$additional_headers = ''){
        return true;
    }

}