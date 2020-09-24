<?php

   import( 'website.project' );
   model( 'user.projectunique' );

   Dispatcher::extendView( 'cart.mediaclip.add' );

   class MediaclipAddToCartFromList extends MediaclipAddToCart implements IView {

      public function Execute( $mediacliporderid = 0 ) {

         $projectId = $_POST[ 'id' ];

         // Find project info.
         $project = new Project( $projectId );

         if ( !( $project->isLoaded() && $project instanceof Project ) ) {

            return false;

         }

         $userId = Login::userid();
         $productId = $project->productid;
         $quantity = 1;

         $location = WebsiteHelper::rootBaseUrl();

         // Create unique id.
         $unique = new DBUserProjectUnique();
         $unique->userid = $userId;
         $unique->project = $project->id;
         $unique->host = $location;
         $unique->save();

         // Create id to be inserted into order XML.
         $orderXmlId = sprintf( '%s-%s-%s-%s', $unique->id, $productId, $quantity, $userId );

         // Add params for accessing general add to cart api.
         $params = array(
            'userid' => $userId,
            'production_id' => $unique->id,
            'realprojectid' => $project->id,
            'productid' => $productId,
            'refid' => 0,
            'quantity' => $quantity,
            'numextrapages' => $project->extrapages,
            'sheetcount' => $project->sheetcount,
            'ordername' => $project->title,
            'title' => $project->title,
            'projectxml' => $this->patchXml( $project->projectxml, $orderXmlId )
            );

         $this->localData = $params;

         parent::Execute();

      }

      private function patchXml( $xml, $id ) {

         $search1 = "/(<orderRequest.+?id=)\"([\d-]*)\"/";
         $replace = '$1"'.$id.'"';

         return preg_replace( $search1, $replace, $xml );

      }

   }

?>
