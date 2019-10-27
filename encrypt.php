<?
/*
*  url encryption
*   - lightweight url encryption
*  (c) 2004 
*  
*
*
*/

   $key_table = array();

   $_url = $_SERVER['PHP_SELF'];
   print $_url;

   $key = 'present me a key';
   $key_length = strlen($key);

   url_init($key,$key_length);
   $_eurl = url_encrypt("/test.php?page=12");
   print "E: $_eurl\n";
   $_durl = url_decrypt($_eurl);
   print "D: $_durl\n";

   function url_decrypt($string)
   {
      global $key_table;
      $out = "";

      for ($i = 0, $k = 0,$n = 0; $n < strlen($string); $n+= 2)
      {
          $hi = nib($string{$n});
          $lo = nib($string{$n+1});
          $b =  ($hi << 4) | $lo;
          $k = $k ^ $key_table[$i];
          $b = $b ^ $k;
          $i++;
          $out .= chr($b);
      }
      return $out;
   }

   function nib($c)
   {
      $c = ucfirst($c);

      if (($c) >= 'A' && ($c) <= 'F')
         return ((ord($c) - ord('A')) + 10);
      else if (($c) >= '0' && ($c) <= '9')
         return (ord($c) - ord('0'));
   }

   function url_encrypt($string)
   {
      global $key_table;

      $out = array();

      for ($k = 0, $n = 0; $n < strlen($string); $n++)
      {
          $k = $k ^ $key_table[$n];
          $b = ord($string{$n});
          $b = $b ^ $k;
          array_push($out,$b);
      }

      return toString($out);
   }

   function toString($array)
   {
      $string = "";
      for ($n = 0; $n < sizeof($array); $n++)
      {
          $string.= dechex($array[$n]);
      }
   return $string;
   }

   function url_init($key,$key_length)
   {
      global $key_table;

      $byte = 0;
      $rand = 0x75;

      for ($i = 0; $i < 256; $i++)
      {
          $byte = $byte ^ ord($key{$i % $key_length});
          $byte = ($rand ^ (($byte + $i) + $key_length)) % 256;
          $rand = ($byte ^ $rand);

          array_push($key_table,$byte);
      }
   }
?>
