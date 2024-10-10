<?php
namespace Lang;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/main.php';
class Main extends \AbstractNS\Main{
    protected $currLangId;//lang_id
    protected $defaultModule;
    protected $defaultGroup;
    protected $translations;
    private $__langCodes;

    function __construct(){
        parent::__construct();
        $this->defaultModule = $this->defaultGroup = null;
        $this->translations = array();

        $this->currLangId = null;
        $this->__langCodes = null;
    }
    private function __reloadLangCache(){
        $languageCodes = array();
        $res = $this->XM->sqlcore->query('SELECT lang_id,lang_code FROM language');
        while($row = $this->XM->sqlcore->getRow($res)){
            $langId = (int)$row['lang_id'];
            $langCode = strtolower(trim($row['lang_code']));
            $languageCodes[$langId] = $langCode;
        }
        $this->XM->sqlcore->freeResult($res);
        $res = @file_put_contents(ABS_PATH.'/modules/Lang/cache/langCache.ag.php', $this->XM->generateArrayInclude(array('lc'=>$languageCodes)));
        if(!$res){
            $this->XM->addMessage('Can\'t save file: '.ABS_PATH.'/modules/Lang/cache/langCache.ag.php', 0, true);
            return false;
        }
        return true;
    }
    public function &getLangCodes(){
        if($this->__langCodes===null){
            $lc = array();
            if(!file_exists(ABS_PATH.'/modules/Lang/cache/langCache.ag.php') && !$this->__reloadLangCache()){
                exit();
            }
            include ABS_PATH.'/modules/Lang/cache/langCache.ag.php';    
            $this->__langCodes = $lc;
        }
        
        return $this->__langCodes;
    }
    public function setCurrLangByCode($lang){
        $lang = strtolower(trim($lang));
        $langCodes = &$this->getLangCodes();
        foreach($langCodes as $langId=>$langCode){
            if($langCode==$lang){
                $this->setLang($langId);
                return true;
            }
        }
        return false;
    }
    public function setLang($langId){
        $this->currLangId = $langId;
        $this->XM->setSessionVar('Lang.currLangId',$langId);
    }
    protected $persistentLangId;
    public function setTempLang($langId){
        $this->persistentLangId = $this->getCurrLangId();
        $this->currLangId = (int)$langId;
    }
    public function revertTempLang(){
        if(!$this->persistentLangId){
            return;
        }
        $this->currLangId = $this->persistentLangId;
    }
    public function getCurrLangId(){
        if($this->currLangId!==null){
            return $this->currLangId;
        }
        $langId = $this->XM->getSessionVar('Lang.currLangId');
        if($langId!==null){
            $this->currLangId = (int)$langId;
            return $this->currLangId;
        }
        $this->setLang(\LANG\DEFAULT_LANG_ID);
        return $this->currLangId;
    }
    public function getLanguageList(){
        $res = $this->XM->sqlcore->query('SELECT lang.lang_id, COALESCE(lang_ml.lang_name,lang.lang_code) as lang_name
            FROM `language` as lang 
            LEFT JOIN (select lang_id,SUBSTRING_INDEX(GROUP_CONCAT(lang order by lang = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as lang from language_ml group by lang_id) as ln_glue on ln_glue.lang_id = lang.lang_id
            LEFT JOIN language_ml as lang_ml on lang_ml.lang_id = ln_glue.lang_id and lang_ml.lang = ln_glue.lang');
        $languageList = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $languageList[] = array('id'=>(int)$row['lang_id'],'name'=>$row['lang_name']);
        }
        $this->XM->sqlcore->freeResult($res);
        return $languageList;
    }
    public function getLanguageListForWrap(){
        $res = $this->XM->sqlcore->query('SELECT lang.lang_id, lang.lang_code, COALESCE(lang_ml.lang_name,lang.lang_code) as lang_name
            FROM `language` as lang 
            LEFT JOIN (select lang_id,SUBSTRING_INDEX(GROUP_CONCAT(lang order by lang = lang_id desc),\',\',1) as lang from language_ml group by lang_id) as ln_glue on ln_glue.lang_id = lang.lang_id
            LEFT JOIN language_ml as lang_ml on lang_ml.lang_id = ln_glue.lang_id and lang_ml.lang = ln_glue.lang');
        $languageList = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $id = (int)$row['lang_id'];
            $languageList[] = array('id'=>$id,'code'=>$row['lang_code'],'name'=>$row['lang_name'],'current'=>($this->XM->lang->getCurrLangId()==$id));
        }
        $this->XM->sqlcore->freeResult($res);
        return $languageList;
    }
    public function getLanguageIdList(){
        $res = $this->XM->sqlcore->query('SELECT lang_id FROM `language`');
        $languageIdList = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $languageIdList[] = (int)$row['lang_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        return $languageIdList;
    }
    public function getInterfaceTranslationModuleList(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            return array();
        }
        $res = $this->XM->sqlcore->query('SELECT lm_id,lm_caption FROM language_module order by lm_caption asc');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['lm_id'],
                    'name'=>(string)$row['lm_caption'],
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function getInterfaceTranslationGroupList($module_id){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            return array();
        }
        $module_id = (int)$module_id;
        $res = $this->XM->sqlcore->query('SELECT lmg_id,lmg_caption FROM language_module_group WHERE lm_id = '.$module_id.' order by lmg_caption asc');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['lmg_id'],
                    'name'=>(string)$row['lmg_caption'],
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function getInterfaceTranslationStringsForAllLanguages($group_id){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            return array();
        }
        $group_id = (int)$group_id;
        $res = $this->XM->sqlcore->query('SELECT language_string.ls_id, language_string.ls_string, `language`.lang_id, COALESCE(language_string_ml.ls_ml_translation,\'\') as ls_ml_translation
            FROM language_string
            inner join `language` on 1=1
            left join language_string_ml on language_string_ml.ls_id = language_string.ls_id and language_string_ml.lang_id = `language`.lang_id
            where  language_string.lmg_id = '.$group_id.'
            order by language_string.ls_string asc, `language`.lang_id asc');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $id = (int)$row['ls_id'];
            if(!isset($result[$id])){
                $result[$id] = array(
                        'id'=>$id,
                        'string'=>(string)$row['ls_string'],
                        'translation'=>array(),
                    );
            }
            $result[$id]['translation'][(int)$row['lang_id']] = (string)$row['ls_ml_translation'];
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function edit_interface_translation($string_id, $lang_id, $translation, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $string_id = (int)$string_id;
        $lang_id = (int)$lang_id;
        $translation = preg_replace('#\s+#u',' ',trim($translation));
        if(mb_strlen($translation,'UTF-8')>256){
            $err = formatReplace(langTranslate('lang', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                langTranslate('lang', 'list', 'Translation', 'Translation'),
                256);
            return false;
        }

        $res = $this->XM->sqlcore->query('SELECT language_string.ls_id, `language`.lang_id, language_string.lmg_id, language_string_ml.ls_ml_id, language_string_ml.ls_ml_translation
            FROM language_string
            left join `language` on `language`.lang_id = '.$lang_id.'
            left join language_string_ml on language_string_ml.ls_id = language_string.ls_id and language_string_ml.lang_id = `language`.lang_id
            where  language_string.ls_id = '.$string_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('lang','err','Variable not found','Variable not found');
            return false;
        }
        if(!$row['lang_id']){
            $err = langTranslate('lang','err','Invalid language','Invalid language');
            return false;
        }
        $ls_ml_id = (int)$row['ls_ml_id'];
        $group_id = (int)$row['lmg_id'];
        if($ls_ml_id){
            if($row['ls_ml_translation']==$translation){
                return true;
            }
            $this->XM->sqlcore->query('UPDATE language_string_ml SET ls_ml_translation = \''.$this->XM->sqlcore->prepString($translation,256).'\' where ls_ml_id = '.$ls_ml_id);
            if(!$this->__update_translation_cache($group_id, $lang_id, $err)){
                return false;
            }
            $this->XM->sqlcore->commit();
            return true;
        } else {
            $this->XM->sqlcore->query('INSERT INTO language_string_ml (ls_id,lang_id,ls_ml_translation) VALUES ('.$string_id.', '.$lang_id.', \''.$this->XM->sqlcore->prepString($translation,256).'\')');
            if(!$this->__update_translation_cache($group_id, $lang_id, $err)){
                return false;
            }
            $this->XM->sqlcore->commit();
            return true;
        }
        return false;//never
    }
    






    public function translate(){
        $args = func_get_args();
        $argscount = count($args);
        if(!$argscount){
            return null;
        }
        $translation = null;
        switch($argscount){
            case 2:
                $module = $this->defaultModule;
                $group = $this->defaultGroup;
                $var = (string)$args[0];
                $default = (string)$args[1];
                $translation = $this->__translate($module, $group, $var, $this->XM->lang->getCurrLangId(),$default);
                break;
            case 3:
                $module = $this->defaultModule;
                $group = strtolower((string)$args[0]);
                $var = (string)$args[1];
                $default = (string)$args[2];
                $translation = $this->__translate($module, $group, $var, $this->XM->lang->getCurrLangId(),$default);
                break;
            case 4:
                $module = $this->XM->getModuleCaseSensitiveName((string)$args[0], false);
                $group = strtolower((string)$args[1]);
                $var = (string)$args[2];
                $default = (string)$args[3];
                $translation = $this->__translate($module, $group, $var, $this->XM->lang->getCurrLangId(),$default);
                break;
            case 5:
                $langId = (int)$args[0];
                $module = $this->XM->getModuleCaseSensitiveName((string)$args[1], false);
                $group = strtolower((string)$args[2]);
                $var = (string)$args[3];
                $default = (string)$args[4];
                $translation = $this->__translate($module, $group, $var, $langId,$default);
                break;
            default:
        }
        if($translation!==null){
            return htmlentities($translation);
        }
        return htmlentities($args[count($args)-1]);
    }

    protected function __getTranslations($module, $group, $langId){
        $t = array();
        $filename = ABS_PATH.'/modules/'.$module.'/lang/'.$group.'/'.$langId.'.php';
        if(file_exists($filename)){
            include $filename;
        }
        return $t;
    }

    protected function __translate($module, $group, $var, $langId, $default=null){
        if(!$module || !$group || !$var || strlen($module)>64 || strlen($group)>64){
            return null;
        }
        if(!isset($this->translations[$module])){
            $this->translations[$module] = array();
        }
        if(!isset($this->translations[$module][$group])){
            $this->translations[$module][$group] = array();
        }
        if(!isset($this->translations[$module][$group][$langId])){
            $this->translations[$module][$group][$langId] = $this->__getTranslations($module, $group, $langId);
        }
        if(isset($this->translations[$module][$group][$langId][$var])){
            return $this->translations[$module][$group][$langId][$var];
        }
        //translation not found
        $this->__insert_translation($module, $group, $var, $langId, $default);
        if(isset($this->translations[$module][$group][$langId][$var])){
            return $this->translations[$module][$group][$langId][$var];
        }
        return null;
    }

    protected function __insert_translation($module, $group, $var, $lang_id, $default){
        $module = strtolower($module);
        $group = strtolower($group);
        $lang_id = (int)$lang_id;
        if(mb_strlen($var,'UTF-8')>256){
            return;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 from language where lang_id = '.$lang_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//invalid language
            return;
        }
        $res = $this->XM->sqlcore->query('SELECT language_module.lm_id, language_module_group.lmg_id, language_string.ls_id, language_string_ml.ls_ml_id 
            from language_module
            left join language_module_group on language_module_group.lm_id = language_module.lm_id and language_module_group.lmg_checksum = '.$this->XM->sqlcore->checksum($group).' and language_module_group.lmg_caption = \''.$this->XM->sqlcore->prepString($group,64).'\'
            left join language_string on language_string.lmg_id = language_module_group.lmg_id and language_string.ls_checksum = '.$this->XM->sqlcore->checksum($var).' and language_string.ls_string = \''.$this->XM->sqlcore->prepString($var,256).'\'
            left join language_string_ml on language_string_ml.ls_id = language_string.ls_id and language_string_ml.lang_id = '.$lang_id.'
            where language_module.lm_checksum = '.$this->XM->sqlcore->checksum($module).' and language_module.lm_caption = \''.$this->XM->sqlcore->prepString($module,64).'\'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $row = array('lm_id'=>null,'lmg_id'=>null,'ls_id'=>null,'ls_ml_id'=>null);
        }
        $lm_id = (int)$row['lm_id'];
        if(!$lm_id){
            $this->XM->sqlcore->query('INSERT INTO language_module (lm_checksum,lm_caption) VALUES ('.$this->XM->sqlcore->checksum($module).',\''.$this->XM->sqlcore->prepString($module,64).'\')');
            $lm_id = $this->XM->sqlcore->lastInsertId();
            $this->XM->sqlcore->commit();
        }
        $lmg_id = (int)$row['lmg_id'];
        if(!$lmg_id){
            $this->XM->sqlcore->query('INSERT INTO language_module_group (lm_id,lmg_checksum,lmg_caption) VALUES ('.$lm_id.','.$this->XM->sqlcore->checksum($group).',\''.$this->XM->sqlcore->prepString($group,64).'\')');
            $lmg_id = $this->XM->sqlcore->lastInsertId();
            $this->XM->sqlcore->commit();
        }
        $ls_id = (int)$row['ls_id'];
        if(!$ls_id){
            $this->XM->sqlcore->query('INSERT INTO language_string (lmg_id,ls_checksum,ls_string) VALUES ('.$lmg_id.','.$this->XM->sqlcore->checksum($var).',\''.$this->XM->sqlcore->prepString($var,256).'\')');
            $ls_id = $this->XM->sqlcore->lastInsertId();
            $this->XM->sqlcore->commit();
        }
        $ls_ml_id = (int)$row['ls_ml_id'];
        if(!$ls_ml_id){
            if(mb_strlen($default,'UTF-8')>256 || !strlen($default)){
                $default = $var;
            }
            $this->XM->sqlcore->query('INSERT INTO language_string_ml (ls_id,lang_id,ls_ml_translation) 
                SELECT '.$ls_id.' as ls_id, language.lang_id, \''.$this->XM->sqlcore->prepString($default,256).'\' as ls_ml_translation 
                    from language
                    left join language_string_ml on language_string_ml.ls_id = '.$ls_id.' and language_string_ml.lang_id = language.lang_id
                    where language_string_ml.ls_ml_id is null');
            $this->XM->sqlcore->commit();
        }
        $err = null;
        $this->__update_translation_cache($lmg_id, $lang_id, $err);
        return;
    }

    protected function __update_translation_cache($lmg_id, $lang_id, &$err){
        $lmg_id = (int)$lmg_id;
        $lang_id = (int)$lang_id;
        $res = $this->XM->sqlcore->query('SELECT language_module.lm_caption as mdl,language_module_group.lmg_caption as grp
            from language_module_group
            inner join language_module on language_module.lm_id = language_module_group.lm_id
            where language_module_group.lmg_id = '.$lmg_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//never
            $err = langTranslate('lang','err','Invalid language_module_group_id','Invalid language_module_group_id');
            return false;
        }
        $module = $this->XM->getModuleCaseSensitiveName((string)$row['mdl']);
        $group = (string)$row['grp'];
        if(!$module || !$group){//never
            $err = langTranslate('lang','err','Empty module or group name','Empty module or group name');
            return false;
        }
        
        $translations = array();
        $res = $this->XM->sqlcore->query('SELECT language_string.ls_string as str,language_string_ml.ls_ml_translation as val
            from language_string
            inner join language_string_ml on language_string_ml.ls_id = language_string.ls_id and language_string_ml.lang_id = '.$lang_id.'
            where language_string.lmg_id = '.$lmg_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $translations[$row['str']]=$row['val'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(empty($translations)){//never
            $err = langTranslate('lang','err','Translations not found','Translations not found');
            return false;
        }
        $this->translations[$module][$group][$lang_id] = $translations;

        if(!is_dir(ABS_PATH.'/modules/'.$module.'/lang/'.$group.'/')){
            if(!@mkdir(ABS_PATH.'/modules/'.$module.'/lang/'.$group.'/',0755,true)){
                $err = formatReplace(langTranslate('lang','err','Error creating directory: @1','Error creating directory: @1'),ABS_PATH.'/modules/'.$module.'/lang/'.$group.'/');
                return false;
            }
        }
        if(!@file_put_contents(ABS_PATH.'/modules/'.$module.'/lang/'.$group.'/'.$lang_id.'.php', $this->XM->generateArrayInclude(array('t'=>$translations)))){
            $err = formatReplace(langTranslate('lang','err','Error saving file: @1','Error saving file: @1'),ABS_PATH.'/modules/'.$module.'/lang/'.$group.'/'.$lang_id.'.php');
            return false;
        }
        return true;
    }

    public function setDefault($module, $group = null){
        $this->defaultModule = $this->XM->getModuleCaseSensitiveName((string)$module, false);
        if($group){
            $this->defaultGroup = strtolower((string)$group);
        }
        return true;
    }
    public function clean($module, $group = null){
        $module = $this->XM->getModuleCaseSensitiveName($module, false);
        if(!$module){
            return;
        }
        if($group){
            $group = strtolower($group);
            if(isset($this->translations[$module][$group])){
                unset($this->translations[$module][$group]);
            }
        } else {
            if(isset($this->translations[$module])){
                unset($this->translations[$module]);
            }
        }
    }
}
