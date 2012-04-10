<?php if (!defined('APPLICATION')) exit();

class ErrorCatcher
{

   protected static $mailTo = '';

   /**
    * Starts error detection
    *
    * @return bool
    */
   public static function init($Configuration)
   {
      if (empty($Configuration['To'])) return FALSE;
      self::$mailTo = $Configuration['To'];

      require_once dirname(__FILE__) . '/UniversalErrorCatcher/autoload.php';
      $catcher = new UniversalErrorCatcher_Catcher();

      $catcher->registerCallback(array(__CLASS__, 'catcher'))
         ->start();
      return TRUE;
   }

   /**
    * Error hendler
    *
    * @param Exception $e
    *
    * @todo fix duplicates
    * @return void
    */
   public static function catcher(Exception $e)
   {
      $code = $e->getCode();
      if (method_exists($e, 'getSeverity')) {
         $code = $e->getSeverity();
      }
      if (in_array($code, array(E_STRICT, E_NOTICE))) return; // TODO remove it in 2.1 verison


      $subject = ' Error: ' . substr($e->getMessage(), 0, 45);
      $body = 'The error: ' . $e . "\n" .
         'In file `' . $e->getFile() . '` on line `' . $e->getLine() . '`' . "\n" .
         'Code: ' . $code . "\n" .
         'Trace: ' . $e->getTraceAsString() . "\n\n" .
         self::dump($_SERVER, 'SERVER') . "\n\n" .
         self::dump($_SESSION, 'SESSION');

      mail(self::$mailTo, $subject, $body);
   }

   /**
    * Dump variables. Recursion safe and humanized
    *
    * @param mixed $value
    *
    * @return string
    */
   protected static function dump(&$varInput, $var_name = '', $reference = '', $method = '=', $sub = false)
   {
      static $output;
      static $depth;
      if ($sub == false)
      {
         $output = '';
         $depth = 0;
         $reference = $var_name;
         $var = serialize($varInput);
         $var = unserialize($var);
      }
      else
      {
         ++$depth;
         $var = &$varInput;
      }
      // constants
      $nl = "\n";
      $block = 'a_big_recursion_protection_block';
      $c = $depth;
      $indent = '';
      while ($c-- > 0)
      {
         $indent .= '| ';
      }
      $namePrefix = $var_name?$var_name . ' ' . $method:'';
      // if this has been parsed before
      if (is_array($var) && isset($var[$block]))
      {
         $real = &$var[$block];
         $name = &$var['name'];
         $type = gettype($real);
         $output .= $indent . $namePrefix . '& ' . ($type == 'array'?'Array':get_class($real)) . ' ' . $name . $nl;
         // havent parsed this before
      }
      else
      {
         // insert recursion blocker
         $var = Array($block => $var, 'name' => $reference);
         $theVar = &$var[$block];
         // print it out
         $type = gettype($theVar);
         switch ($type)
         {
            case 'array' :
               $output .= $indent . $namePrefix . ' Array (' . $nl;
               $keys = array_keys($theVar);
               foreach ($keys as $name)
               {
                  $value = &$theVar[$name];
                  self::dump($value, $name, $reference . '["' . $name . '"]', '=', true);
               }
               $output .= $indent . ')' . $nl;
               break;
            case 'object' :
               $output .= $indent . $namePrefix . get_class($theVar) . ' {' . $nl;
               foreach ($theVar as $name => $value)
               {
                  self::dump($value, $name, $reference . '=>' . $name, '=>', true);
               }
               $output .= $indent . '}' . $nl;
               break;
            case 'string' :
               $output .= $indent . $namePrefix . ' "' . $theVar . '"' . $nl;
               break;
            default :
               $output .= $indent . $namePrefix . ' (' . $type . ') ' . $theVar . $nl;
               break;
         }
      }
      --$depth;
      return $output;
   }
}
