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
         
         
         if( Dispatcher::getPortal() == 'UP-DK'){
            relocate("/cart");
         }
         
         config( 'website.cart' );
         $cardsArray = array( 939, 940, 7039, 7105, 7233, 7239, 7237, 7238 );
         
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
         
         if( in_array( $grouptype,  Settings::get( 'cart', 'photobook_group' ))){
            relocate( '/cart' );
            die();
         }         
         else if(in_array($grouptype,  Settings::get( 'cart', 'calendar_group' ))){
            $rerRefId = Settings::get( 'cart', 'redeyeremoval_calendar' );
            $rerRefId = $rerRefId["refid"];
            $redeye = ProductOption::fromRefId( $rerRefId );

         }
         else{
            $rerRefId = Settings::get( 'cart', 'redeyeremoval' );
            $rerRefId = $rerRefId["refid"];
            $redeye = ProductOption::fromRefId( $rerRefId );
         }
                  
         Session::pipe( 'cart_redirecturl', '/cart');
         
         $cartdata = $cart['items']['mediaclip'][ sprintf("%04d" ,$orderdata->article_id)][$mcorderid];
         
         $this->mediaclipproduct = $cartdata;
         
         $this->total = $cartdata['extrapages']['price'] + $cartdata['price'];
         
         $this->redeyeremoval =  $redeye->asArray();
      }

   }
   
?>