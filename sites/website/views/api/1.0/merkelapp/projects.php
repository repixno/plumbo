<?PHP


   import( 'pages.json' );
   import( 'website.order.merkelapporder' );

   class ListProjects extends JSONPage implements NoAuthRequired, IView {

      public function Execute() {
         
         $userid = Login::userid();
         
         $projects = UserMerkelappOrder::userProjects( $userid );
         
         
         $this->result = true;
         $this->projects = $projects;
         $this->message = 'OK';
         
         return true;
         

         }


         
   }



?>
