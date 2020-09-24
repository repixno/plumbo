<?PHP
   import( 'pages.json' );
   model( 'order.leverpostei');
   import( 'website.cart' );
   import( 'website.giftpagetemplate' );
   import( 'website.giftordertemplate' );
   import( 'website.giftordertext' );
   import( 'website.giftorderclipart' );

   
   class ChagesizeStabburetProject extends JSONPage implements NoAuthRequired, IView {

         
         public function Execute() {
                $message = 'OK';
                $id = $_POST['lokkid'];
                $lokksize = $_POST['lokksize'];                
                $leverpostei = new DBLeverpostei($id);
                $leverpostei->lokksize = $lokksize;
                
                $leverpostei->save();
                
                if( $lokksize == 'small' ){
                  $malid = 3160;
                }else{
                  $malid = 3160;
                }
                
                $printtype = "stabburet_" . $id;
                
                $update = DB::query( "UPDATE mal_order SET malid = ? WHERE printtype = ?", $malid, $printtype  )->fetchSingle();
                
                $this->result = true;
                $this->data =  $leverpostei->id;
                $this->message = $lokksize;
                return true;
         }      
   }



?>
