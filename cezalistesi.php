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
    'cezalistesi.php?page=add' => 'Yeni Ban Ekle',
    'cezalistesi.php?action=harita' => 'Harita Döngüleri'
);
// //

if($mybb->get_input('action') == '')
{   

    if($mybb->get_input('arama')) {
        $bid = $_GET['arama'];
        // $query = $db->query("SELECT COUNT(bid) as total FROM tf2turkiye_ceza.ceza_bans WHERE authid = '$bid'");

        $bid = $_GET['arama'];
        switch ($_GET['yontem']) {
            case 'steamid':
                $query = $db->query("SELECT COUNT(bid) as total FROM tf2turkiye_ceza.ceza_bans WHERE authid = '$bid'");
                break;
            case 'nick':
                $query = $db->query("SELECT COUNT(bid) as total FROM tf2turkiye_ceza.ceza_bans WHERE name = '$bid'");
                break;
            case 'sebep':
                $query = $db->query("SELECT COUNT(bid) as total FROM tf2turkiye_ceza.ceza_bans WHERE reason = '%$bid%'");
                break;
            case 'sure':
                $query = $db->query("SELECT COUNT(bid) as total FROM tf2turkiye_ceza.ceza_bans WHERE length = '$bid'");
                break;
            
            default:
                $query = $db->query("SELECT COUNT(bid) as total FROM tf2turkiye_ceza.ceza_bans WHERE authid = '$bid'");
                break;
        }
    }
    else {
        $query = $db->query("SELECT COUNT(bid) as total FROM tf2turkiye_ceza.ceza_bans");
    }
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


    if($mybb->get_input('arama')) {
        $bid = $_GET['arama'];
        switch ($_GET['yontem']) {
            case 'steamid':
                $query = $db->query("SELECT * FROM tf2turkiye_ceza.ceza_bans WHERE authid = '$bid' ORDER BY bid DESC LIMIT $offset, $perpage");
                break;
            case 'nick':
                $query = $db->query("SELECT * FROM tf2turkiye_ceza.ceza_bans WHERE name = '$bid' ORDER BY bid DESC LIMIT $offset, $perpage");
                break;
            case 'sebep':
                $query = $db->query("SELECT * FROM tf2turkiye_ceza.ceza_bans WHERE reason LIKE '%$bid%' ORDER BY bid DESC LIMIT $offset, $perpage");
                break;
            case 'sure':
                $query = $db->query("SELECT * FROM tf2turkiye_ceza.ceza_bans WHERE `length` = '$bid' ORDER BY bid DESC LIMIT $offset, $perpage");
                break;
            
            default:
                $query = $db->query("SELECT * FROM tf2turkiye_ceza.ceza_bans WHERE authid = '$bid' ORDER BY bid DESC LIMIT $offset, $perpage");
                break;
        }
        $lang->cl_istatistik = $lang->sprintf($lang->cl_istatistik, $total, $offset, $total);
    }
    else {
        $query = $db->query("SELECT * FROM tf2turkiye_ceza.ceza_bans ORDER BY bid DESC LIMIT $offset, $perpage");
        $lang->cl_istatistik = $lang->sprintf($lang->cl_istatistik, $total, $offset, $offset+$perpage);
    }

    while($sonuc = $db->fetch_array($query))
    {
        $tarih = my_date('relative', $sonuc['created']);
        $nick = ($sonuc['name'] == '' ? '<span class="font-weight-normal fz-11">' . $lang->isim_silinmis . '</span>' : strimwidth($sonuc['name']));
        $sebep = strimwidth($sonuc['reason'], 40);

        $bid = $sonuc['bid'];
        $yetkililer_q = $db->query('SELECT tf2turkiye_ceza.ceza_bans.bid, tf2turkiye_ceza.ceza_bans.aid, tf2turkiye_ceza.ceza_admins.aid , tf2turkiye_ceza.ceza_admins.user, tf2turkiye_ceza.ceza_admins.gid 
        FROM tf2turkiye_ceza.ceza_bans INNER JOIN tf2turkiye_ceza.ceza_admins ON tf2turkiye_ceza.ceza_bans.aid = tf2turkiye_ceza.ceza_admins.aid WHERE bid="' . $bid . '"');
        while($yetkililer = $db->fetch_array($yetkililer_q))
        {
            $yetkili_adi = yetkili_adi(strimwidth($yetkililer['user']), $yetkililer['gid']);
            $yetkili_authid = $yetkililer['authid'];
        }
        if($yetkili_adi == "")
            $yetkili_adi = '<span class="font-weight-normal fz-11">' . $lang->yetkili_silinmis . '</span>';

            
        $sid = $sonuc['sid'];
        $sunucular_q = $db->query('SELECT tf2turkiye_ceza.ceza_bans.sid, tf2turkiye_ceza.ceza_servers.sid, tf2turkiye_ceza.ceza_servers.ip 
        FROM tf2turkiye_ceza.ceza_bans INNER JOIN tf2turkiye_ceza.ceza_servers ON tf2turkiye_ceza.ceza_bans.sid = tf2turkiye_ceza.ceza_servers.sid WHERE tf2turkiye_ceza.ceza_bans.sid="' . $sid . '"');
        while($sunucular = $db->fetch_array($sunucular_q))
        {
            $sunucu_adi = sunucu_adi(strimwidth($sunucular['ip']), $sunucular['sid']);
        }
        if($sunucu_adi == "")
            $sunucu_adi = '<span class="font-weight-normal fz-11">' . $lang->sunucu_silinmis . '</span>';

        if($sonuc['ends'] > TIME_NOW && $sonuc['ends'] < strtotime('+1 day', TIME_NOW) ) {
            $kalanzaman = '<span class="d-block bg-info text-white shadow-sm rounded p-1 fz-10"><i class="fas fa-clock mx-2"></i>' . str_replace("İçinde","",my_date('relative', $sonuc['ends'], 0, 0)) . $lang->kaldi. '</span>';
        }
        else if($sonuc['ends'] > strtotime('+1 day', TIME_NOW) ) {
            $kalanzaman = '<span class="d-block bg-info text-white shadow-sm rounded p-1 fz-10"><i class="fas fa-clock mx-2"></i>' . secondsToTime($sonuc['length']) .  $lang->kaldi. '</span>';
        }
        else if($sonuc['length'] == 0) {
            $kalanzaman = '<span class="d-block bg-danger text-white shadow-sm rounded p-1 fz-10 text-center"><i class="fas fa-infinity mx-2"></i> </span>';
        }
        else {
            $kalanzaman = '<span class="d-block bg-success text-white shadow-sm rounded p-1 fz-10"><i class="fas fa-check mx-2"></i>' . $lang->suredoldu . '</span>';
        }

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
            if($mybb->usergroup['cancp'] == 1 ? $sil = '<a href="' . $mybb->settings['bburl'] . '/cezalistesi.php?action=delete&bid=' . $sonuc['bid'] . '">'. $lang->sil .'</a>' : '');
        }


        eval("\$liste_row .= \"".$templates->get("cl_liste_row")."\";");
    }

    
    


    eval('$anasablon  = "' . $templates->get('cl_anasayfa', 1, 0) . '";');
    output_page($anasablon);

}

if($mybb->get_input('action') == 'delete')
{   

    $bid = $_GET['bid'];

    if(isset($_POST['sil_dugme'])) {
        $db->query("DELETE FROM tf2turkiye_ceza.ceza_bans WHERE bid='".$bid."'");
    }

    eval('$anasablon  = "' . $templates->get('cl_delete', 1, 0) . '";');
    output_page($anasablon);
}
