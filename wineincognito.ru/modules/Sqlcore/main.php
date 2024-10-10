<?php
namespace Sqlcore;
if(!defined('IS_XMODULE')){
    exit();
}
require_once 'config.php';
require_once ABS_PATH.'/interface/main.php';
class Main extends \AbstractNS\Main{
    private $dbhndl;
    private $querylog;

    function __construct(){
        parent::__construct();
        $this->querylog = array();
        $this->connect();
    }

    public function connect(){
        if($this->dbhndl){
            mysqli_close($this->dbhndl);
        }
        $this->dbhndl = mysqli_connect(\SQLCORE\DBHOST, \SQLCORE\DBUSER, \SQLCORE\DBPASS);
        if (!$this->dbhndl) {
            die('Can\'t connect to mysql: '.$this->getLastError());
        }
        $db_selected = mysqli_select_db($this->dbhndl, \SQLCORE\DBNAME);
        if (!$db_selected) {
            die ('Can\'t select '.\SQLCORE\DBNAME.': '.$this->getLastError());
        }
        mysqli_autocommit($this->dbhndl, false);
        $this->query("set names 'utf8'");
        $this->query("SET autocommit=0");
        // $this->startTransaction();
    }
    public function query($sql){
        $res = mysqli_query($this->dbhndl, $sql);
        if($res===false){
        //     $this->XM->addMessage($sql.$this->getLastError(), 0, true);
        }
        $this->__log_query($sql);
        return $res;
    }
    // private function startTransaction(){
    //     mysqli_begin_transaction($this->dbhndl, MYSQLI_TRANS_START_READ_WRITE);
    // }
    public function commit(){
        if($this->XM->user->isInReadOnlyMode()){
            return $this->rollback();
        }
        mysqli_commit($this->dbhndl);
        $this->__dump_query_log();
        $this->querylog = array();
        // $this->startTransaction();
    }
    public function rollback(){
        mysqli_rollback($this->dbhndl);
        $this->querylog = array();
        // $this->startTransaction();
    }
    public function getRow($res){
        if(!$res){
            return null;
        }
        return mysqli_fetch_assoc($res);
    }
    public function freeResult($res){
        if($res){
            mysqli_free_result($res);
        }
    }
    public function affectedRows(){
        return mysqli_affected_rows($this->dbhndl);
    }
    public function lastInsertId(){
        return mysqli_insert_id($this->dbhndl);
    }
    public function prepString($str, $len=0){
        $str = preg_replace('#[ \t\r\f]+#u',' ',trim($str, " \t\n\r\0\x0B\xC2\xA0"));
        if($len>0 && mb_strlen($str, 'UTF-8')>$len){
            $str = mb_substr($str, 0, $len, 'UTF-8');
        }
        return mysqli_real_escape_string($this->dbhndl, $str);
    }
    public function checksum($str){
        return (int)crc32($str);
    }
    private $asciialias_charlist = array(
                'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
                'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
                'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
                'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
                'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
                'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
                'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
                'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
                'Ё'=>'Е', 'ё'=>'е',//russian
            );
    public function asciialias($string){
        return trim(preg_replace('#\s+#', ' ', preg_replace('#[^a-z0-9а-я\s]+#u',' ', mb_strtolower(strtr(html_entity_decode((string)$string),$this->asciialias_charlist),'UTF-8'))));
    }
    public function search_engine_alias($string){
        return preg_replace('#(?:^|\s)[^\s]{1,2}(?:\s|$)#', ' ', $this->asciialias($string));
    }
    public function tableExists($tableName){
        $res = $this->query('SELECT 1 FROM information_schema.tables WHERE table_schema = \''.$this->prepString(\SQLCORE\DBNAME).'\' AND table_name = \''.$this->prepString($tableName).'\' LIMIT 1');
        $row = $this->getRow($res);
        $this->freeResult($res);
        return (bool)$row;
    }

    public function getLastError(){
        return mysqli_error($this->dbhndl);
    }

    //query logs
    private function __log_query($query){
        if(!preg_match('#^\s*(update|insert)#iu', $query,$match)){
            return false;
        }
        $action = strtolower($match[1]);
        $last_insert_id = '';
        switch($action){
            case 'update':
                break;
            case 'insert':
                if(preg_match('#\)\s+values\s+\(#', $query)){
                    $last_insert_id = (int)$this->lastInsertId();
                }
                break;
            default:
                return false;
        }
        $tablename = '';
        if(preg_match('#\s*(?:update|insert into) `?([a-z_]+)#iu', $query,$match)){
            $tablename = strtolower($match[1]);
        }
        $this->querylog[] = array($action,$tablename,$this->XM->user->getUserId(),$last_insert_id,$query);
    }
    private function __dump_query_log(){
        if(empty($this->querylog)){
            return;
        }
        foreach($this->querylog as $query){
            @file_put_contents(LOG_DIRECTORY.'/sqllog_'.date('d-m-Y').'.log',
                '['.date('H:i:s').'] pid='.$this->XM->getLogProcessId().' act='.$query[0].' tbl='.$query[1].' uid='.$query[2].' lastid='.$query[3].' "'.$query[4].'"'."\n", FILE_APPEND);
        }
        $this->querylog = array();
    }
}
