<?php
$section_id = (int)$_GET['section'];
$subsection_id = (int)$_GET['subsection'];
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$title = $site_name.' - Добавить материал';
require 'classes/faq.class.php';
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
if(empty($_POST['submit_form']))
{
	if(empty($_GET['section']))
		require 'themes/'.$theme.'/templates/add_content/sections.tpl.php';
	else
	{
		$subsect = sections::get_subsections($section_id);
		if($section_id==1)
		{
			require 'themes/'.$theme.'/templates/add_content/news_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/news_bottom.tpl.php';
		}
		else if($section_id==2)
		{
			require 'themes/'.$theme.'/templates/add_content/articles_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/articles_bottom.tpl.php';
		}
		else if($section_id==3)
		{
			require 'themes/'.$theme.'/templates/add_content/gallery_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/gallery_bottom.tpl.php';
		}
		else if($section_id==4)
		{
			require 'themes/'.$theme.'/templates/add_content/forum_top.tpl.php';
			if(empty($_GET['subsection']))
			{
				require 'themes/'.$theme.'/templates/add_content/select_top.tpl.php';
				for($i=0; $i<count($subsect); $i++)
				{
					$subsect_id = $subsect[$i]['sort'];
					$subsection_name = $subsect[$i]['name'];
					require 'themes/'.$theme.'/templates/add_content/select_middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/add_content/select_bottom.tpl.php';
			}
			else
			{
				$subsect_id = $subsect[$subsection_id-1]['sort'];
				require 'themes/'.$theme.'/templates/add_content/select.tpl.php';
			}
			require 'themes/'.$theme.'/templates/add_content/forum_bottom.tpl.php';
		}
		else
		{
			$legend = 'Такой категории не существует';
			$text = 'Такой категории не существует';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
}
else
{
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
					$legend = 'Не удалось загрузить файл.';
					$text = 'Не удалось загрузить файл.';
					require 'themes/'.$theme.'/templates/fieldset.tpl.php';
					require 'themes/'.$theme.'/templates/footer.tpl.php';
					exit();
				}
			}
			else
			{
				$legend = $error;
				$text = $error;
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				require 'themes/'.$theme.'/templates/footer.tpl.php';
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
	}
	messages::new_thread($_POST['subject'], $_POST['comment'], $section, $_POST['subsection_id'], $file, $extension, $file_size, $image_size);
	if($section_id==1 || $section_id==2 || $section_id==3)
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'view-all.php">');  
	else if($section_id==4)
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'group.php?id='.$_POST['subsection_id'].'&page=1">');  
}
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>