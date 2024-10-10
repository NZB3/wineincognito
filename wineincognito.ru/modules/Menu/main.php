<?php
namespace Menu;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/main.php';

class Main extends \AbstractNS\Main{
    public function getMenuHtml(){
        $menurows = $this->__getMenuRows(false);
        return $this->__recursiveMenuHTMLBuilder($menurows,0);
    }
    public function getSubMenuHtml(){
        $menurows = $this->__getMenuRows(true);
        foreach($menurows as $key=>$menurow){
            if($menurow['id']==3){
                if($fullname = $this->XM->user->getFullName()){
                    $menurows[$key]['caption'] = $fullname;
                }
                break;
            }
        }
        return $this->__recursiveMenuHTMLBuilder($menurows,0);
    }
    private function __recursiveMenuHTMLBuilder(&$menuitems,$parent,$maxdepth=1,$depth=0){
        if($depth>$maxdepth){
            return '';
        }
        $result = '';
        foreach($menuitems as $menuitem){
            if($menuitem['parent_id']!=$parent){
                continue;
            }
            if($menuitem['id']==36 && $this->XM->user->getUserId()==95){//temp hide approve menu item from user with product autoapprove
                continue;
            }
            if($menuitem['url']===null){
                $children = $this->__recursiveMenuHTMLBuilder($menuitems,$menuitem['id'],$maxdepth,$depth+1);
                if(strlen($children)==0){
                    continue;
                }
                $result .= '<li'.($menuitem['is_in_submenu']?' class="is-in-submenu"':'').'><span>'.htmlentities($menuitem['caption']).'</span>'.$children.'</li>';
            } else {
                $result .= '<li'.($menuitem['is_in_submenu']?' class="is-in-submenu"':'').'><a href="'.BASE_URL.$menuitem['url'].'">'.htmlentities($menuitem['caption']).'</a>'.$this->__recursiveMenuHTMLBuilder($menuitems,$menuitem['id'],$maxdepth,$depth+1).'</li>';    
            }
        }
        if(!strlen($result)){
            return '';
        }
        return '<ul>'.$result.'</ul>';
    }
    

    private function __getMenuRows($only_submenu){
        $blocked = array();
        $last_blocked = 0;
        $res = $this->XM->sqlcore->query('SELECT menu_access.menu_id,menu_access.ma_type,menu_access.ma_key,menu_access.ma_value 
            from menu_access 
            inner join menu on menu.menu_id = menu_access.menu_id and menu.menu_visible = 1 '.($only_submenu?'and menu.menu_is_in_submenu = 1':'').'
            order by menu_access.menu_id asc, menu_access.ma_type asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $menu_id = (int)$row['menu_id'];
            if($menu_id === $last_blocked){ 
                continue;
            }
            switch((int)$row['ma_type']){
                case 0:
                    if($this->XM->user->check_state((int)$row['ma_key'])!==(bool)$row['ma_value']){
                        $blocked[] = $last_blocked = $menu_id;
                    }
                    break;
                case 1:
                    if($this->XM->user->check_privilege((int)$row['ma_key'])!==(bool)$row['ma_value']){
                        $blocked[] = $last_blocked = $menu_id;
                    }
                    break;
                default:
                    $blocked[] = $last_blocked = $menu_id;
                    break;
            }
        }
        $this->XM->sqlcore->freeResult($res);
        $menurows = array();
        $res = $this->XM->sqlcore->query('SELECT menu.menu_id, menu.menu_url, menu_ml.menu_ml_caption, menu.menu_parent_menu_id, menu.menu_is_in_submenu
            from menu
            inner join (select menu_id,substring_index(group_concat(menu_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as menu_ml_id from menu_ml group by menu_id) as ln_glue on ln_glue.menu_id = menu.menu_id
            inner join menu_ml on menu_ml.menu_ml_id = ln_glue.menu_ml_id
            where menu.menu_visible = 1 '.($only_submenu?'and menu.menu_is_in_submenu = 1':'').'
            order by menu.menu_order asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $menu_id = (int)$row['menu_id'];
            if(in_array($menu_id, $blocked)){
                continue;
            }
            $menurows[] = array('id'=>$menu_id,'parent_id'=>(int)$row['menu_parent_menu_id'],'url'=>$row['menu_url'],'caption'=>$row['menu_ml_caption'],'is_in_submenu'=>(bool)$row['menu_is_in_submenu']);
        }
        $this->XM->sqlcore->freeResult($res);
        return $menurows;
    }

}