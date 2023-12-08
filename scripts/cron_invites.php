<?
chdir(dirname(__FILE__));
error_reporting(E_ALL);
require_once('../config/config.php');
require_once(ROOT_PATH.'/config/includes.php');
require_once(ROOT_PATH.'/system/utils/functions.php');

$sql = "SELECT * FROM bv_invites WHERE sent = 0";
$invites = Invite::find_by_sql($sql);

echo "Sending invites\n";
foreach($invites as $i) {
	$user = User::find(array('id' => $i->user_id));
	
	if($i->type == 'page') {
		$p = Page::find(array('id' => $i->type_id));
		EmailHelper::send_invitepage_link($user, array('name' => $i->name, 'email' => $i->email, 'code' => $i->code, 'page_name' => $p->name, 'page_id' => $p->id));
	} else {
		EmailHelper::send_invite_link($user, array('name' => $i->name, 'email' => $i->email));
	}
	$i->sent = 1;
	$i->save();
	echo "Send invite ({$i->email})\n";
}
echo "Complete\n";
?>