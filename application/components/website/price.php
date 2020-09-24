<?php
   
   /**
    * Class to wrap price formating etc.
    * Straight port from old EF < 2.8
    * old util.inc
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class Price {
      
      /**
       * Multiply a price with a quantity
       *
       * @param float $t1
       * @param integer $t2
       * @return float
       */
      static function multiply( $t1, $t2 ){
      	
         //assumes that t2 is a whole number!!!
      	$tmp1 = explode(".",$t1);
      	$prime1 = $tmp1[0];
      	$dec1 = $tmp1[1];
      	if(!$prime1){
      		$prime1 = "0";
      	}
      	if(!$dec1){
      		$dec1="0";
      	}
      	if($prime1<0){
      		$prime1 = $prime1 * -1;
      		$t2 = $t2 * -1;
      	}
      	$negative = 0;
      	if($t2<0){
      		$negative = 1;
      	}
      	$newprime = $prime1 * abs($t2);
      	$newdec = $dec1 * abs($t2);
      	//analyze the decimal
      	$oldn = strlen($dec1);
      	$newdec = sprintf("%d",$newdec);
      	$newn = strlen($newdec);
      	if($oldn != $newn){
      		$addprime = "";
      		for($i=0;$i<($newn-$oldn);$i++){
      			$addprime .= $newdec[$i];
      		}
      		$tmpdec="";
      		for($i=($newn-$oldn);$i<$newn;$i++){
      			$tmpdec .= $newdec[$i];
      		}
      		$newdec = $tmpdec;
      		$newprime += $addprime;
      	}
      	$ret = "$newprime.$newdec";
      	if($negative){
      		$ret = "-$newprime.$newdec";
      	}
      	return $ret;
      }
      
      
      /**
       * Add to prices together
       *
       * @param float $t1
       * @param float $t2
       * @return float
       */
      static function add( $t1, $t2 ){
      	
         $tmp1 = explode(".",$t1);
      	$prime1 = $tmp1[0];
      	$dec1 = $tmp1[1];
      	if(!$prime1){
      		$prime1 = "0";
      	}
      	if(!$dec1){
      		$dec1="0";
      	}
      	$tmp2 = explode(".",$t2);
      	$prime2 = $tmp2[0];
      	$dec2 = $tmp2[1];
      	if(!$prime2){
      		$prime2 = "0";
      	}
      	if(!$dec2){
      		$dec2="0";
      	}
      	$dec1n = strlen($dec1);
      	$dec2n = strlen($dec2);
      	//First we pad the dec strings to be of equal lenght
      	if($dec1n>$dec2n){
      		for($i=$dec2n;$i<$dec1n;$i++){
      			$dec2 .= "0";
      		}
      	}
      	else{
      		for($i=$dec1n;$i<$dec2n;$i++){
      			$dec1 .= "0";
      		}
      	}
      	if($prime1 < 0){
      		$dec1 = $dec1 * -1;
      	}
      	if($prime2 < 0){
      		$dec2 = $dec2 * -1;
      	}
      	$newprime = $prime1 + $prime2;
      	$newdec = $dec1 + $dec2;
      	$newdec = sprintf("%d",$newdec);
      	//analyze the decimal
      	$oldn = strlen($dec1);
      	$newn = strlen($newdec);
      	if($oldn != $newn){
      		$addprime = "";
      		for($i=0;$i<($newn-$oldn);$i++){
      			$addprime .= $newdec[$i];
      		}
      		$tmpdec="";
      		for($i=($newn-$oldn);$i<$newn;$i++){
      			$tmpdec .= $newdec[$i];
      		}
      		$newdec = $tmpdec;
      		$newprime += $addprime;
      	}
      	if($newdec < 0){
      		$newdec = $newdec * -1;
      	}
      	$ret = "$newprime.$newdec";
      	return $ret;
      }
      
      
      /**
       * Format a price with 2 decimals
       *
       * @param float $price
       * @return float
       */
      static function format( $price ){

         $ret = sprintf("%0.2f",$price);
         return $ret;
	
      }
      
   }


?>