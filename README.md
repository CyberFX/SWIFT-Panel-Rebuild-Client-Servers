## SWIFT Panel Rebuild/Reinstall Client Servers
>An add-on for [swift game panel](http://www.swiftpanel.com/), clients can `RE-Install` their server!

[![License](http://img.shields.io/badge/license-GNU-brightgreen.svg?style=flat-square)](https://github.com/CyberFX/SWIFT-Panel-Rebuild-Client-Servers/blob/master/LICENSE) [![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/CyberFX995)

### Description
> Default [swift game panel](http://www.swiftpanel.com/) is very popular and interesting, however some of the options are lacking such as client server reinstallation.
> This script will allow clients to `self-reinstall` the server whenever they want and of course it will be logged in logs.
> Script support all games in panel.

### Instalation
> Just import [serverrebuild.php](https://github.com/CyberFX/SWIFT-Panel-Rebuild-Client-Servers/blob/master/UPLOAD_ME/serverrebuild.php) in root of game panel.

### Implementation (in default template for example)
> In [templates/default/serversummary.tpl](https://github.com/CyberFX/SWIFT-Panel-Rebuild-Client-Servers/blob/master/UPLOAD_ME/templates/default/serversummary.tpl) is already set-up, just replace existing `serversummary.tpl` in default template folder.

- Open templates/default/serversummary.tpl file and find next line:
```html
<input type="button" value="Start Server" onclick="window.location='servermanage.php?task=start&amp;serverid={$srv.serverid}'" class="button green start" />
```

- Add this code after previus:
```html
<input type="button" value="Reinstall Server" onclick="doServerRebuild('{$srv.serverid}', '{$srv.name}')" class="button blue restart" />
{literal}
<script language="javascript" type="text/javascript">
<!--
function doServerRebuild(serverid, name){ if (confirm("Are you sure you want to rebuild server: #"+serverid+" - "+name+"? \n\nAll files will be deleted from server!")) { window.location="serverrebuild.php?task=serverrebuild&serverid="+serverid; } }
-->
</script>
{/literal}
```

- Delete cache files from `templates_c` folder and that's it! Enjoy!
> Reinstall button will bi show only when server stoped, also reinstall proccess only work when server is stoped!

### Have problem?
> If have problem with integration, i can finish this for you for small donation, just send me message on mail, facebook or here!

### License
[GPL-3.0](https://github.com/CyberFX/SWIFT-Panel-Rebuild-Client-Servers/blob/master/LICENSE)

### Donation
If you appreciate my work and if this script helped you, treat me a coffee... ^_^

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/CyberFX995)
