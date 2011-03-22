<?php
//ucaptcha class library
$captcha_plugin_path="./plugins";
$captcha_font_path="./fonts";
$captcha_img_path="./cpt";
$captcha_tpl_path="./images";
//
// $ plugins [level] [number]
//
// $ captcha [0] - images name
//            1  - answer
//            
//            
require "plugs.dcfg.php";
class ucaptcha
{
  //We selet a random plug with the supplied level and gen the image
   function gen_image($level=0)
  {
  global $captcha_plug, $captcha_plugin_path;
  if (count($captcha_plug[$level])==0)
      {
      //echo "No plugs of that level";
      $captcha=NULL;
      }else
      {
      $pl=rand(0,count($captcha_plug[$level])-1);
      //"selecting $pl, ". $captcha_plugin_path."/".$captcha_plug[$level][$pl]."/plugin.php<br>";
      include $captcha_plugin_path."/".$captcha_plug[$level][$pl]."/plugin.php";
      //echo "inc";
      $generator = new $captcha_plug[$level][$pl];
      $generator->ucaptcha=$this;
      $canvas = $this->prepare_canvas();
      $captcha = $generator->generate_image($canvas);      
      }
  return $captcha;
  } 
// 2temy4:
/*
Эти функции надо заполнить кодом. В первом случае кидаем имя капчи и ответ в тобло, в другом лучае выбираем
*/
function db_store_answer($uuid, $answer)
  {
  //echo $uuid ." is ". $answer. "<br>";
  return 0;
  }
function db_get_answer($uuid)
  {
  return 0;
  }
function prepare_canvas()
{
//$canvas=imagecreatetruecolor(200,100);
global $captcha_tpl_path;
$canvas=imagecreatefrompng($captcha_tpl_path."/lorng_template.png");
//FixMe: Noise and watermark here
return $canvas;
}
function get_filename()
{
return md5(microtime()+rand(0,255));
}
};
?>