<?php

define("IN_MYBB", 1);
define("THIS_SCRIPT", "cezalistesi.php");

require_once('./global.php');
require_once MYBB_ROOT . 'inc/plugins/cezalistesi/functions.php';

$lang->load("global");
$lang->load("cezalistesi");

add_breadcrumb($lang->cezalistesi, 'cezalistesi.php');

// MENU
$arrayName = array(
    'cezalistesi.php' => 'Ceza Listesi',
    'cezalistesi.php?page=add' => 'Yeni Ban Ekle'
);
// //

if($mybb->get_input('action') == '')
{   

    $query = $db->query("SELECT COUNT(bid) as total FROM ceza_bans");
    $total = $db->fetch_field($query, "total");

    if($mybb->settings['ppp']) // Using posts per page.  tpp for threads per page
    {
    $perpage = (int) $mybb->settings['ppp'];
    }
    else
    {
    $perpage = 15; // I chose 10, but you can put what you like
    }
    $pages = ceil($total / $perpage);

    if($mybb->input['page'])
    {
    $page = $mybb->get_input('page', 1);
    }
    else
    {
    $page = 1;
    }
    if($page < 1)
    {
    $page = 1;
    }
    if($page > $pages)
    {
    $page = $pages;
    }
    $pagination = multipage($total, $perpage, $page, 'cezalistesi.php');
    
    if ($total > 0) {
    $offset = ($page - 1) * $perpage; // forgive the lack of indentation, mybb keeps stripping out the tab characters
    }
    else {
    $offset = 0;
    }

    $query = $db->query("SELECT * FROM ceza_bans ORDER BY bid DESC LIMIT $offset, $perpage");
    while($sonuc = $db->fetch_array($query))
    {
        $tarih = my_date('relative', $sonuc['created']);
        $nick = ($sonuc['name'] == '' ? '<span class="font-weight-normal fz-11">' . $lang->isim_silinmis . '</span>' : strimwidth($sonuc['name']));
        $sebep = strimwidth($sonuc['reason'], 40);

        $bid = $sonuc['bid'];
        $yetkililer_q = $db->query('SELECT ceza_bans.bid, ceza_bans.aid, ceza_admins.aid , ceza_admins.user, ceza_admins.gid 
        FROM ceza_bans INNER JOIN ceza_admins ON ceza_bans.aid = ceza_admins.aid WHERE bid="' . $bid . '"');
        while($yetkililer = $db->fetch_array($yetkililer_q))
        {
            $yetkili_adi = yetkili_adi(strimwidth($yetkililer['user']), $yetkililer['gid']);
            $yetkili_authid = $yetkililer['authid'];
        }
        if($yetkili_adi == "")
            $yetkili_adi = '<span class="font-weight-normal fz-11">' . $lang->yetkili_silinmis . '</span>';

            
        $sid = $sonuc['sid'];
        $sunucular_q = $db->query('SELECT ceza_bans.sid, ceza_servers.sid, ceza_servers.ip 
        FROM ceza_bans INNER JOIN ceza_servers ON ceza_bans.sid = ceza_servers.sid WHERE ceza_bans.sid="' . $sid . '"');
        while($sunucular = $db->fetch_array($sunucular_q))
        {
            $sunucu_adi = sunucu_adi(strimwidth($sunucular['ip']), $sunucular['sid']);
        }
        if($sunucu_adi == "")
            $sunucu_adi = '<span class="font-weight-normal fz-11">' . $lang->sunucu_silinmis . '</span>';

        if($sonuc['ends'] > TIME_NOW)
            $kalanzaman = '<span class="d-block bg-info text-white shadow-sm rounded p-1 fz-10"><i class="fas fa-clock mx-2"></i>' . str_replace("İçinde","",my_date('relative', $sonuc['ends'])) . $lang->kaldi . '</span>';
        else if($sonuc['length'] == 0)
            $kalanzaman = '<span class="d-block bg-danger text-white shadow-sm rounded p-1 fz-10 text-center"><i class="fas fa-infinity mx-2"></i> </span>';
        else
            $kalanzaman = '<span class="d-block bg-success text-white shadow-sm rounded p-1 fz-10"><i class="fas fa-check mx-2"></i>' . $lang->suredoldu . '</span>';

        // ACCORDION
            $steamid64    = steamid64($sonuc['authid']); 
            $steamid32    = $sonuc['authid']; 
            $steamid3     = steamid(steamid64($sonuc['authid']));

            $ip           = ($mybb->usergroup['showforumteam'] == 1 ? $sonuc['ip'] : $lang->gizli);
            $baslangic    = my_date($mybb->settings['dateformat'] . ' ' . $mybb->settings['timeformat'], $sonuc['created'], "", 0);
            $bitis        = my_date($mybb->settings['dateformat'] . ' ' . $mybb->settings['timeformat'], $sonuc['ends'], "", 0);
            $sure         = ($sonuc['length'] > 0 ? secondsToTime($sonuc['length']) : $lang->sinirsizyazi);
            $cezayeri     = $sunucu_adi;
        // ACCORDION

        if( $mybb->usergroup['canmodcp'] == 1 || $yetkili_authid == steamid32($mybb->user['loginname']) )
        {
            $duzenle = '<a href="' . $mybb->settings['bburl'] . '/cezalistesi.php?action=edit&bid=' . $sonuc['bid'] . '">'. $lang->duzenle .'</a>';
            if($mybb->usergroup['issupermod'] == 1 ? $kaldir = '<a href="' . $mybb->settings['bburl'] . '/cezalistesi.php?action=unban&bid=' . $sonuc['bid'] . '">'. $lang->kaldir .'</a>' : '');
        }


        eval("\$liste_row .= \"".$templates->get("cl_liste_row")."\";");
    }

    
    
    $lang->cl_istatistik = $lang->sprintf($lang->cl_istatistik, $total, $offset, $offset+$perpage);

    eval('$anasablon  = "' . $templates->get('cl_anasayfa', 1, 0) . '";');
    output_page($anasablon);

}

if($mybb->get_input('action') != '')
{   
    $bid = $mybb->get_input('bid');
    $yetkililer_q = $db->query('SELECT ceza_bans.bid, ceza_bans.aid, ceza_admins.aid , ceza_admins.user, ceza_admins.gid, ceza_admins.authid
    FROM ceza_bans INNER JOIN ceza_admins ON ceza_bans.aid = ceza_admins.aid WHERE bid="' . $bid . '"');
    while($yetkililer = $db->fetch_array($yetkililer_q))
    {
        $yetkili_authid = $yetkililer['authid'];
    }
    // KONTROL
    if($yetkili_authid != steamid32($mybb->user['loginname']))
    {
        if( $mybb->usergroup['issupermod'] != 1 )
            error_no_permission();
    }
    // //
    
    foreach ($arrayName as $key => $value) {

        eval("\$cl_nav_row .= \"".$templates->get("cl_nav_row")."\";");
    }


    eval('$cl_nav  = "' . $templates->get('cl_nav', 1, 0) . '";');
}
if($mybb->get_input('action') == 'edit')
{
    
    $url = $_SERVER['REQUEST_URI'];
    add_breadcrumb($lang->duzenle, $url);

    $query = $db->query("SELECT * FROM ceza_bans WHERE bid ='" . $bid . "'");
    $sonuc = $db->fetch_array($query);

    if($db->num_rows($query) <= 0) {
        error($lang->kayitbulunamadi, $lang->error);
        exit();
    }

    if($_POST['name']    != '' ? $name = $_POST['name']       : $name = $sonuc['name']);
    if($_POST['authid']  != '' ? $authid = $_POST['authid']   : $authid = $sonuc['authid']);
    if($_POST['ip']      != '' ? $ip = $_POST['ip']           : $ip = $sonuc['ip']);
    if($_POST['reason']  != '' ? $reason = $_POST['reason']   : $reason = $sonuc['reason']);
    if($_POST['length']  != '' ? $length = $_POST['length']   : $length = $sonuc['length']);

    $query = $db->query("SELECT name FROM ceza_bansebebi");
    while($ceza = $db->fetch_array($query))
    {
        $value = $ceza['name'];
        
        if($reason == $value)
            $sec = 'selected';
        else
            $sec = '';

        eval("\$bansebepleri_row .= \"".$templates->get("cl_bansebebi_row")."\";");
    }

    if($_POST['reason'] == 'Diğer') {
        if($_POST['other'] != '')
            $new_reason = htmlspecialchars($_POST['other']);
        else
            $bildiri = hata($lang->cezasebebiyok);
    }
    else {
        $new_reason = $_POST['reason'];
    }
    if($count == 0 ? eval("\$other .= \"".$templates->get("cl_bansebebi_other")."\";") : '');

    $query = $db->query("SELECT length FROM ceza_bansebebi");
    while($ceza = $db->fetch_array($query))
    {
        $value = $ceza['length'];
        $minutes = $lang->dakika;

        if($length == $value)
            $sec = 'selected';
        else
            $sec = '';

        eval("\$banssureleri_row .= \"".$templates->get("cl_bansebebi_row")."\";");
    }

    if($_POST['submit']) {
        $query = $db->query("UPDATE ceza_bans SET name = '" . $name . "', ip = '" . $ip . "', reason = '" . $new_reason . "', length = '" . $length . "' WHERE bid ='" . $bid . "'");
        redirect($_SERVER['REQUEST_URI']. '&onay=1', $lang->kaydedildi, $lang->forum_redirect, true);
    }
    if($mybb->get_input('onay') == '1')
        $bildiri = onay($lang->kaydedildi);


    eval('$cl_edit  = "' . $templates->get('cl_edit', 1, 0) . '";');
    output_page($cl_edit);
}

if($mybb->get_input('action') == 'unban')
{
    $url = $_SERVER['REQUEST_URI'];
    add_breadcrumb($lang->cezakaldir, $url);
    if( $mybb->usergroup['issupermod'] != 1 )
        error_no_permission();
    
    if( $mybb->get_input('bid') == '' )
        redirect($_SERVER['REQUEST_URI'], $lang->unknown_error,$lang->error, true);
    else
      $bid = $mybb->get_input('bid');

    $query = $db->query("SELECT * FROM ceza_bans WHERE bid ='" . $bid . "'");
    $sonuc = $db->fetch_array($query);

    if($db->num_rows($query) <= 0) {
        error($lang->kayitbulunamadi, $lang->error);
        exit();
    }
    
    if($_POST['submit'])
    {
        if($_POST['kaldir_dugme'] != '') {
            $db->query("DELETE FROM ceza_bans
            WHERE bid = '" . $bid . "'");
            redirect($_SERVER['REQUEST_URI'], $lang->cezayikaldironay, $lang->cezayikaldironaybaslik, true);
        }
        else {
            $bildiri = hata($lang->cezayikaldirret);
        }
    }


    eval("\$cl_unban .= \"".$templates->get("cl_unban")."\";");
    output_page($cl_unban);
}
