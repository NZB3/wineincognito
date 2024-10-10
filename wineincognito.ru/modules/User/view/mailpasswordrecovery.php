

<p style="text-align:left; font-size:16pt; color:#4d4d4d;"><?php
    $currLangId = langCurrId();
    if($currLangId==2)://russian
?>Здравствуйте,<br /><br />
Перейдите по ссылке ниже для сброса Вашего пароля от сервиса <a href="<?=BASE_URL?>"><?=BASE_URL?></a>.<br /><br />
<a href="<?=BASE_URL?>/profile/password/reset/<?=$code?>">СБРОСИТЬ ПАРОЛЬ</a><br /><br />
Если Вы не запрашивали восстановление вашего пароля, Вы можете проигнорировать это письмо, и Ваш пароль не будет изменен.<?php
    else:
?>Hello,<br /><br />
Click the link below to reset your <a href="<?=BASE_URL?>"><?=BASE_URL?></a> account password.<br /><br />
<a href="<?=BASE_URL?>/profile/password/reset/<?=$code?>">RESET MY PASSWORD</a><br /><br />
If you did not request to change your password, you can ignore this email and your password will not be changed.<?php
    endif;
?></p>
