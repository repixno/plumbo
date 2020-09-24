<?PHP
   import( 'website.projectorder' );
   
   class MediaClipProductoptions extends WebPage implements IValidatedView {
      
      protected $template = 'cart.mediaclip.productoptions';
      
      public function Validate() {
         
         return array( 
            'execute' => array(
               'fields' => array(
                  'mcorderid' => VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
      
      public function Execute( $mcorderid = 0 ) {
         
         config( 'website.cart' );
<<<<<<< .mine
         $cardsArray = array( 939, 940, 7039, 7105, 7233, 7239, 7237, 7238,7525 );
         
=======
         $cardsArray = array(  940, 7105, 7233, 7237 );
           $etikettArray = array( 7509,7510,7511,7512,7515,7528 );
          $canvasArray = array( 7035,7031,7036,7032,7033,7037,7034,7503,7493,7492,7502,980,981,982,983,984,985,986,7501,7489,7490,7500,7488,7491,7499,7495,7496,7497);
        $opphengArray = array( 6050,6051,6052,6053,6054,6055,6056,6057,6058,6059,6062,6060,6063,6064,6065,6066,6068,6069,6067,6070, 6071);
>>>>>>> .r27377
         // det her er bullshit. hva om cart ikke er lastet enda?
         # $cart = UserSessionArray::getItem( 'cart', 0 );
         $cart = new cart();
         $cart = $cart->enum();
         
         $orderdata = new ProjectOrder( $mcorderid );
         
         $grouptype = DB::query( "SELECT grouptype FROM article where artnr = ?", $orderdata->article_id )->fetchSingle();
         
         if( in_array(  $orderdata->article_id, $cardsArray ) ){
            $this->template = 'cart.mediaclip.productoptions-cards';
            $this->quantities = array(1,10,20,30,40,50,60,70,80,90,100,110,120,130,140,150,160,170,180,190,200,210,220,230,240,250,260,270,280,290,300,310,320,330,340,350,360,370,380,390,400);
         }
         
         
         if( in_array(  $orderdata->article_id, $etikettArray ) ){
            $this->template = 'cart.mediaclip.productoptions-etikett';
            $this->quantities = array(1,2,3,4,5,6,7,8,9,10);
         }
         
         
        if( in_array(  $orderdata->article_id, $opphengArray ) ){
             relocate( '/cart/fotono/accessories' );
            $this->quantities = array(1,2,3);
         
         }
         
         
         if( in_array( $grouptype,  Settings::get( 'cart', 'photobook_group' ))){
          relocate( '/cart' );
           die();;
         }
         
         
         
         
         
         else if(in_array($grouptype,  Settings::get( 'cart', 'calendar_group' ))){
             relocate( '/cart' );
           die();;

         }
         
         else if(in_array($grouptype,  Settings::get( 'cart', 'canvas_group' ))){
            $rerRefId = Settings::get( 'cart', 'upgrade' );
            $rerRefId = $rerRefId["refid"];
            $redeye = ProductOption::fromRefId( $rerRefId );
            

         }
         
         
         else{
            $rerRefId = Settings::get( 'cart', 'redeyeremoval' );
            $rerRefId = $rerRefId["refid"];
            $redeye = ProductOption::fromRefId( $rerRefId );
         }
                  
                  
                  
         Session::pipe( 'cart_redirecturl', '/cart/mediaclip/accessories');
         
         $cartdata = $cart['items']['mediaclip'][ sprintf("%04d" ,$orderdata->article_id)][$mcorderid];
         
         $this->mediaclipproduct = $cartdata;
         
         $this->total = $cartdata['extrapages']['price'] + $cartdata['price'];
         
         $this->redeyeremoval =  $redeye->asArray();
      }

   }
   
?>