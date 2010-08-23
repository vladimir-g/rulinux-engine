<?
$footer = $pagesC->get_templates('footer', $baseC->check_setting('template'));
if(class_exists('news')){
	$shortnews_res = $newsC->get_news('WHERE `active` = 1 AND `type` = 1', 'LIMIT 0, '.$baseC->eread('news_config', 'value', null, 'name', 'small'));
	$gallery_res = $newsC->get_news('WHERE `active` = 1', 'gallery');
}
if(class_exists('faq'))
	$shortfaq_res = $faqC->get_questions('WHERE `available` = 1', 'LIMIT 0, '.$baseC->eread('faq_config', 'value', null, 'name', 'small'));
$pagename = $_SERVER['SCRIPT_NAME'];
$pagename = str_replace(getcwd(), '', $pagename);

//$shortnews=$shortnews.'<a href="news.php" class="newstitle">Новости</a><br />';
$auth = '<div class="boxlet" style="padding: 2px">
<h2>Вход на сайт</h2>
[login]</div>';
$tracker = '<div class="boxlet" style="padding: 2px">
<h2>Последние 10 комментариев</h2>
[tracker]</div>';
$links = '<div class="boxlet" style="padding: 2px">
<h2>Ссылки</h2>
[menu]
</div>';
$faq = '<div class="boxlet" style="padding: 2px"><h2>F.A.Q.</h2>
[faq]
</div>';
$gall = '<div class="boxlet" style="padding: 2px"><div class="gallery-head">
<h2><a href="/gallery.php">Галерея</a></h2></div>
<div align="right">[<a href="add-content.php?type=2">Добавить</a>]</div>
[gallery]
</div>';
if($_COOKIE['remove_blocks'] == 1){
	$info['left_block'] = '';
	$info['right_block'] = '';
	$left_block = '';
	$right_block = '';
}
$left_block = explode(',', $info['left_block']);
$right_block = explode(',', $info['right_block']);
$i = 0;
foreach($left_block as $block){
	$buff= explode(':', $block);
	$left_block[$i] = $buff[1].'_'.$buff[0];
	$i++;
}
$i = 0;
foreach($right_block as $block){
	$buff = explode(':', $block);
	$right_block[$i] = $buff[1].'_'.$buff[0];
	$i++;
}
sort($left_block);
sort($right_block);

//<div class="boxlet">
//<h2>Вход на сайт</h2>
//[login]</div>
//
//<!-- boxes -->
//<div class="boxlet"><h2>F.A.Q.</h2>
//[faq]
//</div>
//<div class="boxlet">
//<h2>Ссылки</h2>
//[menu]
//</div>

$boxlets = '
</div>
</div>
</div>';
if (sizeof($left_block) > 0 && $left_block[0] != '_'){
$boxlets .= '<div class="column">

';
foreach($left_block as $left_part){
	preg_match('/^[0-9]+\_([a-z]+)$/', $left_part, $bname);
	$boxlets .= $$bname[1];
}
$boxlets .= '</div>';
}
if (sizeof($right_block) > 0 && $right_block[0] != '_'){
$boxlets .= '
<div class="column2">

';
foreach($right_block as $right_part){
	preg_match('/^[0-9]+\_([a-z]+)$/', $right_part, $bname);
	$boxlets .= $$bname[1];
}

$boxlets .= '</div>';
}
$trc = $baseC->other_query('SELECT `cid`, `tid`, `fid`, `subject`, SUBSTRING(`comment`, 1, 100) `com`, `nick` 
FROM `[prefix]comments` , `[prefix]users` 
WHERE `deleted` =0
AND `[prefix]comments`.`uid` = `[prefix]users`.`id`
AND `[prefix]comments`.`mconf` = 0
ORDER BY `timestamp` DESC 
LIMIT 0 , 10', 'assoc_array');
//print_r($trc);

$trc_c = '';
foreach($trc as $m){
   $tracker_content = '<a href="message.php?newsid='.$m['tid'].'&fid='.$m['fid'].'#'.$m['cid'].'" style="font-weight: bold">'.$m['subject'].' &rarr;</a><br>';
   $tracker_content .= strip_tags($m['com']).'...<br>';
   $tracker_content .= '<br><strong>'.$m['nick'].'</strong>';
   $tracker_content .= '(<a href="profile.php?user='.$m['nick'].'" style="font-weight: bold">*</a>)<br>';
   $tracker_content .= '<br><a href="message.php?newsid='.$m['tid'].'&fid='.$m['fid'].'#'.$m['cid'].'" style="font-weight: bold">Перейти >></a><hr>';
   $trc_c .= $tracker_content;
}

if (sizeof($shortnews_res)>0){
	if (sizeof($shortnews_res)>=$baseC->eread('news_config', 'value', null, 'name', 'small'))
		$to = $baseC->eread('news_config', 'value', null, 'name', 'small');
	else
		$to = sizeof($shortnews_res);
	for ($i=1; $i<=$to; $i++){
		$shortnews=$shortnews.'<a href="news.php?newsid='.$shortnews_res[$i]['id'].'" id="newsheader">'.$shortnews_res[$i]['title'].' </a><hr>';
		$shortnews=$shortnews.'<p id="newstext">'.$shortnews_res[$i]['desc'].'</p>';
		$shortnews=$shortnews.'<p id="newsbottom">Опубликована: '.$shortnews_res[$i]['timestamp'].'</p><br>';
	}
}

if (sizeof($shortfaq_res)>0){
	if (sizeof($shortfaq_res)>=$baseC->eread('nfaq_config', 'value', null, 'name', 'small'))
		$to = $baseC->eread('faq_config', 'value', null, 'name', 'small');
	else
		$to = sizeof($shortfaq_res);
	for ($i=1; $i<=$to; $i++){
		$shortfaq.='<tr>
        [?]<h3> '.$shortfaq_res[$i]['date'].'</h3>
		  <div class="news_text"><a href="faq.php">'.substr($shortfaq_res[$i]['question'], 0, 102).'</a>...</div>';
	}
}
if (sizeof($gallery_res)>0){
	$to = sizeof($gallery_res) < 3 ? sizeof($gallery_res) : 3;
	for ($i=1; $i<=$to; $i++){
		$gallery=$gallery.'<a href="message.php?newsid='.$baseC->eread('news', 'id', '', 'timestamp', $gallery_res[$i]['timestamp']).'">'.$gallery_res[$i]['title'].' </a>';
		$gallery=$gallery.'<p id="newstext"><a href="gallery/'.$gallery_res[$i]['file'].'.'.$gallery_res[$i]['extension'].'"><img src="gallery/thumbs/'.$gallery_res[$i]['file'].'_small.png"></a></p>';
		$gallery=$gallery.'<p id="newsbottom">Опубликована: '.$baseC->timeToSTDate($gallery_res[$i]['timestamp']).'<br>Автор: <a href="profile.php?user='.$gallery_res[$i]['by'].'">'.$gallery_res[$i]['by'].'</a></p><hr>';
	}
}

if (empty($_SESSION['user_login'])){if($_SESSION['user_admin'] == 1)	$moder = '* <a href="register.php">Модераторская конференция</a><br>';else	$moder = '';
$loginform='<form action="" method="post">
											<table>
											    <tr>
											        <td>Логин</td>
											        <td><input type="text" name="login"></td>
											    </tr>
												<tr>
											        <td>Пароль</td>
											        <td><input type="password" name="password"></td>
											    </tr>
											</table>
											<input type="submit" value="Войти">
											</form>											* <a href="profile.php?user='.$info['nick'].'">Профиль</a><br>											* <a href="page.php?id=1">Правила ресурса</a><br><br>											
											* <a href="register.php">Регистрация</a>';
}
else {	if($_SESSION['user_admin'] == 1){		$forums = $baseC->other_query('SELECT (MAX(forum_id) + 100) maxfid FROM [prefix]forums', 'assoc_array');		$new_fid = rand($forums['maxfid'], $forums['maxfid']+5000);				$moder  = '* <a href="add-message.php?fid='.$new_fid.'&mfid=1">Новая модераторская конференция</a><br>';		$read_conf = $baseC->other_query('			SELECT COUNT(admin.user) cnt			FROM admin, settings			WHERE admin.user='.$_SESSION['user_login'].'					AND admin.conf IN(SELECT value conf FROM settings WHERE name="last_conf")			', 'assoc_array');			$conf = $baseC->check_setting('last_conf');			$conf = explode(':', $conf);		if($read_conf[0]['cnt'] > 0)			$moder .= '* <a href="message.php?newsid='.$conf['1'].'&mconf">Последняя конференция &rarr;</a><br><br>';		else{			$moder .= '* <a href="message.php?newsid='.$conf['1'].'&mconf" style="font-weight: bold; color: yellow">Есть требующая ответа &rarr;</a><br><br>';		}	}	else		$moder = '';
	$group = $usersC->get_group($info['gid']);
					if ($user == 'anonymous')
						$health = 100;
					else
						$health = $baseC->eread('users', 'health', '', 'id', $_SESSION['user_login']);
				  switch ($health){
						case $health >= 70:
							$health_color = '#068200';
						break;
						case $health < 70 && $health >20:
							$health_color = '#e8a500';
						break;
						case $health <= 20:
							$health_color = '#ff0000';
						break;
				  }
	$loginform='
	<form action="?logout" method="post">
	Вы вошли как '.$info['nick'].' ('.$group['name'].')
	<div style="width:60px; border: 1px solid #000000; height:5px;" title="'.$health.'%"><div style="width:'.$health.'%; border: 1px solid '.$health_color.'; background-color:'.$health_color.'; height:3px" title="'.$health.'%"></div></div>('.$health.'%)<br />
	<input type="submit" value="Выйти">
	</form>
	* <a href="profile.php?user='.$info['nick'].'">Профиль</a><br>
	* <a href="page.php?id=1">Правила ресурса</a><br><br>	'.$moder.'
	* <a href="view-comments.php">Мои комментарии</a>
	';
}
$searchform='
<form action="search.php" method="get">
            <td colspan="3" align="center"><input name="keys" type="text" class="input_search" value=""></td>
            </tr>
          <tr>
            <td width="45" align="center" class="td_search"></td>
            <td width="52" align="center" class="td_search"></td>
            <td width="59" align="center"><input name="enter2" type="submit" class="buttom" id="enter2" value="поиск" align="right" ></td>
          </tr></form>';
$msearchform='<form action="msearch.php" method="get" style="width: 100%;">
<input type="text" value="'.urldecode($_GET['keys']).'" id="keys" name="keys" style="width: 100%;" type="text"><br /><br /><input value="Искать" type="submit">
<input type="button" value="Очистить" onclick="clean_field()">
</form>
<script>
function clean_field(){
	document.getElementById("keys").value = "";
}
</script>';
if (!SUB_PAGE)
	$footer=str_replace('[boxlets]', $boxlets, $footer);
else
	$footer=str_replace('[boxlets]', '', $footer);
	
$footer=str_replace('[tracker]', $trc_c, $footer);
$footer=str_replace('[news]', $shortnews, $footer);
$footer=str_replace('[gallery]', $gallery, $footer);
$footer=str_replace('[faq]', $shortfaq, $footer);
$footer=str_replace('[m-search]', $msearchform, $footer);
$footer=str_replace('[search]', $searchform, $footer);
$footer=str_replace('[menu]', $pagesC->get_menu('', true), $footer);
$footer=str_replace('[login]', $loginform, $footer);
$footer=str_replace('<div class="name2">', '<div class="name2">'.$content['title'], $footer);
echo '<!--footer section begin-->';
if ($_COOKIE['remove_blocks'])
	$rm_blocks = 'Показать блоки';
else
	$rm_blocks = 'Убрать блоки';
	
if ($_COOKIE['info_only'])
	$info_only = 'Обычная верстка';
else
	$info_only = 'Информационная верстка';
echo '<div align="center">[<a href="news.php?remove_blocks">'.$rm_blocks.'</a>]&nbsp;[<a href="news.php?info_only">'.$info_only.'</a>]</div>';
echo $footer;
echo '<!--footer section end-->';
echo '<!-- Yandex.Metrika -->
<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript"></script>
<div style="display:none;"><script type="text/javascript">
try { var yaCounter906590 = new Ya.Metrika(906590); } catch(e){}
</script></div>
<noscript><div style="position:absolute"><img src="//mc.yandex.ru/watch/906590"  alt="" /></div></noscript>
<!-- /Yandex.Metrika -->';
echo '</body>
</html>';if (isset($miniBB_gzipper_encoding)) {	$miniBB_gzipper_in = ob_get_contents();	$miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);	$miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);	$miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);	$gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);	$percent = round($gzpercent);	$miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);	ob_clean();	header('Content-Encoding: '.$miniBB_gzipper_encoding);	echo $miniBB_gzipper_out;}
?>
