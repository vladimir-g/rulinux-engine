<?php
class bookz
{

 function get_random_question()
    {
    //
    $res[0]="Мой дядя самых честных правил,\nКогда не в шутку занемог\nОн уважать себя заставил...\n";
    $l=rand(0,2);
    switch($l)
    {
    case 0:
    $res[1]='Фамилия автора?'; //Получается из базы рандомно, может быть фамилия, имя, или отчество.
    $res[2]='Пушкин';
    break;
    case 1:
    $res[1]='Имя автора?'; //Получается из базы рандомно, может быть фамилия, имя, или отчество.
    $res[2]='Александр';
    break;
    case 2:
    $res[1]='Отчество автора?'; //Получается из базы рандомно, может быть фамилия, имя, или отчество.
    $res[2]='Сергеевич';
    break;
    }
    //$res[2]='FixMe';
    return $res;
    }

 function generate_image($canvas)
    {
    global $captcha_font_path,$captcha_img_path;
      $txt=$this->get_random_question();
      $rand=$txt[0];
      $lines=explode("\n",$rand);
      for ($i=0;$i<count($lines);$i++)
      {
      $color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
      imagefttext($canvas, 7+rand(0,3), rand (-5,5), 5, 10+$i*10, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $lines[$i]);
      }
      
      imagefttext($canvas, 8, rand (-5,5), 5, 10+$i*10, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $txt[1]);
      $nme=$this->ucaptcha->get_filename();
      //imagepng($canvas,$captcha_img_path."/".$nme.".png");
      imagepng($canvas);
      $captcha[0]=$nme;
      $captcha[1]=$txt[2];
      return $captcha;
    }
}
?>