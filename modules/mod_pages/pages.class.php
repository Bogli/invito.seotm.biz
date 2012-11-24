<?php
// ================================================================================================
// System : SEOCMS
// Module : pages.class.php
// Date : 04.10.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Purpose : Class definition for all actions with dynamic pages
// ================================================================================================

 include_once( SITE_PATH.'/modules/mod_pages/pages.defines.php' );

// ================================================================================================
//    Class                         : DynamicPages
//    Date              			: 04.10.2007
//    Constructor       			: Yes
//    Returns           			: None
//    Description       			: Pages Module
//    Programmer        			:  Igor Trokhymchuk
// ================================================================================================
class DynamicPages extends Page {

    var $Right;
    var $Form;
    var $Msg;
    var $Spr;

    var $title;
    var $description;
    var $body;
    var $keywords;
    var $name;
    var $descr;

    var $display;
    var $sort;
    var $start;
    var $move;
    var $id_categ;

    var $user_id;
    var $module;

    var $fltr;
    var $fln;

    var $width;

    var $Err;
    var $lang_id;

    var $visible;
    var $preview = NULL;
    var $sel = NULL;
    var $is_image = 1;
    var $is_tags = 1;
    var $is_short_descr = 1;
    var $is_special_pos = 1;
    var $is_main_page = 1;

    public $treePageList = NULL; //array $this->treePageList[]=$id_cat
    public $treePageLevels = NULL; //array $this->treePageLevels[level][id_cat]=''
    public $treePageData = NULL; //array treePageData[id_cat]=array with category data

    // ================================================================================================
    // Function : DynamicPages Constructor()
    // Date : 11.02.2005
    // Returns :      true,false / Void
    // Description :  Up position
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DynamicPages()
    {
     $this->db =  DBs::getInstance();
     $this->Form =  &check_init('FormPages', 'Form', 'mod_pages');
     $this->Spr = &check_init('SysSpr', 'SysSpr');
     $this->width = '750';
     if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
    }//end Constructor

    /**
    * Class method loadTree
    * load all data of catalog categories to arrays
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2011
    */
    function loadTree()
    {
        if( is_array($this->GetTreePageLevelAll()) AND is_array($this->GetTreePageDataAll()) ) return true;

        $q = "SELECT `".TblModPages."`.*, `".TblModPagesTxt."`.`pname` FROM `".TblModPages."`, `".TblModPagesTxt."`
              WHERE  `".TblModPagesTxt."`.cod=`".TblModPages."`.id
              AND `".TblModPagesTxt."`.lang_id='".$this->lang_id."'
              ORDER BY `move` asc";

        $res = $this->db->db_Query($q);
        //echo $q.' <br/>$res = '.$res.' $this->db->result='.$this->db->result;
        if(!$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows($res);
        if($rows==0)
            return false;

        $tree = array();

        for($i = 0; $i < $rows; $i++){
            $row = $this->db->db_FetchAssoc($res);

            if(empty($tree[$row['level']])) {
                $tree[$row['level']] = array();
            }
            $this->SetTreeCatLevel($row['level'], $row['id']);
            //$this->treePageLevels[$row['level']][$row['id']]='';
            $this->SetTreePageData($row);
            //$this->treePageData[$row['id']]=$row;
        }
        //build category translit path for all categories and subcategories
        //exit();
        $this->makeCatPath();
        return true;
    } //end of function loadTree()

    /**
    * Class method SetTreeCatLevel
    * set new vlaue to property $this->treePageLevels. It build array $this->treePageLevels[level][id_cat]=''
    * @param integer $level - id of the parent category
    * @param integer $id - id of the category
    * @return none
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreeCatLevel($level, $id)
    {
        $this->treePageLevels[$level][$id]='';
    } //end of function SetTreeCatLevel()

    /**
    * Class method GetTreePageLevelAll
    * get array $this->treePageLevels
    * @return array $this->treePageLevels
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreePageLevelAll()
    {
        return $this->treePageLevels;
    } //end of function GetTreePageLevelAll()

    /**
    * Class method GetTreePageLevel
    * get node of array $this->treePageLevels where store array with sublevels
    * @param integer $item - id of the category as node in array
    * @return node of array $this->treePageLevels[$item]
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreePageLevel($item=0)
    {
        if(!isset($this->treePageLevels[$item])) return false;
        return $this->treePageLevels[$item];
    } //end of function GetTreePageLevel()

    /**
    * Class method SettreePageData
    * set new vlaue to property $this->treePageData. It build array $this->treePageData[id_cat]=array with category data
    * @param array $row - assoc array with data of category
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreePageData($row)
    {
        $this->treePageData[$row['id']]=$row;
        return true;
    } //end of function SettreePageData()

    /**
    * Class method SettreePageDataAddNew
    * set new vlaue to property $this->treePageData. It build array $this->treePageData[id_cat]=array with category data
    * @param integer $id_cat - id of the category
    * @param varchar $key - name of new key
    * @param varchar $val - value for key $key
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreePageDataAddNew($id_cat, $key, $val)
    {
        $this->treePageData[$id_cat][$key]=$val;
        return true;
    } //end of function SettreePageDataAddNew()


    /**
    * Class method GettreePageDataAll
    * get array $this->treePageData
    * @return array $this->treePageData
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreePageDataAll()
    {
        return $this->treePageData;
    } //end of function GettreePageDataAll()

    /**
    * Class method GettreePageData
    * get node of array $this->treePageData where store array with data about category
    * @param integer $item - id of the category as node in array
    * @return node of array $this->treePageData[$item]
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GettreePageData($item)
    {
        if(!isset($this->treePageData[$item])) return false;
        return $this->treePageData[$item];
    } //end of function GettreePageData()

    /**
    * Class method SettreePageList
    * set new vlaue to property $this->treePageList. It build array $this->treePageList[counter]=id of the category
    * @param integer $counter - counter for array
    * @param integer $id_cat - id of the category
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SettreePageList($counter, $id_cat)
    {
        $this->treePageList[$counter] = $id_cat;
        return true;
    } //end of function SettreePageList()

    /**
    * Class method GetTreeListAll
    * get array $this->treePageList
    * @return array $this->treePageList
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreeListAll()
    {
        return $this->treePageList;
    } //end of function GetTreeListAll()

    /**
    * Class method makeCatPath
    * build relative url to category using category translit for all categories and subcategories
    * @param integer $level - id of the category
    * @param string $path - path to category
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 14.01.2011
    */
    function makeCatPath($level = 0, $path = NULL)
    {
        if( !$this->GetTreePageLevel($level) ) return;
        $n = count($this->GetTreePageLevel($level));
        $keys = array_keys($this->GetTreePageLevel($level));
        for($i = 0; $i < $n; $i++) {
            //echo '<br />$keys[$i]='.$keys[$i];
            $row = $this->GettreePageData($keys[$i]);
            //if(!$path) $full_path = '/'.$row['name'];
            //else $full_path = $path.$row['name'];
            if($row['ctrlscript']==1){
                //echo '<br>substr($path, (strlen($path)-1))='.substr($path, (strlen($path)-1)).' substr($row[name], 1,1)='.substr($row['name'], 1,1).' $row[name]='.$row['name'];
                if(substr($path, (strlen($path)-1))!='/' AND substr($row['name'], 1,1)!='/' AND !empty($path)) $full_path = $path.'/'.$row['name'];
                else $full_path = $path.$row['name'];
                //echo ' $full_path='.$full_path;
            }
            else $full_path = $row['name'];
            //$this->treePageData[$keys[$i]]['path'] = $full_path;
            $this->SettreePageDataAddNew($keys[$i], 'path', $full_path);
            //$this->treePageList[]=$row['id'];
            $this->SettreePageList($i, $row['id']);
            $this->makeCatPath($row['id'], $full_path);
        }
    }//end of function makeCatPath()

    /**
    * Class method isPageASubcatOfLevel
    * Checking if the page $id_page is a subcategory of $item at any dept start from $arr[$item]
    * @param integer $id_page - id of the page
    * @param integer $item - as index for array $arr
    * @return array with index as counter
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2012
    */
    function isPageASubcatOfLevel($id_page, $item)
    {
       if($id_page==$item) return true;
       $a_tree = $this->GetTreePageLevel($item);
       if( !$a_tree ) return false;
       $keys = array_keys($a_tree);
       $rows = count($keys);
       if(array_key_exists($id_page, $a_tree)) return true;
       for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( $this->GetTreePageLevel($id) AND is_array($this->GetTreePageLevel($id)) ) {
                $res = $this->isCatASubcatOfLevel($id_page, $id);
                if($res) return true;
            }
        }
        return false;
    } // end of function isPageASubcatOfLevel()

    /**
    * Class method isSubLevels()
    * Checking exist or not sublevels for page $id_page
    * @param integer $id_page - id of the page
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2012
    */
    function isSubLevels($id_page)
    {
       if( !$this->GetTreePageLevel($id_page) ) return false;
       return true;
    } // end of function isSubLevels()

    /**
    * Class method getSubLevels
    * return string with sublevels for page $id_page
    * @param integer $id_page - id of the page
    * @return sting with id of categories like (1,13,15,164? 222)
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.04.2012
    */
    function getSubLevels( $id_page )
    {
       if( !$this->GetTreePageLevel($id_page) ) return false;
       $a_tree = $this->GetTreePageLevel($id_page);
       $keys = array_keys($a_tree);
       $rows = count($keys);
       for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( empty($arr_row)) $arr_row = $id;
            else $arr_row = $arr_row.','.$id;
            if(  $this->GetTreePageLevel($id) AND is_array($this->GetTreePageLevel($id)) ) {
                $arr_row .= ','.$this->getSubLevels($id);
            }
        }
        return $arr_row;
    } // end of function getSubLevels()

    /**
    * Class method getTopLevel
    * get the top level of pages for page $id_page
    * @param integer $id_page - id of the page
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 05.04.2012
    */
    function getTopLevel($id_page)
    {
        $cat_data = $this->GetTreePageData($id_page);
        if(!$cat_data) return false;
        if($cat_data['level']==0) return $id_cat;
        return $this->getTopLevel($cat_data['level']);
    } // end of function getTopLevel()


    /**
    * Class method getUrlByTranslit
    * build reletive URL link to page $id_page
    * @param string $translit - string with tranlsit to the page
    * @return string $link with reletive URL link to page $id_page
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 05.04.2012
    */
    function getUrlByTranslit($translit)
    {
        if( !defined("_LINK")) {
            $Lang = &check_init('SysLang', 'SysLang', 'NULL, "front"');
            if( _LANG_ID!=$Lang->GetDefFrontLangID() ) define("_LINK", "/".$Lang->GetLangShortName(_LANG_ID)."/");
            else define("_LINK", "/");
        }

        $link = _LINK.$translit;
        return $link;
    } //end of function getUrlByTranslit()

    // ================================================================================================
    // Function : Disable()
    // Returns : true,false / Void
    // Programmer : Yaroslav Gyryn
    // Date : 21.02.2010
    // ================================================================================================
     function Disable($field=null)
    {
        if($field==null)
            return false;
        $db = DBs::getInstance();
        $q="UPDATE `".TblModPages."`
              SET `".$field."`= 2
              WHERE `".$field."`= 1";
        $res = $db->db_Query($q);
        $rows = $db->db_GetNumRows($res);
        if(!$rows)
            return false;
        return true;
    }
    // ================================================================================================
    // Function : Enable()
    // Returns : true,false / Void
    // Programmer : Yaroslav Gyryn
    // Date : 21.02.2010
    // ================================================================================================
     function Enable($field=null)
    {
        if($field==null)
            return false;
        $db = DBs::getInstance();
        $q="UPDATE `".TblModPages."`
              SET `".$field."`= 1
              WHERE `".$field."`= 2";
        $res = $db->db_Query($q);
        $rows = $db->db_GetNumRows($res);
        if(!$rows)
            return false;
        return true;
    }

    // ================================================================================================
    // Function : GetIdByFolderName()
    // Date : 05.02.2008
    // Params :  $q - string with path
    // Returns :      true,false / Void
    // Description : return id of the page
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetIdByFolderName($translit=NULL, $level=0)
    {
        $tmp=explode("/",$translit);
        //print_r($tmp);
        $cnt = count($tmp);
        if( $cnt==0 ) return false;

        $db = DBs::getInstance();
        if( empty($tmp[$cnt-1]) ) $cnt=$cnt-1;
        $q = "SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-1])."'";

        //if link like http://cms.seotm/Napravleniya
        if( $cnt==1 ) $q =$q." AND `level`='".$level."'";

        //============ block for search uniqe link start ==============
        //if link like http://cms.seotm/Napravleniya/steel/services/services/all/1
        if( $cnt>5 AND !empty($tmp[$cnt-1]) )
            $q .= " AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-2])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-3])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-4])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-5])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-6])."'
                    AND `level`='".$level."')))))";
        //if link like http://cms.seotm/Napravleniya/steel/services/services/all
        elseif( $cnt>4 AND !empty($tmp[$cnt-1]) )
            $q .= " AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-2])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-3])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-4])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-5])."'
                    AND `level`='".$level."'))))";
        //if link like http://cms.seotm/Napravleniya/steel/services/services
        elseif( $cnt>3 AND !empty($tmp[$cnt-1]) )
            $q .= " AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-2])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-3])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-4])."'
                    AND `level`='".$level."')))";
        //if link like http://cms.seotm/Napravleniya/steel/services
        elseif( $cnt>2 AND !empty($tmp[$cnt-1]) )
            $q .= " AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-2])."'
                    AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-3])."'
                    AND `level`='".$level."'))";
        //if link like http://cms.seotm/Napravleniya/steel
        elseif($cnt>1 AND !empty($tmp[$cnt-1]) )
            $q = $q." AND `level`=(SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$this->PrepareLink($tmp[$cnt-2])."' AND `level`='".$level."')";
        //============ block for search uniqe link end ==============

        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if( !$res OR !$db->result ) return false;
        $row = $db->db_FetchAssoc();
        //echo '<br>$ros[id]='.$row['id'];
        if(empty($row['id'])){
            $q = "SELECT `id` FROM `".TblModPages."` WHERE BINARY `name`= BINARY '".$translit."'
                  AND `ctrlscript`='0'";
            $res = $db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
            if( !$res OR !$db->result ) return false;
            $row = $db->db_FetchAssoc();
        }
        return  $row['id'];
    }//end of function GetIdByFolderName()

    // ================================================================================================
    // Function : GetIdByName()
    // Date : 05.02.2008
    // Params :  $pn - name of the link
    // Returns :      true,false / Void
    // Description : return id of the page
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetIdByName($pn)
    {
        $db = DBs::getInstance();
        $q = "SELECT `id` FROM `".TblModPages."` WHERE `name`='".$pn.".html'";
        $res =$db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if( !$res OR !$db->result ) return false;
        $row = $db->db_FetchAssoc();
        return $row['id'];
    }//end of function GetIdByName()

    // ================================================================================================
    // Function : GetPagesInArray()
    // Date : 04.04.2006
    // Returns : true,false / Void
    // Description : Show structure of pages in Combo box
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPagesInArray($level = NULL, $default_val = NULL, $mas = NULL, $spacer = NULL, $show_content = 1, $front_back = 'back', $show_sublevels = 1)
    {
        $db = DBs::getInstance();
        $tmp_db = DBs::getInstance();
        $q = "SELECT `".TblModPages."`.*, `".TblModPagesTxt."`.`pname`
              FROM `".TblModPages."`, `".TblModPagesTxt."`
              WHERE `".TblModPages."`.`level`='".$level."'
              AND `".TblModPages."`.`id`=`".TblModPagesTxt."`.`cod`
              AND `".TblModPagesTxt."`.`lang_id`='".$this->lang_id."'";
        //echo " tar=".$front_back;
        if ( $front_back=='front' ) $q = $q." AND `visible`='2'";
        //if ( $front_back=='back' ) $q = $q." AND `visible`='2'";
        $q = $q." order by `move` ";
        $res = $db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
        if( !$res )return false;
        $rows = $db->db_GetNumRows();
        for( $i = 0; $i < $rows; $i++ )
        {
            $arr_data[$i]=$db->db_FetchAssoc();
        }
        //echo '<br> $rows='.$rows;
        //echo '<br> $show_content='.$show_content;
        $mas[''] = $default_val;
        for( $i = 0; $i < $rows; $i++ )
        {
            $row=$arr_data[$i];
            $mas[''.$row['id']] = $spacer.'- '.stripslashes($row['pname']);

            $tmp_q = "SELECT `id` FROM ".TblModPages." WHERE `level`=".$row['id'];
            $res = $tmp_db->db_Query( $tmp_q );
            $tmp_rows = NULL;
            if( $res ) $tmp_rows = $tmp_db->db_GetNumRows();
            //echo '<br> $tmp_rows='.$tmp_rows;

            //----------------- show subcategory ----------------------------
            if( $show_sublevels==1 ){
                if ($tmp_rows>0) $mas = $mas + $this->GetPagesInArray($row['id'], $default_val, $mas, $spacer.'&nbsp;&nbsp;&nbsp;', $show_content, $front_back, $show_sublevels);
            }
            //------------------------------------------------------------------
        }
        return $mas;
    } // end of function GetPagesInArray()

    // ================================================================================================
    // Function : GetImgWithPath()
    // Date : 29.05.2008
    // Parms : $id - id of the page
    // Returns : true,false / Void
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetImgWithPath($img, $lang_id){
        return Pages_Img_Path_Small.$lang_id.'/'.$img;
    } // end of funfiotn GetImgWithPath()

    // ================================================================================================
    // Function : GetImgWithPathFull()
    // Date : 29.05.2008
    // Parms : $id - id of the page
    // Returns : true,false / Void
    // Description :
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetImgWithPathFull($img, $lang_id){
        return Pages_Img_Path.$lang_id.'/'.$img;
    } // end of function GetImgWithPathFull()

    // ================================================================================================
    // Function : GenerateTranslit()
    // Date : 20.02.2008
    // Parms :  $id_cat - if of the level
    //          $id     - id of the current position
    //          $name    -  name of the current position
    // Returns : true,false / Void
    // Description :  generate translit name of current position
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GenerateTranslit($id_cat, $id, $name = NULL)
    {
        $Crypt = &check_init('Crypt', 'Crypt');
        $lang = &check_init('SysLang', 'SysLang');
        $default_lang = $lang->GetDefFrontLangID();
        $translit = NULL;

        if( is_array($name)){
            //fisrt try to get name in English
            if( isset($name[1]) AND !empty($name[1]) AND $default_lang!=1){
                $tmp_name = stripslashes(trim($name[1]));
            }
            //if not exist name in English then use name on default language of front-end
            elseif( isset($name[$default_lang]) AND !empty($name[$default_lang]) ){
                $tmp_name = stripslashes(trim($name[$default_lang]));
            }
            //if not exist name on default language  then try to use name on language of admin-part
            elseif( isset($name[$this->lang_id]) AND !empty($name[$this->lang_id]) ){
                $tmp_name = stripslashes(trim($name[$this->lang_id]));
            }
            else $tmp_name=NULL;
            //echo '<br>$tmp_name='.$tmp_name;

            // crop last sympol "/" if it is exist in the link.
            if( (strrpos($tmp_name, "/")+1) == strlen($tmp_name) ){
                $tmp_name = substr($tmp_name, 0, strrpos($tmp_name, "/") );
            }


            //get translited string
            $translit_no_last_slash = $Crypt->GetTranslitStr(stripslashes(trim($tmp_name)));
            //echo '<br>$tmp_name='.$tmp_name.' $translit_no_last_slash='.$translit_no_last_slash;
        }
        else{
            //get translited string
            $translit_no_last_slash = $Crypt->GetTranslitStr(stripslashes(trim($name)));
        }
        //before check tranlsit in the database add last symbol "/" to translitted string $translit_no_last_slash
        $translit = $this->PrepareLink($translit_no_last_slash);

        //chek if already exist record with same translit.
        $res_id = $this->IsExistTranslit($translit, $id_cat);
        //echo '<br>$res_id='.$res_id.' $id='.$id;
        if( $res_id!='' AND $res_id!=$id ){
            $translit = $this->PrepareLink($translit_no_last_slash.'-'.$id);
        }
        //echo '<br>$translit='.$translit;
        return $translit;
    }// end of function GenerateTranslit()

    // ================================================================================================
    // Function : IsExistTranslit()
    // Date : 20.02.2008
    // Parms :  $translit - translit name
    //          $id_cat - if of the category
    // Returns : true,false / Void
    // Description :  save translit name of current position
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function IsExistTranslit($translit, $id_cat)
    {
        $db = DBs::getInstance();
        $q = "SELECT `id` FROM `".TblModPages."` WHERE `level`='".$id_cat."' AND `name`='".$translit."'";
        $res =$db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if( !$res OR !$db->result ) return false;
        $row = $db->db_FetchAssoc();
        return $row['id'];
    }// end of function IsExistTranslit()

    // ================================================================================================
    // Function : MainPage()
    // Date : 24.12.2009
    // Returns : true,false / Void
    // Description : return id of the main page
    // Programmer : Oleg Morgalyuk
    // ================================================================================================
     function MainPage()
     {
         if (isset($this->main_page))
            return $this->main_page;
         $this->main_page = $this->GetMainPage();
         return $this->main_page;
     }//end of function MainPage()

    // ================================================================================================
    // Function : GetMainPage()
    // Date : 14.10.2008
    // Returns : true,false / Void
    // Description :  return id of the main page
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetMainPage()
    {
        $db = DBs::getInstance();
        $q = "SELECT * FROM `".TblModPages."` WHERE `main_page`='1'";
        $res =$db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if( !$res OR !$db->result ) return false;
        $rows = $db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        if( $rows==0 ) return false;
        $row=$db->db_FetchAssoc();
        return $row['id'];
    }// end of function GetMainPage()

    // ================================================================================================
    // Function : PrepareLink()
    // Date : 18.07.2009
    // Parms :  $name     - url of the current position
    //          $ctrlscript - inner or outer link to page
    // Returns : true,false / Void
    // Description : prepare translit str to link
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function PrepareLink($name, $ctrlscript=true)
    {
        //echo '<br>$name='.$name.' (strrpos($name, "/")+1)='.(strrpos($name, "/")+1).' strlen($name)='.strlen($name);
        if( $ctrlscript AND !empty($name) AND !(strstr($name, ".htm")) AND (strrpos($name, "/")+1) != strlen($name) ) $name = $name."/";
        return $name;
    }// end of function PrepareLink()


    // ================================================================================================
    // Function : GetPageTxt()
    // Date : 23.12.2009
    // Parms :  $id - id of the current position
    // Returns : true,false / Void
    // Description : return all text files for page $id
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetPageTxt($id, $lang_id=NULL)
    {
        if( isset($this->page_txt) AND is_array($this->page_txt) AND $id==$this->page) return $this->page_txt;
        $page_txt = $this->GetPageData($id, $lang_id=NULL);
        return $page_txt;
    }// end of function GetPageTxt()

   // ================================================================================================
   // Function : GetPageData()
   // Date : 18.05.2010
   // Returns : true,false / Void
   // Description : get dat of $page in array
   // Programmer : Igor Trokhymchuk
   // ================================================================================================
   function GetPageData($page, $lang_id=NULL)
   {
        $db = DBs::getInstance();
        if(empty($lang_id)) $lang_id = $this->lang_id;
        $q = "SELECT `".TblModPages."`.*, `".TblModPagesTxt."`.*  FROM `".TblModPages."`
              LEFT JOIN `".TblModPagesTxt."` ON (`".TblModPages."`.`id`=`".TblModPagesTxt."`.`cod` AND `".TblModPagesTxt."`.`lang_id`='".$lang_id."')
              WHERE `".TblModPages."`.`id`='".$page."'";
        $res =$db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if( !$res OR !$db->result ) return false;
        $row = $db->db_FetchAssoc();
        return $row;
   } //end of function GetPageData()

    // ================================================================================================
    // Function : QuickSearch()
    // Date : 27.03.2008
    // Returns : true,false / Void
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function QuickSearch($search_keywords)
    {
        $search_keywords = stripslashes($search_keywords);
        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';

        $str_like = $this->build_str_like(TblModPagesTxt.'.pname', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModPagesTxt.'.content', $search_keywords);

        $q ="SELECT `".TblModPages."`.id, `".TblModPages."`.id_categ, `".TblModPages."`.visible, `".TblModPages."`.level, `".TblModPages."`.move, `".TblModPagesTxt."`.*
            FROM `".TblModPages."`, `".TblModPagesTxt."`
            WHERE (".$str_like.")
            AND `".TblModPages."`.id = `".TblModPagesTxt."`.cod
            AND `".TblModPagesTxt."`.lang_id = '".$this->lang_id."'
            AND `".TblModPages."`.visible = '1'
            ORDER BY `".TblModPages."`.level, `".TblModPages."`.move";
        $res =  $this->db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res;
        //echo '<br>q='.$q.' res='.$res.'  $this->db->result='. $this->db->result;
        if ( !$res OR ! $this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $arr_res = array();
        for($i=0;$i<$rows;$i++){
            $arr_res[$i] = $this->db->db_FetchAssoc();
        }
        return $arr_res;
    } // end of function QuickSearch

    // ================================================================================================
    // Function : build_str_like
    // Date : 19.01.2005
    // Parms : $find_field_name - name of the field by which we want to do search
    //         $field_value - value of the field
    // Returns : str_like_filter - builded string with special format;
    // Description : create the string for SQL-command SELECT for search in the text field by any word
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function build_str_like($find_field_name, $field_value)
    {
        $str_like_filter=NULL;
        // cut unnormal symbols
        $field_value=preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
        // delete double spacebars
        $field_value=str_replace(" +", " ", $field_value);
        $wordmas=explode(" ", $field_value);

        for ($i=0; $i<count($wordmas); $i++){
              $wordmas[$i] = trim($wordmas[$i]);
              if (EMPTY($wordmas[$i])) continue;
              if (!EMPTY($str_like_filter)) $str_like_filter=$str_like_filter." AND ".$find_field_name." LIKE '%".$wordmas[$i]."%'";
              else $str_like_filter=$find_field_name." LIKE '%".$wordmas[$i]."%'";
        }
        if ($i>1) $str_like_filter="(".$str_like_filter.")";
        //echo '<br>$str_like_filter='.$str_like_filter;
        return $str_like_filter;
    } //end of function build_str_like()

} //end of class DynamicPages
?>