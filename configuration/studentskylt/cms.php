<?PHP
   
   // set the current version
   Settings::set( 'cms', 'templates', array(
      
      // available templates for menu items
      'menuitem' => array(
         'cms.articles.standard'           => 'Standard Article Listing',
         'cms.articles.standard_wide'      => 'Wide Article Listing (with menu)',
         'cms.articles.menu-span-20-blank' => 'Blank span-20 with menu',
         'cms.articles.quarantine'         => 'Special Article Quarantine',
         'cms.products.list'               => 'Standard Product Listing',
         'cms.products.1big+8medium+list'  => '1 Big, 8 Medium Listing',
         'cms.products.8medium+list'       => '8 Medium Listing',
         'cms.mainpages.products'          => 'Products Main Page',

         
      ),
      
      // available templates for content entities
      'textentity' => array(
         'cms.content'                     => 'Default Content Rendering',
         'cms.articles.standard'           => 'Article Rendering',
         'cms.products.show_one_product'   => 'Product Page Rendering',
         'cms.articles.photo_books'        => 'Photo Books'
      ),
      
      // setup some defaults for new objects
      'textentity_defaults' => array(
         'article' => 'cms.articles.standard',
         'product' => 'cms.products.show_one_product',
      ),
      
   ) );
   
?>