<?php langSetDefault('sendmail', 'sendmail');
?><html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Wine Incognito<?=strlen($title)?': '.$title:''?></title>
</head>
<body>
    <div id="mailsub">
        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="760" class="pad_null">
            <tbody>
                <tr class="pad_null">
                    <td height="10"></td>
                </tr>
                <tr class="pad_null">
                    <td class="pad_null" height="63px" style="text-align: center;vertical-align:middle;<?=strlen($header_background_color)?'background-color: #'.$header_background_color.';':''?>"><?php
    if(strlen($header_logo_url)):
                        ?><a href="<?=BASE_URL?>" target="_blank" alt="Wine Incognito" title="Wine Incognito"><img src="<?=BASE_URL.$header_logo_url?>" alt="Wine Incognito" title="Wine Incognito" border="0" style="border:0px;"></a><?php 
    endif;
                    ?></td>
                </tr>
                <tr class="pad_null">
                    <td valign="bottom" align="center" class="pad_null"><img src="<?=BASE_URL?>/modules/Sendmail/img/line6.jpg" width="760" height="1" style="border:0; display:block;"></td>
                </tr>
                <tr class="pad_null">
                    <td class="pad_null" height="10"></td>
                </tr>
                <tr class="pad_null">
                    <td class="pad_null" style="text-align:left; font-size:11pt; font-family: Arial; color:#4D4D4D; text-align: left">
                        <?=$content?>
                    </td>
                </tr>
<?php if($full && strlen($footer_logo_url)): ?>
                <tr class="pad_null">
                    <td class="pad_null" height="10"></td>
                </tr>
                <tr class="pad_null">
                    <td valign="bottom" align="center" class="pad_null"><img src="<?=BASE_URL?>/modules/Sendmail/img/line6.jpg" width="760" height="1" style="border:0; display:block;"></td>
                </tr>
                <tr class="pad_null">
                    <td class="pad_null" height="36px" style="text-align: center;vertical-align:middle;<?=strlen($footer_background_color)?'background-color: #'.$footer_background_color.';':''?>">
                        <a href="<?=BASE_URL?>" target="_blank" alt="Wine Incognito" title="Wine Incognito"><img src="<?=BASE_URL.$footer_logo_url?>" alt="Wine Incognito" title="Wine Incognito" border="0" style="border:0px;"></a>
                    </td>
                </tr>
<?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>