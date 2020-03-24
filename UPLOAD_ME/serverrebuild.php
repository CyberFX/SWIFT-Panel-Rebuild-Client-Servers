<?php
//====================================================================
//  Copyright © 2020 Stefan Nikolic (CyberFX) All Rights Reserved.
//  email: cyberdizajn@gmail.com | cyberdizajn[at]gmail[dot]com
//  Facebook: https://fb.com/cyberdesign
//  GitHub: https://github.com/CyberFX
//====================================================================
//*************************************************************************************************

$return = TRUE;
require("./configuration.php");
include("./include.php");
$task = addslashes($_POST['task']);
if(empty($task)){$task = addslashes($_GET['task']);}
$serverid = addslashes($_POST['serverid']);
if(empty($serverid)){$serverid = addslashes($_GET['serverid']);}
$clientid = addslashes($_SESSION['clientid']);

switch ($task){
	case "serverrebuild" :
		unset($_SESSION['msg1']);
		unset($_SESSION['msg2']);
		$rows = mysql_fetch_array(mysql_query("SELECT * FROM `server` WHERE `serverid` = '".$serverid."' && `clientid` = '".$clientid."'"));
		if(empty($rows['homedir']) || empty($rows['installdir'])){
			$_SESSION['msg1'] = 'Validation Error!';
			$_SESSION['msg2'] = 'Invalid Directory.';
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
		}
		if($rows['online'] == 'Started'){
			$_SESSION['msg1'] = 'Validation Error!';
			$_SESSION['msg2'] = 'Server must be stopped.';
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
		}
		if(!extension_loaded('ssh2')){
			$_SESSION['msg1'] = 'SSH2 Extension Error!';
			$_SESSION['msg2'] = 'SSH2 Extension not detected!';
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
		}
		$rows1 = mysql_fetch_array(mysql_query("SELECT `ip`, `sshport`, `login`, `password` FROM `box` WHERE `boxid` = '".$rows['boxid']."'"));
		if(!$sshconnection = @ssh2_connect($rows1['ip'], $rows1['sshport'])){
			$_SESSION['msg1'] = 'Connection Error!';
			$_SESSION['msg2'] = 'Unable to connect to box with SSH.';
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
		}
		if(!ssh2_auth_password($sshconnection, $rows1['login'], @base64_decode($rows1['password']))){
			$_SESSION['msg1'] = 'Authentication Error!';
			$_SESSION['msg2'] = 'Unable to login to box with SSH.';
			header('Location: serversummary.php?id='.urlencode($serverid));
			exit();
		}
		$sshshell = @ssh2_shell($sshconnection, 'vt102', null, 400, 80, SSH2_TERM_UNIT_CHARS);
@fwrite($sshshell, 'cd '.$rows['installdir'].'
');
sleep(1);
		
		while($sshline = fgets($sshshell)){
		if(preg_match('/No such file or directory/', $sshline)){
			$_SESSION['msg1'] = 'Command Error!';
			$_SESSION['msg2'] = 'Could not change to directory: ' . $rows['installdir'];
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
			continue;
		}
		}
		
@fwrite($sshshell, 'usermod '.$rows['user'].'
');
sleep(1);
		
		while($sshline = fgets($sshshell)){
		if(preg_match('/does not exist/', $sshline)){
			$_SESSION['msg1'] = 'Command Error!';
			$_SESSION['msg2'] = 'User does not exist: ' . $rows['user'];
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
			continue;
		}
		}
		
@fwrite($sshshell, 'cd '.$rows['homedir'].'
');
sleep(1);
		
		while($sshline = fgets($sshshell)){
		if(preg_match('/No such file or directory/', $sshline)){
			$_SESSION['msg1'] = 'Command Error!';
			$_SESSION['msg2'] = 'Could not change to directory: ' . $rows['homedir'];
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
			continue;
		}
		}
		
		if(empty($rows['homedir']) || empty($rows['installdir'])){
			$_SESSION['msg1'] = 'Validation Error!';
			$_SESSION['msg2'] = 'Invalid Directory.';
			header('Location: serversummary.php?id=' . urlencode($serverid));
			exit();
		}
	
@fwrite($sshshell, 'cd' . PHP_EOL);
sleep(1);
@fwrite($sshshell, "screen -m -S serverrebuild" . PHP_EOL);
sleep(2);
@fwrite($sshshell, "nice -n 19 rm -Rf ".$rows['homedir']."/* && nice -n 19 cp -Rf ".$rows['installdir']."/* ".$rows['homedir']." && chown -Rf ".$rows['user'].":".$rows['user']." ".$rows['homedir']." && exit" . PHP_EOL);
sleep(2);
@fclose($sshshell);
		$rows2 = mysql_fetch_array(mysql_query("SELECT `clientid`, `boxid`, `name` FROM `server` WHERE `serverid` = '".$serverid."' LIMIT 1"));
		$rows3 = mysql_fetch_array(mysql_query("SELECT `name` FROM `box` WHERE `boxid` = '".$rows2['boxid']."' LIMIT 1"));
		$message = 'Server Rebuilt: <a href="serversummary.php?id='.$serverid.'">'.$rows2['name'].'</a> on <a href="boxsummary.php?id='.$rows2['boxid'].'">'.$rows3['name'].'</a>';
		mysql_query( "INSERT INTO `log` SET `clientid` = '".$rows2['clientid']."', `serverid` = '".$serverid."', `boxid` = '".$rows2['boxid']."', `message` = '".$message."', `name` = '".$_SESSION['clientfirstname']." ".$_SESSION['clientlastname']."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		$_SESSION['msg1'] = 'Rebuild Successfully!';
		$_SESSION['msg2'] = 'The server has been rebuilt. Allow 5 minutes for server files to transfer before starting.';
		header('Location: serversummary.php?id=' . urlencode($serverid));
		exit();
	break;

	default:
		header("Location: index.php");
		exit();
}
?>
