<?php 
    langSetDefault('main','agerestrict');
?><table class="subcontent agerestrict"><tbody>
<tr class="header"><td class="age">18+</td><td class="logo"><img src="<?=BASE_URL?>/modules/Main/img/logo.png" /></td></tr>
<?php
    if(langCurrId()==2)://russian
?><tr class="confirm"><td colspan="2"><p class="focus">Сайт содержит информацию, предназначенную к просмотру лицам, достигшим совершеннолетнего возраста. Для доступа на сайт подтвердите, что Вам исполнилось 18 лет.</p><p>Сведения, предоставленные на сайте, носят исключительно информационный характер, не являются рекламой и предназначены только для личного использования.</p></td></tr>
<tr class="deny"><td colspan="2"><p>Уважаемый посетитель, мы вынуждены отказать вам в посещении сайта. Мы выступаем категорически против употребления алкоголя несовершеннолетними.</p></td></tr><?php
    else:
?><tr class="confirm"><td colspan="2"><p class="focus">This website is intended for adult viewing only. To access the website please confirm your age.</p><p>The information on the website is purely informational, it is not an advertisement, but for the personal use only.</p></td></tr>
<tr class="deny"><td colspan="2"><p>We have to decline your web site visiting because we are strongly against underage drinking.</p></td></tr><?php
    endif;
?>
<tr class="buttons"><td><input type="button" class="submitbtn confirm" value="<?=langTranslate('I\'m 18 or older','I\'m 18 or older')?>" /></td><td><input type="button" class="deny" value="<?=langTranslate('I am under 18','I am under 18')?>" /></td></tr>
</tbody></table><?php 
    langClean('main','agerestrict');