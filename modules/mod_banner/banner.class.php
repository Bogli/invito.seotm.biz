<?
include_once( SITE_PATH.'/modules/mod_banner/banner.defines.php' );

/**
* Class Banner
* parent class of Banner module
* @package Feedback Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.11.2011
* @copyright (c) 2010+ by SEOTM
*/ 
class Banner {

    public $user_id;
    public $module;

    public $Right;
    public $Form;
    public $Msg;
    public $Spr;
    public $Err;
    public $display = 20;
    public $start;
    public $fltr;
    public $fltr2;
    public $asc_desc;
    public $lang_id;

    public $path = NULL;
    public $visible = NULL;
    public $type = NULL;
    public $href = NULL;
    public $size = NULL;
    public $img_bg = NULL;
    public $bannerList = NULL;

    /**
    * Banner::__construct()
    * 
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.11.2011
    * @return void
    */
    function __construct($user_id=NULL, $module=NULL)
    {
        $this->user_id = $user_id;
        $this->module = $module;

        if (empty($this->db)) $this->db = DBs::getInstance();
        $this->Right =  &check_init('RightsBan', 'Rights', "'".$this->user_id."','".$this->module."'");
        $this->Form = &check_init('Form', 'Form', '"form_banner"');
        $this->Msg = &check_init('ShowMsg', 'ShowMsg');
        $this->Spr = &check_init('SysSpr', 'SysSpr');
        $this->lang_id = _LANG_ID;
        if(empty ($this->multi)) $this->multi = &check_init_txt('TblBackMulti', TblBackMulti);
        //$this->AddTable();
        
        $this->arr_set['0'] = $this->multi['TXT_BANNER_TYPE1'];
        $this->arr_set['1'] = $this->multi['TXT_BANNER_TYPE2'];
        $this->arr_set['2'] = $this->multi['TXT_BANNER_TYPE3'];
        
        $this->bannerList = $this->GetBannersInArray(1);
    }


    /**
    * Class method AddTable
    * edit structure of Db for banners
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.08.2011
    * @return true/false
    */  
   function AddTable()
   {
       // add field id_group to the table settings
       if ( !$this->db->IsFieldExist(TblModBanner, "set") ) {
           $q = "ALTER TABLE `".TblModBanner."` ADD `set` char(255) NOT NULL default '';";
           $res = $this->db->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res;
           if( !$res )return false; 
            
           $q = "ALTER TABLE `".TblModBanner."` ADD INDEX ( `set` ) ;";
           $res = $this->db->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res;                   
           if( !$res )return false;
       } 
       
       // add field move to the table of banners
       if ( !$this->db->IsFieldExist(TblModBanner, "move") ) {           
           $q = "ALTER TABLE `".TblModBanner."` ADD `move` int(4) NOT NULL default '0';";
           $res = $this->db->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res;
           if( !$res )return false; 
           $q = "ALTER TABLE `".TblModBanner."` ADD INDEX ( `move` ) ;";
           $res = $this->db->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res;                   
           if( !$res )return false;
           
       } 
       
       // add field img_bg to the table of banners
       if ( !$this->db->IsFieldExist(TblModBanner, "img_bg") ) {
           $q = "ALTER TABLE `".TblModBanner."` ADD `img_bg` char(255) NOT NULL default '';";
           $res = $this->db->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res;
           if( !$res )return false; 
       }       
       return true;       
   }// end of function AddTable()

    /**
    * GetBannersInArray()
    * Get Banners
    * @param - int $visibility
    * @return - $array $this->bannerList
    */
    function GetBannersInArray($visibility=1, $type=NULL)
    {
        $q = "SELECT *
              FROM `".TblModBanner."` 
              WHERE 
                `visible`='".$visibility."' 
              AND
                `s_dt` < '".time()."'
              AND
                `e_dt` > '".time()."'";
        if( !empty($type) ) $q.=" AND `type`='".$type."'";
        $q .= "
              ORDER BY `type`, `id` desc";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db->result='.$this->db->result;
        //echo '<br> $rows = '.$rows;
        $arr = array();
        for($i = 0; $i < $rows; $i++ )
        {
            $row = $this->db->db_FetchAssoc($res);
            $arr[$row['type']][] = $row;
        } //end for 
        return $arr;     
        
    } //end of function GetAllBannersInArray


    /**
    * Class method GetContent
    * get content of banners
    * @param string $limit - Make limit or not for SELECT data from database. Can be 'limit' or 'nolimit'.
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 23.05.2011
    * @return count of rows in 'nolimit' or array with data if 'limit'
    */      
    function GetContent($limit='limit')
    {       
        if( !$this->sort ) $this->sort='move';
        $q = "SELECT * FROM `".TblModBanner."` where 1";
        if( $this->fltr ) $q .= " AND `type`='".$this->fltr."'"; 
        if( $this->fltr2 ) $q .= " AND `ыуе`='".$this->fltr2."'";
        $q .= " ORDER BY `$this->sort` $this->asc_desc";
        if($limit=='limit') $q .= " LIMIT ".$this->start.", ".$this->display;
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();
    
        if($limit=='limit'){
            $arr=array();
            for($i=0;$i<$rows;$i++){
                $arr[$i]=$this->Right->db_FetchAssoc();
            }
            return $arr;
        }
        else return $rows;
   }//end of function GetContent() 

    /**
    * Class method show
    * show content of banners
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 23.05.2011
    * @return true or false
    */    
    function show()
    {
        $rows = $this->GetContent('nolimit');
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        $script = $_SERVER['PHP_SELF']."?$script";
    
        if( !$this->sort ) $this->sort='display';
        if( strstr( $this->sort, 'display' ) )$this->sort = $this->sort.' desc';
    
        /* Write Form Header */
        $this->Form->WriteHeader( $script );
    
        /* Write Table Part */
        AdminHTML::TablePartH();
    
        /* Write Links on Pages */
        ?>
        <tr>
            <td colspan="13">
                <?
                $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
                $script1 = $_SERVER['PHP_SELF']."?$script1";
                $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?$this->Form->WriteTopPanel( $script );?>
            </td>
        </tr>
        <tr>
            <td>
                <div name="load" id="load"></div>
                <div id="result"></div>
                <div id="debug"><?$this->ShowContentHTML();?></div>
            </td>
        </tr>
        <?
        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
    } // end of function show()
    
    /**
    * Class method ShowContentHTML
    * show content list of banners
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 23.05.2011
    * @return true or false
    */
    function ShowContentHTML()
    {
        $row_arr = $this->GetContent();
        $rows = count($row_arr);
        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";
        
     
        if($rows>$this->display) $ch = $this->display;
        else $ch = $rows;
        ?>
        <table > 
         <tr>
            <td colspan="3"></td>
            <td align="center">
                <?
                $arr_fltr = NULL;
                $arr_fltr[''] = $this->multi['TXT_BANNER_ALL_TYPES'];
                $q = "SELECT `".TblModBannerSprTypes."`.*
                      FROM `".TblModBannerSprTypes."` 
                      WHERE `lang_id`='".$this->lang_id."'
                     ";
                $res = $this->Right->db_Query( $q, $this->user_id, $this->module );
                $rows1 = $this->Right->db_GetNumRows();
                for( $i = 0; $i < $rows1; $i++ )
                {
                    $row1 = $this->Right->db_FetchAssoc();
                    $arr_fltr[$row1['cod']] = stripslashes($row1['name']);
        
                }
                $this->Form->SelectAct( $arr_fltr, 'fltr', $this->fltr, "onChange=\"location='$this->script'+'&fltr='+this.value\"" );
                ?>
            </td>         
         </tr>
         <tr>
         <th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
         <th class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->multi['_FLD_ID']?></A></Th>
         <th class="THead"><?=$this->multi['_FLD_PATH']?></Th>
         <th class="THead"><A HREF=<?=$script2?>&sort=id_category><?=$this->multi['_FLD_TYPE_POSITION']?></A></Th>
         <th class="THead"><?=$this->multi['_FLD_DATE']?></Th>
         <th class="THead"><?=$this->multi['_FLD_EXTERNAL_RESOURSE_HREF']?></Th>
         <th class="THead"><?=$this->multi['TXT_SIZE']?></Th>
         <th class="THead"><A HREF=<?=$script2?>&sort=status><?=$this->multi['_FLD_VISIBLE']?></A></Th>
         <th class="THead"><A HREF=<?=$script2?>&sort=status><?=$this->multi['TXT_LIMIT_SHOWS_SHORT']?></A></Th>
         <th class="THead"><A HREF=<?=$script2?>&sort=status><?=$this->multi['TXT_CNT_WIEVS']?></A></Th> 
         <th class="THead">Порядок</th>
         </tr>
        <?


        $style1 = 'TR1';
        $style2 = 'TR2'; 
        for( $i = 0; $i <$rows; $i++ )
        {
           $row = $row_arr[$i];
           $s = explode("/", $row['size']);
           if(is_array($s))
           {
             if(isset($s[1])) $w = $s[0]; else $w='';
             if(isset($s[1])) $h = $s[1]; else $h='';
           }
           if ( (float)$i/2 == round( $i/2 ) )
           {
            echo '<tr class="'.$style1.'">';
           }
           else echo '<tr class="'.$style2.'">';
        
           echo '<td>';
            $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );
        
           echo '<td>';
           $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_EDIT_DATA'] );
                if($row['e_dt']<time()) { $style = 'style="border:2px dashed #FF0000; padding:2px; color:#FF0000;"'; $text = 'Баннер просрочился'; }
                else {$style = NULL; $text = ''; }
               echo '<TD align=center '.$style.'>';
               if(!empty($row['path'])){
               if($row['set']=='0'){
                echo $row['path'];
               } else {
                $file_analisis = explode(".", $row['path']);
                $file_type = $file_analisis[1];
                if($file_type == 'swf')
                {
                    echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'.$w.'" height="'.$h.'">
                     <param name="movie" value="/images/mod_banners/'.$row['path'].'">
                     <param name="quality" value="high">
                     <embed src="/images/mod_banners/'.$row['path'].'" quality="high" width="'.$w.'" height="'.$h.'" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
                     </object>';
                }
                else
                {
                    echo '<img src="/images/mod_banners/'.$row['path'].'" width="'.$w.'" height="'.$h.'">';
                }
                }
            }
            else
            {
                echo "Не установлено!";
            }        
            echo '<br>'.$text.'</td>';
        
             echo '<td align="center">';
            echo $this->Spr->GetNameByCod( TblModBannerSprTypes, $row['type'] );
            echo '</td>';
            
            echo '<td align="center" nowrap="nowrap">';
            echo '<u>'.$this->multi['TXT_FROM'].':</u> '.date('Y-m-d H:i', $row['s_dt']);
            echo '<br><u>'.$this->multi['TXT_TO'].':</u> '.date('Y-m-d H:i', $row['e_dt']);
            echo '</td>';
            
            echo '<td align="center">';
            echo '<a href="'.$row['href'].'" target="_blank">'.$row['href'].'</a>';
            echo '</td>';
           
            echo '<td height="70">';
            if(is_array($s) && !empty($s[0]) && !empty($s[1]))
            {
                echo 'Размеры: '.$s[0].' X '.$s[1];     
            }
            echo '</td>';
              
            echo '<td align="center">';
            switch($row['visible'])
           {
                   case '1': echo '<img src="/admin/images/icons/tick.png">';
                                break;
                case '0': echo '<img src="/admin/images/icons/publish_x.png">';
                                break;
           }
            echo '</td>';
            
           echo '<td>';
           echo $row['limit_shows'];
           
           echo '<td>';
           echo $row['cnt_shows'];
           ?>
           <td align="center" nowrap="nowrap">
            <?
            $url = '/modules/mod_banner/banner.backend.php?'.$this->script_ajax.'&type='.$row['type'];
            if( $i!=0 ){
              $this->Form->ButtonUpAjax($this->script, $row['id'], $url, 'debug', 'move', $row['move']);  
            }
            else{?><img src="images/spacer.gif" width="12"/><?}
            //for replace
            ?>&nbsp;<?$this->Form->TextBoxReplace($url, 'debug', 'move', $row['move'], $row['id']);
            if( $i!=($rows-1) ){
              $this->Form->ButtonDownAjax($this->script, $row['id'], $url, 'debug', 'move', $row['move']);
            }
            else{?><img src="images/spacer.gif" width="12"/><?}
            ?>       
           </td>
           <?
        
         
        } //-- end for
        ?>
           </tr>
          </table>
        <?
        return true;

    } //end of function ShowContentHTML()

    /**
    * Class method edit
    * 
    * @param integer $id - id of banner
    * @param array $mas - array with data of banner
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.11.2011
    * @return true or false
    */     
    function edit( $id, $mas=NULL )
    {
     $Panel = new Panel();
     $ln_sys = new SysLang();
     $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false); 
     $calendar->load_files(); $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
     
     $script = $_SERVER['PHP_SELF']."?$script";
    
     if( $id!=NULL and ( $mas==NULL ) )
     {
       $q = "SELECT * FROM ".TblModBanner." where id='$id'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       if( !$res ) return false;
       $mas = $this->Right->db_FetchAssoc();
     }
    
     /* Write Form Header */
     //$this->Form->WriteHeader( $script );
     $this->Form->WriteHeaderFormImg( $script );
     //$this->Form->IncludeHTMLTextArea();
     if( $id!=NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
     else $txt = $this->multi['_TXT_ADD_DATA'];
     AdminHTML::PanelSubH( $txt );
     AdminHTML::PanelSimpleH();
    
    if($id) $this->cnt_show = $mas['cnt_shows'];               
    if($id) $this->limit_show = $mas['limit_shows'];         
    
    $widthTD = '250';
    ?>
    <table class="EditTable" border="0">
        <tr>
            <td width="<?=$widthTD;?>" valign="top" align="left">
                <?=$this->multi['_FLD_ID'].' ';
                    if( $id!=NULL )
                    {
                    echo $mas['id'];
                    $this->Form->Hidden( 'id', $mas['id'] );
                    }
                    else $this->Form->Hidden( 'id', '' );
                ?>
            </td>
        </tr>
        <tr>    
            <td><?=$this->multi['TXT_CNT_WIEVS']?></td>
            <td><?=$this->Form->TextBox( 'cnt_show', $this->cnt_show, 9 );?></td>
        </tr>
        <tr>
            <td><?=$this->multi['TXT_LIMIT_SHOWS']?></td>
            <td><?=$this->Form->TextBox( 'limit_show', $this->limit_show, 9 );?></td>
        </tr>
        <tr>
            <td><b><?=$this->multi['_FLD_VISIBLE']?>:</b></td>
            <td>
                <?
                $mass['0'] = $this->multi['_FLD_DO_NOT_SHOW_IN_PAGE'];
                $mass['1'] = $this->multi['_FLD_VISIBLE'];
                if( $id!=NULL ) $this->Err!=NULL ? $val=$this->visible : $val=$mas['visible'];
                else $val=1;
                $this->Form->Select( $mass, 'visible', $val, NULL );
                ?>
            </td>
        </tr>        
        <tr>
         <td><strong><?=$this->multi['TXT_START_DATE']?>:</strong></td>
         <td>
            <?
            if( $id!=NULL ) $this->Err!=NULL ? $sdt=$this->sdt : $sdt=date('Y-m-d H:i',$mas['s_dt']);
            else $sdt=$this->sdt;         
            //$this->Form->TextBox( 'dt', $dt, 20 );
          $a1 = array('firstDay'       => 1, // show Monday first
                     'showsTime'      => true,
                     'showOthers'     => true,
                     'ifFormat'       => '%Y-%m-%d %H:%M',
                     'timeFormat'     => '24');
          $a2 = array('style'       => 'width: 15em; font-size:12px; border: 1px solid #000; text-align: center',
                      'name'        => 'sdt',
                      'value'       => $sdt );
          $calendar->make_input_field( $a1, $a2 );
            ?>     
         </td>
        </tr>
        <tr>
         <td><strong><?=$this->multi['TXT_END_DATE']?>:</strong></td>
         <td>
            <?
            if( $id!=NULL ) $this->Err!=NULL ? $edt=$this->edt : $edt=date('Y-m-d H:i',$mas['e_dt']);
            else $edt=$this->edt;         
            //$this->Form->TextBox( 'dt', $dt, 20 );
          $a1 = array('firstDay'       => 1, // show Monday first
                     'showsTime'      => true,
                     'showOthers'     => true,
                     'ifFormat'       => '%Y-%m-%d %H:%M',
                     'timeFormat'     => '24');
          $a2 = array('style'       => 'width: 15em; font-size:12px; border: 1px solid #000; text-align: center',
                      'name'        => 'edt',
                      'value'       => $edt );
          $calendar->make_input_field( $a1, $a2 );
            ?>     
         </td>
        </tr>
        <tr>
         <td><b><?=$this->multi['TXT_BANER_PLACE']?>:</b></td>
         <td>
         <?

          if( $id!=NULL ) $this->Err!=NULL ? $val=$this->type : $val=$mas['type'];
          else $val=$this->fltr; 
          $this->Spr->ShowInComboBox( TblModBannerSprTypes, 'type', $val, '300');
         ?>
         </td>
        </tr>        
        <tr>
         <td colspan="2"><hr></td>
        </tr>
        <tr>
         <td><b><?=$this->multi['TXT_TYPE']?>:</b></td>
         <td>
          <?
          if( $id!=NULL ) $this->Err!=NULL ? $val=$this->set : $val=$mas['set'];
          else $val=1;
          $params = 'id="bannerType" onChange="BannerType();"';
          $this->Form->Select( $this->arr_set, 'set', $val, NULL, $params );
          ?>
         </td>
        </tr>    
        <tr>
            <td>
                <div id="path1"><b><?=$this->multi['_FLD_FILE']?>:</b></div>
            </td>
            <td>
                 <div id="path2">
                    <?
                    if(!empty($mas['path'])){
                        echo Banners_Img_Path_Small.$mas['path'];
                        $this->Form->Hidden('path', $mas['path']);
                    }
                    ?> 
                    <br /><INPUT TYPE="file" NAME="filename" size="40" value="<?=$this->path?>">
                 </div>
            </td>
        <tr>
        <tr>
            <td>
                <div id="img_bg1" style="display: none;"><b><?=$this->multi['_FLD_FILE2']?>:</b></div>
            </td>
            <td>
                <div id="img_bg2" style="display: none;">
                    <?
                    if(!empty($mas['img_bg'])){
                        echo Banners_Img_Path_Small.$mas['img_bg'];
                        $this->Form->Hidden('img_bg', $mas['img_bg']);
                    }
                    ?>
                    <br /><INPUT TYPE="file" NAME="file_img_bg" size="40" value="<?=$this->img_bg;?>">
                </div>
            </td>
        <tr>
           <TD>
            <div id="context1" style="display: none;"><b><?=$this->multi['TXT_CONTEXT']?>:</b></div>
           </TD>
           <td>
                <?
                if ( !empty($mas['path']) and  $mas['set']=='1' ) {
                $this->Form->Hidden( 'path', $mas['path'] );
                } 
                ?>
           <div id="context2" style="display: none;"><?=$this->Form->TextArea( 'content', $mas['path'], 10, 50 );?></div>
        <tr>
         <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td><b><?=$this->multi['FLD_REFERANSE']?></b></td>
            <td>
                <?
                if( $id!=NULL ) $this->Err!=NULL ? $val=$this->href : $val=$mas['href'];
                else $val=$this->href;
                $this->Form->TextBox( 'href', $val, 50 );
                ?>
            </td>
        </tr>
        <tr>
            <td><?=$this->multi['FLD_FORMAT']?></td>
            <td><?=$this->Form->TextBox( 'size', $mas['size'], 50 );?></td>
        </tr>
        <tr>
         <td colspan="2"><hr></td>
        </tr>
        <?
        if($id)
        {
            ?><tr><td colspan="2"><h3><?=$this->multi['TXT_PREVIEW'];?></h3><?
            //$q = "select * from `".TblModBannerSprTypes."` where `cod`='".$mas['type']."'";
            //$res = $this->Right->Query($q, $this->user_id, $this->module);
            //$row = $this->Right->db_FetchAssoc();
    
            if($mas['set']=='0'){
                ?><div style="float:left;"><?=$mas['path'];?></div><?
             }  
             elseif(!empty($mas['path'])) {
                $tmp = explode(".", $mas['path']);
                $tmp_arr = explode("/", $mas['size'] );
                $cnt = count($tmp_arr);
                if($cnt>1)
                {
                    $width = $tmp_arr[0];
                    $height = $tmp_arr[1];
                }
                else{
                    $width = 0;
                    $height = 0;
                }
    
                if(is_array($tmp))
                {
                   $ext = $tmp[1];
                }
                
                $path = Banners_Img_Path_Small.$mas['path'];
                $path_img_bg = Banners_Img_Path_Small.$mas['img_bg'];
                ?><div style="float:right;"><?
                    if($ext=='swf' || $ext=='flv')
                    {
                     echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'.$width.'" height="'.$height.'">
                     <param name="movie" value="'.$path.'">
                     <param name="quality" value="high">
                     <embed src="'.$path.'" quality="high" width="'.$width.'" height="'.$height.'" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
                     </object>';
                     ?><img src="<?=$path_img_bg;?>" <?if($width>0){?>width="<?=$width;?>"<?}?> <?if($height>0){?>height="<?=$height;?>"<?}?>><?                        
                    }
                    else
                    {
                     ?><img src="<?=$path;?>" <?if($width>0){?>width="<?=$width;?>"<?}?> <?if($height>0){?>height="<?=$height;?>"<?}?>><?
                    }
                ?></div><?
            }
            ?></td></tr><?
        }  
        ?></table><?
     $this->Form->WriteFooter();
     AdminHTML::PanelSimpleF();
     $this->Form->WriteSavePanel( $script );
     $this->Form->WriteCancelPanel( $script );
     AdminHTML::PanelSubF();
     ?>
     <script type="text/javascript">
     $(document).ready(function(){
        BannerType();
     });
     function BannerType(){
        if($('#bannerType').val()==0){
            $('#context1').css('display', 'block');
            $('#context2').css('display', 'block');
            $('#path1').css('display', 'none');
            $('#path2').css('display', 'none');
            $('#img_bg1').css('display', 'none');
            $('#img_bg2').css('display', 'none');
        }
        else if($('#bannerType').val()==1){
            $('#path1').css('display', 'block');
            $('#path2').css('display', 'block');
            $('#context1').css('display', 'none');
            $('#context2').css('display', 'none');
            $('#img_bg1').css('display', 'none');
            $('#img_bg2').css('display', 'none');
        }
        else if($('#bannerType').val()==2){
            $('#path1').css('display', 'block');
            $('#path2').css('display', 'block');
            $('#img_bg1').css('display', 'block');
            $('#img_bg2').css('display', 'block');
            $('#context1').css('display', 'none');
            $('#context2').css('display', 'none');
        }
     } 
     </script>
     <?
     return true;
    } // end of function edit()

    /**
    * Class method save
    * 
    * @param integer $id - id of banner
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.11.2011
    * @return true or false
    */    
    function save( $id )
    {
        $q = "SELECT * FROM ".TblModBanner." WHERE `id`='".$id."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res ) return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
       
        $sdt = strtotime($this->sdt);
        $edt = strtotime($this->edt);
       
        if( $rows>0 )   //--- update
        {
         //echo '<br>$row[path]='.$row['path'].' $this->path='.$this->path;
         //echo '<br>$row[img_bg]='.$row['img_bg'].' $this->img_bg='.$this->img_bg;
         if (!empty($row['path']) AND $row['path']!=$this->path) $this->DelImg($id);
         if (!empty($row['img_bg']) AND $row['img_bg']!=$this->img_bg) $this->DelImgBg($id);
         
         $q = "update `".TblModBanner."` set
               `type`='".$this->type."',
               `path`='".$this->path."',
               `visible`='".$this->visible."',
               `href`='".$this->href."',
               `size`='".$this->size."',
               `set`='".$this->set."',
               `s_dt` = '".$sdt."',
               `e_dt` = '".$edt."',
               `cnt_shows` = '".$this->cnt_show."',
               `limit_shows` = '".$this->limit_show."',
               `img_bg`='".$this->img_bg."'
                where `id`='".$id."'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo "<br> q = ".$q." res = ".$res;
         if( !$res ) return false;
        }
        else          //--- insert
        {
            $q="SELECT MAX(`move`) AS `maxx` FROM `".TblModBanner."` WHERE `type`='".$this->type."'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            $rows = $this->Right->db_GetNumRows();
            $my = $this->Right->db_FetchAssoc();
            $maxx=$my['maxx']+1;
         
         $q = "insert into `".TblModBanner."` set 
         `path` = '".$this->path."',
         `type` = '".$this->type."',
         `href` = '".$this->href."',
         `visible` = '".$this->visible."',
         `size` = '".$this->size."',
         `set` = '".$this->set."',
         `s_dt` = '".$sdt."',
         `e_dt` = '".$edt."',
         `cnt_shows` = '".$this->cnt_show."',     
         `limit_shows` = '".$this->limit_show."',
         `move` = '".$maxx."'
         ";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo "<br> q = ".$q." res = ".$res;
         if( !$res ) return false;
        }
        return true;
    } //end of function save
    
    
    /**
    * Class method del
    * 
    * @param array $id_del - array with data of banner for delete
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.11.2011
    * @return true or false
    */       
    function del( $id_del )
    {
        $kol = count( $id_del );
        $db = new DB();
        $del = 0;
        for( $i=0; $i<$kol; $i++ )
        {
         $u = $id_del[$i];
    
        $q = "delete from `".TblModBanner."` where `id`='".$u."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if ( $res )
          $del=$del+1;
         else
          return false;
        } //end for
        
      return $del;
    }
    
    /**
    * Class method DelImg
    * function for delete image/file for Banner
    * @param $id - id of the banner
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 26.09.2011
    */
    function DelImg( $id = NULL ) 
    {
       if ( !empty($id) ) $this->id = $id;
       $tmp_db = new DB();
       
       $q="SELECT * FROM `".TblModBanner."` WHERE `id`='".$this->id."'";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;  
       if( !$res OR !$tmp_db->result ) return false;
       $row = $tmp_db->db_FetchAssoc();
       
       if ( !empty($row['path']) ){
          $path = $_SERVER['DOCUMENT_ROOT'].Banners_Img_Path_Small.$row['path'];
          //echo '<br>$path='.$path;
          if ( file_exists($path) ) {
             $res = unlink ($path);
             if( !$res ) return false;
          }
       }
       $q="update `".TblModBanner."` set `path`='' where `id`='".$this->id."'";
       $res = $tmp_db->db_Query( $q );
       if( !$res )return false;           
        
       return true; 
    } // end function DelImg()
    
    /**
    * Class method DelImgBg
    * function for delete background image for Banner
    * @param $id - id of the banner
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 26.09.2011
    */
    function DelImgBg( $id = NULL ) 
    {
       if ( !empty($id) ) $this->id = $id;
       $tmp_db = new DB();
       
       $q="SELECT * FROM `".TblModBanner."` WHERE `id`='".$this->id."'";
       $res = $tmp_db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;  
       if( !$res OR !$tmp_db->result ) return false;
       $row = $tmp_db->db_FetchAssoc();
       
       if ( !empty($row['img_bg']) ){
          $path = $_SERVER['DOCUMENT_ROOT'].Banners_Img_Path_Small.$row['img_bg'];
          //echo '<br>$path='.$path;
          if ( file_exists($path) ) {
             $res = unlink ($path);
             if( !$res ) return false;
          }
       }
       $q="update `".TblModBanner."` set `img_bg`='' where `id`='".$this->id."'";
       $res = $tmp_db->db_Query( $q );
       if( !$res )return false;           
        
       return true; 
    } // end function DelIDelImgBgmg()        
    
    /**
    * Class method up
    * Up position
    * @param string $table - name of table in db
    * @param string $level_name - name of field 
    * @param integer $level_val - value of field $level_name
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.11.2011
    * @return true or false
    */      
    function up($table, $level_name = NULL, $level_val = NULL)
    {
     $q="select * from `$table` where `move`='$this->move'";
     if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];


     $q="select * from `$table` where `move`<'$this->move'";
     if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
     $q = $q." order by `move` desc";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];

     //echo '<br> $move_down='.$move_down.' $id_down ='.$id_down.' $move_up ='.$move_up.' $id_up ='.$id_up;
     if( $move_down!=0 AND $move_up!=0 )
     {
     $q="update `$table` set
         `move`='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

     $q="update `$table` set
         `move`='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

     }
    } // end of function up()

    /**
    * Class method down
    * down position
    * @param string $table - name of table in db
    * @param string $level_name - name of field 
    * @param integer $level_val - value of field $level_name
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 02.11.2011
    * @return true or false
    */      
    function down($table, $level_name = NULL, $level_val = NULL)
    {
     $q="select * from `$table` where `move`='$this->move'";
     if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];
     //echo '<br>$id_up='.$id_up;


     $q="select * from `$table` where `move`>'$this->move'";
     if ( !empty($level_name) ) $q = $q." AND `$level_name`='$level_val'";
     $q = $q." order by `move` asc";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];
     //echo '<br>$id_down='.$id_down;

     if( $move_down!=0 AND $move_up!=0 )
     {
     $q="update `$table` set
         `move`='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );

     $q="update `$table` set
         `move`='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     }
    } // end of function down()    


    
    // ======================= FRONT END ==========================

    /**
    * GetBanner()
    * Get Banner by type
    * @param - int $type
    * @param - int $visibility
    * @return - false
    */
    function GetBanner($type, $visibility=1)
    {
        $rows = 0;
        if(isset($this->bannerList[$type])) {
            $keys = array_keys($this->bannerList[$type]);
            $rows = count($keys);
        }
        //echo '<br>$rows = '.$rows;
        //sprint_r($this->bannerList);
        if($rows>0) {
             $numbers = range(0, $rows-1);
             shuffle($numbers);
        }
            
        for($i = 0; $i<$rows; $i++ )
        {
            $row = $this->bannerList[$type][$i];
            if($i==$numbers[0]) {
                if(!empty($row['path']))
                {
                $nCntsh = $row['cnt_shows'] +1;
                 if($row['limit_shows']>0 AND $nCntsh>$row['limit_shows']) 
                 {
                  $q = "update `".TblModBanner."` set `visible` = '0' where `id` = '".$row['id']."'";
                  $res = $this->db->db_Query($q);
                  return false; // мы уже показали баннер столько сколько нужно - нафиг.
                 }
                
                if($row['set']=='0') echo $row['path'];
                else {
                    if($rows>1)
                    {
                        echo '<br>';
                    }
                    $s = explode("/", $row['size']);
                    if(is_array($s) && !empty($s[0]) && !empty($s[1]))
                    {
                       $w = $s[0];
                       $h =  $s[1];
                    }

                    $file = explode(".", $row['path']);
                    $file_type = $file[1];
                    
                    $href = stripslashes($row['href']);
                    $path = Banners_Img_Path_Small.$row['path'];
                    $path_img_bg = Banners_Img_Path_Small.$row['img_bg'];                    
                    
                    if($file_type == 'swf')
                    {
                        if($href!=''){ 
                            ?><a href="<?=$href;?>" target="_blank"><?
                        }
                        /*
                        ?>
                        <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="<?=$w?>" height="<?=$h?>" id="manufac" align="middle">
                          <param name="allowScriptAccess" value="sameDomain" />
                          <param name="movie" value="<?=$path;?>" />
                          <param name="quality" value="high" />
                          <param name="bgcolor" value="#ffffff" />
                          <param name="wmode" value="opaque" />
                          <embed src="<?=$path;?>" wmode="opaque" quality="high" bgcolor="#ffffff" width="<?=$w?>" height="<?=$h?>" name="manufac" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
                        </object>
                        <?*/?>    
                        <object id="myId" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?=$w?>" height="<?=$h?>">
            				<param name="movie" value="<?=$path;?>" />
                            
                    		<!--[if !IE]>-->
            				<object type="application/x-shockwave-flash" data="<?=$path;?>" width="<?=$w?>" height="<?=$h?>">
            				<!--<![endif]-->
            				<div>
            					<a href="<?=$href;?>"><img src="<?=$path_img_bg;?>" alt="<?=$href;?>" width="<?=$w?>" height="<?=$h?>" /></a>
            				</div>
            				<!--[if !IE]>-->
            				</object>
            				<!--<![endif]-->
            			</object>                     
                     
                        <?
                        if($href!=''){
                            ?></a><?
                        }
                    }
                    else
                    {
                        if($href!=''){ 
                            ?><a href="<?=$href;?>" target="_blank"><? 
                        } 
                        if(isset($w) and isset($h)){
                            ?><img src="<?=$path;?>" border="0" width="<?=$w?>" height="<?=$h?>" alt="<?=$href;?>" /><? 
                        } 
                        else {
                            ?><img src="<?=$path;?>" border="0" alt="<?=$href;?>" /><?
                        }
                        if($row['href']!=''){ ?></a><? } 
                    }
                   }
                 $q = "update `".TblModBanner."` set `cnt_shows` = '".$nCntsh."' where  `id` = '".$row['id']."' ";
                 $res = $this->db->db_Query($q);
                }
            } 
        } //end for
    } //end of function GetBanner

    /**
    * Class method ShowSlider
    * show dynamic banner. List of banners as one with slider.
    * @param integer $type - id of place for banner
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 23.05.2011
    * @return true or false
    */
    function ShowSlider($type)
    {
        $set = 1;
        $q = "SELECT * FROM `".TblModBanner."` 
              WHERE `type`='".$type."'
              AND `set`='".$set."'  
              AND `visible`='1' 
              AND `s_dt` < '".time()."'
              AND `e_dt` > '".time()."'
              order by `move` 
             ";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>q='.$q.' $res='.$res.' $this->db->result='.$this->db->result.' $rows='.$rows;
        if($rows==0) return false;
        
        for($i = 0; $i<$rows; $i++ )
        {
            $row = $this->db->db_FetchAssoc($res);
            $arr[$i]=$row;
        }
        ?>
        
        <div id="slider">
            <div class="main_view">
                <div class="window">
                    <div class="image_reel">
                        <?
                        for($i=0;$i<$rows;$i++){
                            $nCntsh = $row['cnt_shows'] +1;
                
                            if($row['limit_shows']>0 AND $nCntsh>$row['limit_shows']) 
                            {
                              $q = "update `".TblModBanner."` set `visible` = '0' where `id` = '".$row['id']."'";
                              $res = $db->db_Query($q);
                              return false; // мы уже показали баннер столько сколько нужно - нафиг.
                            }
                            
                            ?><a href="<?=$arr[$i]['href'];?>"><img src="/images/mod_banners/<?=$arr[$i]['path'];?>" /></a><?
                        }
                        ?>
                    </div>
                </div>
                <div class="paging">
                    <?
                    for($i=0;$i<$rows;$i++){
                        ?><a href="#" rel="<?=($i+1);?>"></a><?
                    }
                    ?>
                </div>
            </div>
        </div>
        <?        
        
    } //end of fucntion ShowSlider()


} //end of class Banner