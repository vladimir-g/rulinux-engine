<?
include('../incs/db.inc.php');
require_once('../classes/auth.class.php');
require_once('../classes/config.class.php');
require_once('../classes/users.class.php');
header('Content-Type: text/html; charset='.$db_charset);
?>
<? if ($_SESSION['user_admin']>=1): ?>
<?
   switch($_GET['r']){
      case 'data':
         switch($_GET['data']){
            case 'author':
               echo '<a href="mailto:'.base::get_field_by_id('faq', 'email', $_GET['qid']).'">'.base::get_field_by_id('faq', 'name', $_GET['qid']).'</a>';
            break;
            case 'question':
               echo base::get_field_by_id('faq', 'question', $_GET['qid']);
            break;
            case 'name':
               echo base::get_field_by_id('faq', 'name', $_GET['qid']);
            break;
            case 'email':
               echo base::get_field_by_id('faq', 'email', $_GET['qid']);
            break;
            case 'date':
               echo base::get_field_by_id('faq', 'date', $_GET['qid']);
            break;
            case 'answer':
               echo base::get_field_by_id('faq', 'answer', $_GET['qid']);
            break;
            case 'av':
               echo base::get_field_by_id('faq', 'available', $_GET['qid']);
            break;
            default:
               echo '!Unknown query!';
            break;
         }
      break;
      case 'del':
         echo base::del_element('faq', $_GET['qid']);
      break;
   }
?>
<? endif; ?>