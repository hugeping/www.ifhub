<?php
/*-------------------------------------------------------
*
*   StickyTopics v2
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail:kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

$config=array();

// Встраивать прикрепленные топики в общую ленту? Они в любом случае передаются в шаблонную переменную $aStickyTopics
$config['sticky_topics_in_feed']=true;

// Разрешать пользователям закреплять топики в списке их топиков на их страничке /profile/ХХХХ/created/topics/
$config['allow_personal_sticky_topics']=true;

// Разрешать пользователям закреплять на их страничках:
// любые доступные им топики - all
// написанные ими - self
// написанные только в персональный блог - personal
$config['personal_sticky_topics_kind']='self';

// Разрешать закреплять в блогах:
// любые доступные  топики - all
// написанные только в этот блог - blog
$config['blog_sticky_topics_kind']='blog';


return $config;
?>