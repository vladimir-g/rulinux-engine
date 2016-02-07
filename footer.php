<?php
require 'themes/'.$theme.'/templates/footer.tpl.php';
echo '<!--Страница сгенерировалась за '.round(timeMeasure()-TIMESTART, 6).' сек.-->';
// ob_end_flush();
?>