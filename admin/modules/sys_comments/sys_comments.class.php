<?php
include_once( SITE_PATH.'/include/defines.php' );

/**
 * Class CommentsCtrl
 * Class definition for all actions with managment of Comments
 * @package System Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 02.04.2012
 * @copyright (c) 2005+ by SEOTM
 */
class CommentsCtrl {

    public $user_id = NULL;
    public $module = NULL;
    public $lang_id = NULL;
    public $Err = NULL;
    public $sort = NULL;
    public $display = 10;
    public $start = 0;
    public $width = 500;
    public $fln = NULL;
    public $fltr = NULL;
    public $fltr2 = NULL;
    public $srch = NULL;
    public $Msg = NULL;
    public $Rights = NULL;
    public $Form = NULL;
    public $Spr = NULL;
    public $listModules = NULL;

    /**
     * CommentsCtrl::__construct()
     *
     * @param integer $user_id
     * @param integer $module
     * @param integer $display
     * @param string $sort
     * @param integer $start
     * @param integer $width
     * @return void
     */
    function __construct($user_id = NULL, $module = NULL, $display = NULL, $sort = NULL, $start = NULL, $width = NULL) {
        //Check if Constants are overrulled
        ( $user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL );
        ( $module != "" ? $this->module = $module : $this->module = NULL );
        ( $display != "" ? $this->display = $display : $this->display = 10 );
        ( $sort != "" ? $this->sort = $sort : $this->sort = NULL );
        ( $start != "" ? $this->start = $start : $this->start = 0 );
        ( $width != "" ? $this->width = $width : $this->width = 750 );

        if (defined("_LANG_ID"))
            $this->lang_id = _LANG_ID;

        if (empty($this->Rights))
            $this->Rights = new Rights($this->user_id, $this->module);
        if (empty($this->db))
            $this->db = DBs::getInstance();
        if (empty($this->Form))
            $this->Form = new Form('form_comments');
        if (empty($this->multi))
            $this->multi = &check_init_txt('TblBackMulti', TblBackMulti);
        if (empty($this->Spr))
            $this->Spr = new SysSpr();

        if(defined("MOD_PAGES") AND MOD_PAGES) $this->listModules[37]=$this->multi['TXT_MOD_PAGES'];
        if(defined("MOD_CATALOG") AND MOD_CATALOG) $this->listModules[21]=$this->multi['TXT_MOD_CATALOG'];
        if(defined("MOD_NEWS") AND MOD_NEWS) $this->listModules[24]=$this->multi['TXT_MOD_NEWS'];
        if(defined("MOD_ARTICLE") AND MOD_ARTICLE) $this->listModules[32]=$this->multi['TXT_MOD_ARTICLE'];
        if(defined("MOD_GALLERY") AND MOD_GALLERY) $this->listModules[87]=$this->multi['TXT_MOD_GALLERY'];
        if(defined("MOD_VIDEO") AND MOD_VIDEO) $this->listModules[83]=$this->multi['TXT_MOD_VIDEO'];
        if(defined("MOD_ASKED") AND MOD_ASKED) $this->listModules[75]=$this->multi['TXT_MOD_ASKED'];
    }

// End of CommentsCtrl Constructor
    // ================================================================================================
    // Function : show
    // Version : 1.0.0
    // Date : 20.08.2008
    //
	   // Parms :
    // Returns : true,false / Void
    // Description : Show data from $module table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 05.12.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function show() {

        $this->ShowJS();
        $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
        $calendar->load_files();

        $from="`".TblSysModComments."`";
        $lef_join = "";
        $where = "";
        include_once( SITE_PATH.'/include/defines.php' );
        //if($this->fltr2){
            if(defined("MOD_PAGES") AND MOD_PAGES){
                $lef_join .= "LEFT JOIN `".TblModPagesTxt."` ON (`".TblSysModComments."`.`id_item`=`".TblModPagesTxt."`.`cod` AND `".TblModPagesTxt."`.`lang_id`='".$this->lang_id."') ";
                $where .= " OR `".TblModPagesTxt."`.`pname` LIKE '%".$this->fltr2."%'";
            }
            if(defined("MOD_CATALOG") AND MOD_CATALOG){
                $lef_join .= "LEFT JOIN `".TblModCatalogPropSprName."` ON (`".TblSysModComments."`.`id_item`=`".TblModCatalogPropSprName."`.`cod` AND `".TblModCatalogPropSprName."`.`lang_id`='".$this->lang_id."') ";
                $where .= " OR `".TblModCatalogPropSprName."`.`name` LIKE '%".$this->fltr2."%'";
            }
            if(defined("MOD_NEWS") AND MOD_NEWS){
                $lef_join .= "LEFT JOIN `".TblModNewsSprSbj."` ON (`".TblSysModComments."`.`id_item`=`".TblModNewsSprSbj."`.`cod` AND `".TblModNewsSprSbj."`.`lang_id`='".$this->lang_id."') ";
                $where .= " OR `".TblModNewsSprSbj."`.`name` LIKE '%".$this->fltr2."%'";
            }
            if(defined("MOD_ARTICLE") AND MOD_ARTICLE){
                $lef_join .= "LEFT JOIN `".TblModArticleTxt."` ON (`".TblSysModComments."`.`id_item`=`".TblModArticleTxt."`.`cod` AND `".TblModArticleTxt."`.`lang_id`='".$this->lang_id."') ";
                $where .= " OR `".TblModArticleTxt."`.`name` LIKE '%".$this->fltr2."%'";
            }
            if(defined("MOD_GALLERY") AND MOD_GALLERY){
                $lef_join .= "LEFT JOIN `".TblModGalleryTxt."` ON (`".TblSysModComments."`.`id_item`=`".TblModGalleryTxt."`.`cod` AND `".TblModGalleryTxt."`.`lang_id`='".$this->lang_id."') ";
                $where .= " OR `".TblModGalleryTxt."`.`name` LIKE '%".$this->fltr2."%'";
            }
            if(defined("MOD_VIDEO") AND MOD_VIDEO){
                $lef_join .= "LEFT JOIN `".TblModVideoTxt."` ON (`".TblSysModComments."`.`id_item`=`".TblModVideoTxt."`.`cod` AND `".TblModVideoTxt."`.`lang_id`='".$this->lang_id."') ";
                $where .= " OR `".TblModVideoTxt."`.`name` LIKE '%".$this->fltr2."%'";
            }
            if(defined("MOD_ASKED") AND MOD_ASKED){
                $lef_join .= "LEFT JOIN `".TblModAsked."` ON `".TblSysModComments."`.`id_item`=`".TblModAsked."`.`id` ";
                $where .= " OR `".TblModAsked."`.`question` LIKE '%".$this->fltr2."%'";
            }
        //}
        //$lef_join = "";
        if($this->srch)
            $from.=", `".TblModUser."`";
        if( !$this->sort )
            $this->sort='dt';

        $q = "SELECT `".TblSysModComments."`.*
              FROM ".$from." ".$lef_join."
              WHERE 1";
	if($this->fltr) $q .= " AND `".TblSysModComments."`.`id_module`='".$this->fltr."'";
        if($this->fltr2) $q .= $where;
        if($this->srch)
        {
            $arrFIO=explode(' ',$this->srch);
            $q.=" AND ((";
            for($j=0;$j<count($arrFIO);$j++)
            {
                if($j!=0)
                    $q.=" AND ";
                $q.=" (";
                $q.=" `".TblModUser."`.`name` LIKE '$arrFIO[$j]%'
                        OR `".TblModUser."`.`family` LIKE '$arrFIO[$j]%'
                        OR `".TblModUser."`.`patronic` LIKE '$arrFIO[$j]%'";
                $q.=" )";
            }
            $q.=" ) || (`".TblModUser."`.`email`='".$this->srch."'))
                AND `".TblModUser."`.`sys_user_id`=`".TblSysModComments."`.`id_user`";
        }
        $q = $q." group by `".TblSysModComments."`.`id` ORDER BY `".TblSysModComments."`.`".$this->sort."` desc";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res )return false;
        $rows = $this->Rights->db_GetNumRows();

        // echo '<br> this->srch ='.$this->srch.' $script='.$script;

        /* Write Form Header */
        $this->Form->WriteHeader($this->script);

        $this->ShowContentFilters();
        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=10>';
        //$script1 = 'module='.$this->module.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2;
        //$script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages($this->script, $rows, $this->display, $this->start, $this->sort);

        echo '<TR><TD COLSPAN=10><div class="topPanel">';
        $this->Form->WriteTopPanel($this->script, 2);
        ?></div><td align=center><?
        // $this->Spr->ShowActSprInCombo(TblModCommentsSprGroup, 'fltr', $this->fltr, $_SERVER["QUERY_STRING"]."&start=$this->start&display=$this->display&sort=$this->sort&srch=$this->srch&srch2=$this->srch2");

        /*
          ?><td align=center><?
          $this->Spr->ShowActSprInCombo(TblModCommentsSprCity, 'fltr2', $this->fltr2, $_SERVER["QUERY_STRING"]."&start=$this->start&display=$this->display&sort=$this->sort&srch=$this->srch&srch2=$this->srch2");
         */
        if ($rows > $this->display)
            $ch = $this->display;
        else
            $ch = $rows;
        ?>
        <tr>
            <td colspan="10" valign="top" style="height: 60px;background:#E5F2FF;padding:15px 0 0 20px;">
                <table cellpadding="3" cellspacing="5">
                    <tr>
                        <td>Модуль:</td>
                        <td>
                            <select name="fltr" style="border: solid 1px #BFBFBF;">
                                <option value=""></option>
                                <?
                                $keys = array_keys($this->listModules);
                                $cnt = count($this->listModules);
                                for($i=0;$i<$cnt;$i++){
                                    if($this->fltr==$keys[$i]) $sel = 'selected';
                                    else $sel = '';
                                    ?><option value="<?=$keys[$i]?>" <?=$sel;?>><?=$this->listModules[$keys[$i]];?></option><?
                                }
                                ?>
                            </select>
                        </td>
                        <td>Позиция:</td>
                        <td><input name="fltr2" size="30" style="border: solid 1px #BFBFBF;" value="<?=$this->fltr2;?>" /></td>
                        <td>Пользователь (ФИО/логин):</td>
                        <td><input name="srch" size="20" style="border: solid 1px #BFBFBF;" value="<?=$this->srch;?>" /></td>
                        <td><input type="submit" value="Поиск" /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <TR>
            <Th class="THead" style="width: 32px;"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?= $ch; ?>); this.value = '0';} else {checkAll(<?= $ch; ?>); this.value = '1';}" type="checkbox"></Th>
            <Th class="THead" style="width: 40px;"><? $this->Form->LinkTitle($this->script . '&sort=id', $this->multi['FLD_ID']); ?></Th>
            <Th class="THead" style="width: 100px;"><?= $this->multi['_FLD_MODULE'] ?></Th>
            <Th class="THead" style="width: 100px;"><?= $this->multi['_FLD_POSITION'] ?></Th>
            <Th class="THead" style="width: 100px;"><?= $this->multi['FLD_USER_ID'] ?></Th>
            <? /*
              <td class="THead"><A HREF=<?=$this->script?>&sort=city_d><?=$this->multi['FLD_CITY'])?></A></Th>
             */ ?>
            <Th class="THead"><?= $this->multi['FLD_TEXT'] ?></Th>
            <Th class="THead" style="width: 135px;"><?= $this->multi['FLD_DATE'] ?></Th>
            <Th class="THead" style="width: 50px;"><?= $this->multi['_FLD_VISIBLE'] ?></Th>
            <?
            $a = $rows;
            $j = 0;
            $up = 0;
            $down = 0;
            $row_arr = NULL;
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->Rights->db_FetchAssoc();
                if ($i >= $this->start && $i < ( $this->start + $this->display )) {
                    $row_arr[$j] = $row;
                    $j = $j + 1;
                }
            }

            $style1 = 'TR1';
            $style2 = 'TR2';
            for ($i = 0; $i < count($row_arr); $i++) {
                $row = $row_arr[$i];

                if ((float) $i / 2 == round($i / 2)) {
                    echo '<TR CLASS="' . $style1 . '">';
                }
                else
                    echo '<TR CLASS="' . $style2 . '">';

                echo '<TD>';
                $this->Form->CheckBox("id_del[]", $row['id'], null, "check" . $i);
                echo '<TD><a name="id'.$row['id'].'">' . $row['id'].'</a>';
                $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['TXT_EDIT'] );
                echo '<TD align=center>' . stripslashes($this->Spr->GetNameByCod(TblSysSprFunc, $row['id_module'])) . '</TD>';
                //   echo "<br> row['id_module'] = ".$row['id_module'];
                echo '<TD align=center>'.$this->getItemName($row['id_module'],$row['id_item']);

                $U = new SysUser();
                $module_u = 35;
                echo '<TD align=center>';
                if(!empty($row['name']) AND !empty($row['id_user'])) $name = '<a href="/admin/index.php?module='.$module_u.'&task=edit&id='.$row['id_user'].'">'.stripslashes($row['name']).'</a>';
                elseif(!empty($row['name']) AND empty($row['id_user'])) $name = stripslashes($row['name']);
                elseif(empty($row['name']) AND !empty($row['id_user'])) $name = '<a href="/admin/index.php?module='.$module_u.'&task=edit&id='.$row['id_user'].'">'.$U->GetUserLoginByUserId($row['id_user']).'</a>';
                else $name = '';
                echo $name;
                if( !empty($row['email'])) echo ' '.stripslashes($row['email']);
                $U->GetUserLoginByUserId($row['id_user']);
                        echo '</TD>';

                echo '<td align="left">';
                if($row['level']>0){
                    echo 'Ответ к отзыву <a href="#id'.$row['level'].'">#'.$row['level'].'</a>: <br/>';
                }
                echo stripslashes($row['text']);
                echo '</td>';
                echo '<td align="center" style="padding: 5px; 0px 5px 0px;">';
                //echo '<br>$row[dt]='.$row['dt'];
                $start_date_val=$row['dt']; //strftime('%Y-%m-%d %H:%M', $row['dt']);
                //echo '<br>$start_date_val='.$start_date_val;
                $a1 = array('firstDay'       => 1, // show Monday first
                         'showsTime'      => true,
                         'showOthers'     => true,
                         'ifFormat'       => '%Y-%m-%d %H:%M',
                         'timeFormat'     => '12');
                $a2 = array('style'       => 'width: 100%; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                         'name'        => 'dt',
                         'value'       => $start_date_val);
                $calendar->make_input_field( $a1, $a2, 'propdt'.$row['id'] );

                $name = date('Y-m-d H:i:s', strtotime($row['dt']) );
                //$this->Form->Textbox('dt'.$row['id'], $name, 'style="width:100%;" id="propdt'.$row['id'].'"');
                ?>
                <div>
                    <div id="propdtres<?=$row['id'];?>"></div>
                    <div style="float:left;"><?$this->Form->Button('save_dt',$this->multi['TXT_SAVE'], NULL, "onclick='SaveDt(\"".$row['id']."\"); return false;'");?></div>
                </div>
                <?
                echo '</td>';
                echo '<TD align="center">';
                if ($row['status'] < 2)
                    $n_ch = $row['status'] + 1;
                else
                    $n_ch = 0;
                ?>
            <div id="res_ch<?= $row['id']; ?>">
                <? /* <a href="#" onclick="makeRequest('/admin/modules/sys_comments/sys_comments.php', 'task=ch_stat&id=<?= $row['id']; ?>&status=<?= $n_ch ?>', 'res_ch<?= $row['id']; ?>');"> */ ?>
                <a href="#" onclick="QuickChangeData('res_ch<?= $row['id']; ?>', 'module=<?= $this->module; ?>&task=ch_stat&id=<?= $row['id']; ?>&status=<?= $n_ch ?>');" >
                    <?
                    if ($row['status'] == 0)
                        $this->Form->Img('http://' . NAME_SERVER . '/admin/images/icons/publish_x.png', $this->multi['TXT_UNVISIBLE'], 'border=0');
                    if ($row['status'] == 1)
                        $this->Form->Img('http://' . NAME_SERVER . '/admin/images/icons/tick.png', $this->multi['_TXT_VISIBLE'], 'border=0');
                    if ($row['status'] == 2)
                        $this->Form->Img('http://' . NAME_SERVER . '/admin/images/icons/publish_g.png', $this->multi['_FLD_MODERATION'], 'border=0');
                    ?></a>
            </div>
            </td><?
            $up = $row['id'];
            $a = $a - 1;
        } //-- end for
        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;
    } //end of fuinction show

    /**
    * Class method getItemName
    * return name of the item position in current module
    * @return true/false:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 25.06.2012
    */
    function getItemName($module, $item) {
        switch ($module) {
            case '21':
                $tbl = TblModCatalogPropSprName;
                $nameItem = stripslashes($this->Spr->GetNameByCod($tbl, $item));
                break;
            case '32':
                $tbl = TblModArticleTxt;
                $nameItem = stripslashes($this->Spr->GetNameByCod($tbl, $item));
                break;
            case '37':
                $q = "SELECT `pname` FROM `" . TblModPagesTxt . "` WHERE `cod`='" .$item . "'";
                $res = $this->db->db_Query($q);
                $res_data = $this->db->db_FetchAssoc();
                $nameItem = stripslashes($res_data['pname']);
                break;
            case '24':
                $tbl = TblModNewsSprSbj;
                $nameItem = stripslashes($this->Spr->GetNameByCod($tbl, $item));
                break;
            case '87':
                $tbl = TblModGalleryTxt;
                $nameItem = stripslashes($this->Spr->GetNameByCod($tbl, $item));
                break;
            case '83':
                $tbl = TblModVideoTxt;
                $nameItem = stripslashes($this->Spr->GetNameByCod($tbl, $item));
                break;
            default:
                //$tbl = TblModPagesTxt;
                $nameItem = null;
                break;
        }
        return $nameItem;
    }
    /**
    * Class method change_stat
    * change status of comment
    * @return true/false:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 25.06.2012
    */
    function change_stat() {
        $q = "UPDATE `" . TblSysModComments . "` SET
              `status`='$this->status'
              WHERE `id` = '$this->id'";
        $res = $this->Rights->Query($q, $this->user_id, $this->module);
        //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if (!$res OR !$this->Rights->result)
            return false;

        if ($this->status < 2)
            $n_ch = $this->status + 1;
        else
            $n_ch = 0;
        ?>
        <a href="#" onclick="QuickChangeData('res_ch<?= $this->id; ?>', 'module=<?= $this->module; ?>&task=ch_stat&id=<?= $this->id; ?>&status=<?= $n_ch ?>');">
            <?
            if ($this->status == 0)
                $this->Form->Img('http://' . NAME_SERVER . '/admin/images/icons//publish_x.png', $this->multi['TXT_UNVISIBLE'], 'border=0');
            if ($this->status == 1)
                $this->Form->Img('http://' . NAME_SERVER . '/admin/images/icons/tick.png', $this->multi['_TXT_VISIBLE'], 'border=0');
            if ($this->status == 2)
                $this->Form->Img('http://' . NAME_SERVER . '/admin/images/icons/publish_g.png', $this->multi['_FLD_MODERATION'], 'border=0');
            ?>
        </a>
        <?
    } // end of function change_stat

    /**
    * Class method change_stat
    * change status of comment
    * @return true/false:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 25.06.2012
    */
    function changeDt() {
        //echo '<br>$this->dt='.$this->dt;
        $a = strptime($this->dt, '%Y-%m-%d %H:%M');
        $timestamp = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
        //echo '<br>$timestamp='.$timestamp.' date='.date("Y-m-d H:i:s", $timestamp);
        $q = "UPDATE `" . TblSysModComments . "` SET
              `dt`='".$timestamp."'
              WHERE `id` = '$this->id'";
        $res = $this->Rights->Query($q, $this->user_id, $this->module);
        //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if (!$res OR !$this->Rights->result)
            return false;
        return true;
    }


    // ================================================================================================
    // Function : ShowContentFilters
    // Version : 1.0.0
    // Date : 20.08.2008
    //
	   // Parms :
    // Returns : true,false / Void
    // Description : Show content of the catalogue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 05.12.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowContentFilters() {
        /* Write Table Part */
        AdminHTML::TablePartH();
        //phpinfo();
        ?>
        <table border=0 cellpadding=0 cellspacing=0>
            <tr valign=top>
                <? /*
                  <td>
                  <table border=0 cellpadding=2 cellspacing=1>
                  <tr><td><h4><?=$this->multi['TXT_FILTERS'];?></h4></td></tr>
                  <tr class=tr1>
                  <td align=left><?=$this->multi['FLD_GROUP'];?></td>
                  <td align=left><?$this->Spr->ShowActSprInCombo(TblModDealerSprGroup, 'fltr', $this->fltr, $_SERVER["QUERY_STRING"]);?></td>
                  </tr>
                  <tr class=tr2>
                  <td align=left><?=$this->multi['FLD_CITY'];?></td>
                  <td align=left><?$this->Spr->ShowActSprInCombo(TblModDealerSprCity, 'fltr2', $this->fltr2, $_SERVER["QUERY_STRING"]);?></td>
                  </tr>
                  </table>
                  </td>
                  <td width=30></td>
                 */ ?>
                <td>
                    <table border="0" cellpadding="3" cellspacing="4">
                        <tr><td><b><?= $this->multi['_FLD_LEGEND']; ?></b></td></tr>
                        <tr class=tr1>
                            <td align="center"><img src="http://<?= NAME_SERVER; ?>/admin/images/icons/publish_x.png"></td>
                            <td><?= $this->multi['TXT_UNVISIBLE'] ?></td>
                        <tr class=tr2>
                            <td align="center"><img src="http://<?= NAME_SERVER; ?>/admin/images/icons/tick.png"></td>
                            <td><?= $this->multi['_TXT_VISIBLE'] ?></td>
                        <tr class=tr1>
                            <td align="center"><img src="http://<?= NAME_SERVER; ?>/admin/images/icons/publish_g.png"></td>
                            <td><?= $this->multi['_FLD_MODERATION'] ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?
        AdminHTML::TablePartF();
    } //end of fuinction ShowContentFilters()

   // ================================================================================================
   // Function : ShowJS()
   // Version : 1.0.0
   // Date : 08.08.2007
   // Parms :
   // Returns : true,false / Void
   // Description : show form with rating from users about goods
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2007
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowJS()
   {
       ?>
        <script type="text/javascript">
            function QuickChangeData(div_id, mydata){
                did = "#"+div_id;
                $.ajax({
                    type: "POST",
                    data: mydata,
                    url: "/admin/modules/sys_comments/sys_comments.php",
                    success: function(msg){
                        //alert(msg);
                        $(did).html( msg );
                    },
                    beforeSend : function(){
                        //$("#sss").html("");
                        $(did).html('<div style="text-align:center;"><img src="/admin/images/ajax-loader.gif" alt="" title="" /></div>');
                    }
                });
            } // end of function QuickChangeData

            function SaveDt(id_prop){
                did = "#propdtres"+id_prop;
                val = $("#propdt"+id_prop).val();
                $("#<?=$this->Form->name;?>.task").val("savedt");
                mydata = "module=<?=$this->module;?>&task=savedt&id="+id_prop+"&lang_id=<?=$this->lang_id;?>&dt="+val;
                //alert('val='+val+' mydata='+mydata);
                $.ajax({
                        type: "POST",
                        data: mydata,
                        url: "/admin/modules/sys_comments/sys_comments.php",
                        success: function(msg){
                            $(did).html( msg );
                        },
                        beforeSend : function(){
                            $(did).html('<div style="text-align:center;"><img src="/admin/images/icons/loading_animation_liferay.gif" alt="" title="" /></div>');
                        }
                });
            } // end of function SaveName
        </script>
        <?
   }//end of function ShowJS()

// ================================================================================================
    // Function : edit()
    // Version : 1.0.0
    // Date : 05.12.2006
    //
	   // Parms :
    // Returns : true,false / Void
    // Description : edit/add records in Comments module
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 05.12.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function edit() {
        $Panel = new Panel();
        $ln_sys = new SysLang();
        $Spr = new SysSpr();
        $mas = NULL;
        if ($this->id != NULL) {
            $q = "SELECT * FROM " . TblSysModComments . " where `id`='$this->id'";
            $res = $this->Rights->Query($q, $this->user_id, $this->module);
            if (!$res)
                return false;
            $mas = $this->Rights->db_FetchAssoc();
        }

        /* Write Form Header */
        $this->Form->WriteHeaderFormImg($this->script);

        $this->Form->Hidden('display', $this->display);
        $this->Form->Hidden('start', $this->start);
        $this->Form->Hidden('sort', $this->sort);
        $this->Form->Hidden('srch', $this->srch);
        $this->Form->Hidden('srch2', $this->srch2);
        $this->Form->Hidden('fltr', $this->fltr);
        $this->Form->Hidden('fltr2', $this->fltr2);
        $this->Form->Hidden('fln', $this->fln);
        $this->Form->Hidden('delimg', "");

        $settings=SysSettings::GetGlobalSettings();
        $this->Form->textarea_editor = $settings['editer']; //'tinyMCE';
        $this->Form->IncludeSpecialTextArea( $this->Form->textarea_editor );

        if ($this->id != NULL)
            $txt = $this->multi['TXT_EDIT'];
        else
            $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH($txt);
        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //-------------------------------------------------------------
        AdminHTML::PanelSimpleH();
        ?>
            <TR><TD><b><? echo $this->multi['FLD_ID'] ?>:</b>
                <TD width="90%">
                    <?
                    if ($this->id != NULL) {
                        echo $mas['id'];
                        $this->Form->Hidden('id', $mas['id']);
                    }
                    else
                        $this->Form->Hidden('id', '');
                    ?>
            <TR><TD><b><? echo $this->multi['_FLD_MODULE'] ?>:</b>
                <TD>
                    <?
                    if ($this->id != NULL)
                        $this->Err != NULL ? $id_module = $this->id_module : $id_module = $mas['id_module'];
                    else
                        $id_module = $this->id_module;
                    echo stripslashes($this->Spr->GetNameByCod(TblSysSprFunc, $mas['id_module']))
                    ?>
            <TR><TD><b><? echo $this->multi['FLD_VISIBLE'] ?>:</b>
                <TD>
                    <?
                    $arr_v[0] = $this->multi['TXT_UNVISIBLE'];
                    $arr_v[1] = $this->multi['TXT_VISIBLE'];

                    if ($this->id != NULL)
                        $this->Err != NULL ? $visible = $this->visible : $visible = $mas['visible'];
                    else
                        $visible = $this->visible;
                    $this->Form->Select($arr_v, 'visible', $visible);
                    ?>
            <TR><TD><b><? echo $this->multi['_FLD_IMAGE'] ?>:</b>
                <TD>
                    <table border=0 cellpadding=0 cellspacing=1 class="EditTable">
                        <tr>
                            <td><?
            if ($this->id != NULL)
                $this->Err != NULL ? $img = $this->img : $img = $mas['img'];
            else
                $img = $this->img;
            if (!empty($img)) {
                        ?><table border=0 cellpadding=0 cellspacing=5>
                                        <tr>
                                            <td class='EditTable'><?
                        $this->Form->Hidden('img', $img);
                        ?><a href="<?= Comments_Img_Path . $img; ?>" target="_blank" onmouseover="return overlib('<?= $this->multi['TXT_ZOOM_IMG']; ?>',WRAP);" onmouseout="nd();" alt="<?= $this->multi['TXT_ZOOM_IMG']; ?>" title="<?= $this->multi['TXT_ZOOM_IMG']; ?>"><?
                                    $this->ShowImage($img, 'size_width=150', 100, NULL, "border=0");
                        ?></a><br><?
                                        /*
                                          <img src="http://<?=NAME_SERVER?>/thumb.php?img=<?=Dealer_Img_Path.$img?>&size_auto=100" border=0 alt="<?=$this->Spr->GetNameByCod( TblModDealerSprName, $mas['id'], $this->lang_id ); ?>">
                                         */
                        ?>
                                            <td class='EditTable'><?
                                    echo Comments_Img_Full_Path . $img . '<br>';
                        ?><a href="javascript:form_comments.delimg.value='<?= $mas['img']; ?>';form_comments.submit();"><?= $this->multi['_TXT_DELETE_IMG']; ?></a><?
                        ?></table><?
                            echo '<tr><td><b>' . $this->multi['_TXT_REPLACE_IMG'] . ':</b>';
                        }
                    ?>
                                <INPUT TYPE="file" NAME="filename" size="40" VALUE="" />
                            </td>
                        </tr>
                    </table>
                    <?
                    if ($this->id == NULL) {
                        $arr = NULL;
                        $arr[''] = '';
                        $tmp_db = new DB();
                        $tmp_q = "select * from `" . TblModComments . "` order by move desc";
                        $res = $tmp_db->db_Query($tmp_q);
                        if (!$res)
                            return false;
                        $tmp_row = $tmp_db->db_FetchAssoc();
                        $move = $tmp_row['move'];
                        $move = $move + 1;
                        $this->Form->Hidden('move', $move);
                    }
                    else
                        $move = $mas['move'];
                    $this->Form->Hidden('move', $move);

                    echo '<TR><TD COLSPAN=2 ALIGN=left>';
                    $this->Form->WriteSavePanel($this->script);
                    $this->Form->WriteCancelPanel($this->script);
                    echo '</table>';
                    AdminHTML::PanelSimpleF();
                    AdminHTML::PanelSubF();

                    $this->Form->WriteFooter();
                    return true;
                }
//end of fuinction edit

    // ================================================================================================
    // Function : ShowErrBackEnd()
    // Date : 10.01.2006
    // Returns :     void
    // Description :  Show errors
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowErrBackEnd() {
        if ($this->Err) {
            echo '
        <fieldset class="err" title="' . $this->Msg->show_text('MSG_ERRORS') . '"> <legend>' . $this->Msg->show_text('MSG_ERRORS') . '</legend>
        <div class="err_text">' . $this->Err . '</div>
        </fieldset>';
        }
    }

                // ================================================================================================
                // Function : save()
                // Version : 1.0.0
                // Date : 05.12.2006
                //
	   // Parms :
                // Returns : true,false / Void
                // Description : Store data to the table
                // ================================================================================================
                // Programmer : Igor Trokhymchuk
                // Date : 05.12.2006
                // Reason for change : Creation
                // Change Request Nbr:
                // ================================================================================================
                function save() {
                    $this->Form->Hidden('display', $this->display);
                    $this->Form->Hidden('start', $this->start);
                    $this->Form->Hidden('sort', $this->sort);
                    $this->Form->Hidden('srch', $this->srch);
                    $this->Form->Hidden('srch', $this->srch2);
                    $this->Form->Hidden('fltr', $this->fltr);
                    $this->Form->Hidden('fltr2', $this->fltr2);
                    $this->Form->Hidden('fln', $this->fln);

                    $q = "SELECT * FROM " . TblModComments . " WHERE `id`='$this->id'";
                    $res = $this->Rights->Query($q, $this->user_id, $this->module);
                    if (!$res OR !$this->Rights->result)
                        return false;
                    $rows = $this->Rights->db_GetNumRows();
                    //echo '<br>$q='.$q.'$rows='.$rows;
                    if ($rows > 0) {   //--- update
                        $row = $this->Rights->db_FetchAssoc();
                        //Delete old image
                        //echo '<br>$row[img]='.$row['img'].' $this->img='.$this->img;
                        if (!empty($row['img']) AND $row['img'] != $this->img) {
                            $this->DelItemImage($row['img']);
                        }

                        $q = "update `" . TblModComments . "` set
				  `group_d`='$this->group_d',
				  `city_d`='$this->city_d',
				  `img` = '$this->img',
				  `visible`='$this->visible',
				  `move`='$this->move' WHERE `id` = '$this->id'";
                        $res = $this->Rights->Query($q, $this->user_id, $this->module);
                        //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
                        if (!$res OR !$this->Rights->result)
                            return false;
                    }
                    else {   //--- insert
                        /*
                          $q="select * from `".TblModDealers."` where 1";
                          $res = $this->Rights->Query( $q, $this->user_id, $this->module );
                          $rows = $this->Rights->db_GetNumRows();
                          $maxx=0;  //add link with position auto_incremental
                          for($i=0;$i<$rows;$i++)
                          {
                          $my = $this->Rights->db_FetchAssoc();
                          if($maxx < $my['move'])
                          $maxx=$my['move'];
                          }
                          $maxx=$maxx+1;
                         */

                        $q = "insert into `" . TblModComments . "` values(NULL, '$this->group_d', '$this->city_d', '$this->img', '$this->visible', '$this->move')";
                        $res = $this->Rights->Query($q, $this->user_id, $this->module);
                        //echo '<br>'.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
                        if (!$res OR !$this->Rights->result)
                            return false;
                    }

                    if (empty($this->id)) {
                        $this->id = $this->Rights->db_GetInsertID();
                    }

                    // Save Description on different languages
                    $res = $this->Spr->SaveNameArr($this->id, $this->name, TblModCommentsSprName);
                    if (!$res)
                        return false;
                    $res = $this->Spr->SaveNameArr($this->id, $this->content, TblModCommentsSprDescr);
                    if (!$res)
                        return false;
                    return true;
                }

//end of fuinction save()
                // ================================================================================================
                // Function : del()
                // Version : 1.0.0
                // Date : 05.12.2006
                //
	   // Parms :
                // Returns :      true,false / Void
                // Description :  Remove data from the table
                // ================================================================================================
                // Programmer :  Igor Trokhymchuk
                // Date : 05.12.2006
                // Reason for change : Creation
                // Change Request Nbr:
                // ================================================================================================
                function del($id_del) {
                    $kol = count($id_del);
                    $del = 0;
                    for ($i = 0; $i < $kol; $i++) {
                        $u = $id_del[$i];
                        $q = "DELETE FROM `" . TblSysModComments . "` WHERE id='$u'";
                        $res = $this->Rights->Query($q, $this->user_id, $this->module);
                        if (!$res)
                            return false;
                        if ($res)
                            $del = $del + 1;
                        else
                            return false;
                    }
                    return $del;
                }

//end of fuinction del()
                // ================================================================================================
                // Function : CheckFields()
                // Version : 1.0.0
                // Date : 05.12.2006
                //
	   // Parms :        $id - id of the record in the table
                // Returns :      true,false / Void
                // Description :  Checking all fields for filling and validation
                // ================================================================================================
                // Programmer :  Igor Trokhymchuk
                // Date : 05.12.2006
                // Reason for change : Creation
                // Change Request Nbr:
                // ================================================================================================
                function CheckFields($id = NULL) {
                    $this->Err = NULL;
                    /*
                      if (empty( $this->group_d)) {
                      $this->Err = $this->Err.$this->multi['MSG_FLD_GROUP_EMPTY'].'<br>';
                      }

                      if (empty( $this->city_d)) {
                      $this->Err = $this->Err.$this->multi['MSG_FLD_CITY_EMPTY'].'<br>';
                      }
                     */
                    if (empty($this->name[$this->lang_id])) {
                        $this->Err = $this->Err . $this->multi['MSG_FLD_NAME_EMPTY'] . '<br>';
                    }

                    //echo '<br>$this->Err='.$this->Err.' $this->multi->table='.$this->multi->table;
                    return $this->Err;
                }

//end of fuinction CheckFields()
            }

            // End of class CommentsCtrl
            ?>
