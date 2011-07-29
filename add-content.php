<?php
require 'classes/core.php';
$section_id = (int)$_GET['section'];
$subsection_id = (int)$_GET['subsection'];
$title = ' - Добавить материал';
$rss_link='view-rss.php';
if(empty($_POST['submit_form']))
{
	require 'header.php';
	if ($_SESSION['user_id'] == 1 || users::get_captcha_level($_SESSION['user_id']) > -1)
		$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
	else
		$captcha = '';
	if(empty($_GET['section']))
		require 'themes/'.$theme.'/templates/add_content/add/sections.tpl.php';
	else
	{
		$subsect = sections::get_subsections($section_id);
		if($section_id==1)
		{
			require 'themes/'.$theme.'/templates/add_content/add/news_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/add/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/news_middle.tpl.php';
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				$checked_filter = '';
				require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/news_bottom.tpl.php';
		}
		else if($section_id==2)
		{
			require 'themes/'.$theme.'/templates/add_content/add/articles_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/add/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/articles_middle.tpl.php';
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				$checked_filter = '';
				require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/articles_bottom.tpl.php';
		}
		else if($section_id==3)
		{
			require 'themes/'.$theme.'/templates/add_content/add/gallery_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/add/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/gallery_middle.tpl.php';
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				$checked_filter = '';
				require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/gallery_bottom.tpl.php';
		}
		else if($section_id==4)
		{
			require 'themes/'.$theme.'/templates/add_content/add/forum_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/add/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/forum_middle.tpl.php';
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				$checked_filter = '';
				require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/forum_bottom.tpl.php';
		}
		else
		{
			$legend = 'Такой категории не существует';
			$text = 'Такой категории не существует';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	require 'footer.php';
}
else
{
	if($_POST['submit_form']=="Предпросмотр")
	{
		require 'header.php';
		$prooflink = $_POST['news_link'];
		$comment = $_POST['comment'];
		$subject = $_POST['subject'];
		$section_id = (int)$_GET['section'];
		$subsection_id = (int)$_POST['subsection_id'];
		$usr = users::get_user_info($_SESSION['user_id']);
		$author = $usr['nick'];
		$author_profile = '/profile.php?user='.$usr['nick'];
		$timestamp = core::to_local_time_zone(gmdate("Y-m-d H:i:s"));
		if($section_id == 1)
		{
			$subsection_image = '/themes/'.$theme.'/icons/'.sections::get_subsection_icon($subsection_id);
			require 'themes/'.$theme.'/templates/add_content/preview/news.tpl.php';
			require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
			$subsect = sections::get_subsections($section_id);
			for($i=0; $i<count($subsect); $i++)
			{
				$subsect_id = $subsect[$i]['sort'];
				$subsection_name = $subsect[$i]['name'];
				if($i== $subsection_id-1)
					$selected = 'selected';
				else
					$selected = '';
				require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
			require 'themes/'.$theme.'/templates/add_content/add/news_middle.tpl.php';
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				$num = $i+1;
				if(!empty($_POST['filter_'.$num]))
					$checked_filter = 'checked';
				else
					$checked_filter = '';
				require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/news_bottom.tpl.php';
		}
		else if($section_id ==2)
		{
			require 'themes/'.$theme.'/templates/add_content/preview/art.tpl.php';
			require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
			$subsect = sections::get_subsections($section_id);
			for($i=0; $i<count($subsect); $i++)
			{
				$subsect_id = $subsect[$i]['sort'];
				$subsection_name = $subsect[$i]['name'];
				if($i== $subsection_id-1)
					$selected = 'selected';
				else
					$selected = '';
				require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
			require 'themes/'.$theme.'/templates/add_content/add/articles_middle.tpl.php';
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				$num = $i+1;
				if(!empty($_POST['filter_'.$num]))
					$checked_filter = 'checked';
				else
					$checked_filter = '';
				require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/articles_bottom.tpl.php';
		}
		else if($section_id ==3)
		{
			$legend = 'Невозможно создать форму предпросмотра';
			$text = 'В разделе галлерея предпросмотр не реализован';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		else if($section_id ==4)
		{
			require 'themes/'.$theme.'/templates/add_content/preview/forum.tpl.php';
			require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
			$subsect = sections::get_subsections($section_id);
			for($i=0; $i<count($subsect); $i++)
			{
				$subsect_id = $subsect[$i]['sort'];
				$subsection_name = $subsect[$i]['name'];
				if($i== $subsection_id-1)
					$selected = 'selected';
				else
					$selected = '';
				require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
			require 'themes/'.$theme.'/templates/add_content/add/forum_middle.tpl.php';
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				$num = $i+1;
				if(!empty($_POST['filter_'.$num]))
					$checked_filter = 'checked';
				else
					$checked_filter = '';
				require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/add/forum_bottom.tpl.php';
		}
		require 'footer.php';
	}
	else
	{
		if ($_SESSION['user_id'] == 1 || users::get_captcha_level($_SESSION['user_id']) > -1)
		{
			if(empty($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring'])
			{
				require 'header.php';
				$legend = 'Неверно введен ответ с картинки';
				$text = 'Неверно введен ответ с картинки';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				require 'footer.php';
				exit();
			}
		}
		$sct=array(1, 2, 3, 4);
		if(!in_array($section_id, $sct))
			$section = 4;
		else
			$section = $section_id;
		if($section_id==3)
		{
			if(!empty($_FILES['scrot_link']))
			{
				$blacklist = array(".php", ".phtml", ".php3", ".php4");
				foreach ($blacklist as $item)
				{
					if(preg_match("/$item\$/i", $_FILES['scrot_link']['name']))
					{
						$error ='Неверный тип файла';
						exit;
					}
				}
				$uploaddir = 'gallery/';
				preg_match('/^.+(\.jp[e]?g|\.png|\.gif)$/', basename($_FILES['scrot_link']['name']), $ext);
				$filename = md5(time().basename($_FILES['scrot_link']['name']));
				$ext[1] = substr(basename($_FILES['scrot_link']['name']), strlen(basename($_FILES['scrot_link']['name']))-4, 4);
				$ext[1] = str_replace('.', '', $ext[1]);
				$uploadfile = $uploaddir.$filename.'.'.$ext[1];
							$imageinfo = getimagesize($_FILES['scrot_link']['tmp_name']);
				if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg'  && $imageinfo['mime'] != 'image/png')
				{
					$error = 'Неверный тип файла';
				}
				if(($_FILES['scrot_link']['size']/1000) > 700)
					$error = 'Слишком большой размер файла';
				if(($imageinfo[0] < 400 || $imageinfo[0] > 2048) || ($imageinfo[1] < 400 || $imageinfo[1] > 2048))
					$error = 'Ошибка загрузки файла';
				if (empty($error))
				{
					if (move_uploaded_file($_FILES['scrot_link']['tmp_name'], $uploadfile))
					{
						$coeff = $imageinfo[0]/200;
						$image_width = 200;
						@$image_height = floor($imageinfo[1]/$coeff);
						switch ($imageinfo[2])
						{
							case 1:
								$source = imagecreatefromgif($uploadfile);
								break;
							case 2:
								$source = imagecreatefromjpeg($uploadfile);
								break;
							case 3:
								$source = imagecreatefrompng($uploadfile);
								break;
						}
						$resource = imagecreatetruecolor($image_width, $image_height);
						imagecopyresampled($resource, $source, 0, 0, 0, 0, $image_width, $image_height, $imageinfo[0], $imageinfo[1]);
						imagePng($resource, $uploaddir.'/thumbs/'.$filename.'_small.png');
						$date = gmdate('d.m.Y H:i:s');
						$file = $filename;
						$extension = $ext[1];
						echo $ext[1];
						$file_size = $_FILES['scrot_link']['size'];
						$image_size = $imageinfo[0].'x'.$imageinfo[1];
					}
					else
					{
						require 'header.php';
						$legend = 'Не удалось загрузить файл.';
						$text = 'Не удалось загрузить файл.';
						require 'themes/'.$theme.'/templates/fieldset.tpl.php';
						require 'header.php';
						exit();
					}
				}
				else
				{
					require 'header.php';
					$legend = $error;
					$text = $error;
					require 'themes/'.$theme.'/templates/fieldset.tpl.php';
					require 'footer.php';
					exit();
				}

			}
		}
		else
		{
			$file = '';
			$extension = '';
			$file_size = 0;
			$image_size = '';
			$prooflink = $_POST['news_link'];
		}
		$cid = messages::new_thread($_POST['subject'], $_POST['comment'], $section, $_POST['subsection_id'], $file, $extension, $file_size, $image_size, $prooflink);
		$filters_count = filters::get_filters_count();
		for($i=1; $i<=$filters_count; $i++)
		{
			if(!empty($_POST['filter_'.$i]))
				$str = $str.$i.':1;';
			else
				$str = $str.$i.':0;';
		}
		$val = messages::set_filter($cid, $str);
		if($section_id==1 || $section_id==2 || $section_id==3)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'view-all.php">');
		else if($section_id==4)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'group.php?id='.$_POST['subsection_id'].'&page=1">');
	}
}
?>