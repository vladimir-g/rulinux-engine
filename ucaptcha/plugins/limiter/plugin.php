<?php
class limiter
{
 function generate_image($canvas){
  global $captcha_font_path,$captcha_img_path;   
  $nums = array();
  $signs = array();
  
  $limit = rand(-10, 10);
  $x_lim = 'x->'.$limit;
  $x_d = rand(1, 2);
  $free = rand(1, 20);
  $lim = 'lim(';
  $action_res = rand(0, 1);
  switch($action_res){
       case 0: $action = '+'; break;
       case 1: $action = '-'; break;
  }
            
  $signs[0] = $action;
  $nums[0] = $free;
  $lim .= $free.$action;
  
  for ($i = 1; $i <= $x_d; $i++){
       $x = rand(1, 20);
       if($i == 1)
            $lim .= $x.'x';
       else
            $lim .= $x.'x^'.$i;
       $action_res = rand(0, 1);
       switch($action_res){
            case 0: $action = '+'; break;
            case 1: $action = '-'; break;
       }
       $lim .= $action;
       $signs[$i] = $action;
       $nums[$i] = $x;
  }
  array_pop($signs);
  $lim = substr($lim, 0, (strlen($lim)-1));
  $lim .= ')';
  $answer = 0;
  foreach($signs as $i => $sign){
       switch($sign){
            case '+':
                 //echo '<br>'.$answer .'+'. ($nums[$i+1]*pow($limit, $i+1));
                 if ($i <= 0)
                      $answer = ($nums[$i]*pow($limit, $i));
                 $answer += ($nums[$i+1]*pow($limit, $i+1));
            break;
            case '-':
                 //echo '<br>'.$answer .'-'. ($nums[$i+1]*pow($limit, $i+1));
                 if ($i <= 0)
                      $answer = ($nums[$i]*pow($limit, $i));
                 $answer -= ($nums[$i+1]*pow($limit, $i+1));
            break;
       }
  }
  for ($i=0;$i<strlen($lim);$i++){
    $color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
    imagefttext($canvas, 12, rand (-20,20), 12+8*$i, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $lim[$i]);
   }
   imagefttext($canvas, 12, rand (-20,0), 12+8, 65, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $x_lim);
   imagepng($canvas);
   $nme=$this->ucaptcha->get_filename();
   $captcha[0]=$nme;
   $captcha[1]=$answer;
   return $captcha;
  }
}
?>