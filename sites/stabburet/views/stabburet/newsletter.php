<?PHP

   /**
    * Add to newsletter list Stabburet!
    *
    */

   class CheckoutConfirm extends WebPage implements IView {
      
      
      protected  $template = 'create.stabburetnewsletter';
      
      public function Execute( $orderid ) {
         
         
         try{
         
            $stabburetCheck = DB::query( 'SELECT ordrenr FROM historie_ordrelinje WHERE ordrenr =? AND artikkelnr =?' , $orderid, 7196 )->fetchSingle();
                    
            
            if( $stabburetCheck ){
            
               $userid = DB::query( 'SELECT uid FROM historie_ordre WHERE ordrenr =?' , $orderid )->fetchSingle();
               
               $user = new User( $userid );
               $user->newsletter = true;
               $user->save();
               
               $this->contactemail = $user->contactemail;
            }else{
               relocate( '/stabburet/');
            }
            
         }catch ( Exception  $e ){
            
            util::Debug( $e->getMessage() );
         }
         
         
      }
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
   }
















?>