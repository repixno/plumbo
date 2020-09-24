<?PHP
   
   // set the current version
   Settings::set( 'cms', 'templates', array(
      
      // available templates for menu items
      'menuitem' => array(
         'cms.articles.standard'           => 'Standard Article Listing',
         'cms.articles.standard_wide'      => 'Wide Article Listing (with menu)',
          'cms.products.normerk'             => 'Normerk Product list',
         'cms.articles.menu-span-20-blank' => 'Blank span-20 with menu',
         'cms.articles.quarantine'         => 'Special Article Quarantine',
          'cms.articles.temporary'         => 'Normerk Temporary Artikkel',
         'cms.products.list'               => 'Standard Product Listing',

         'cms.mainpages.products'          => 'Products Main Page',
         'cms.mainpages.frontpage'         => 'Site frontpage',

         // product categories
         'cms.products.category'           => 'Product Category',
         'cms.products.list'               => 'Product list',
         'cms.products.normerk'            => 'Normerk Product list',
          'cms.products.temporary'            => 'Normerk Temporary Template',
           'cms.products.barnehage_maler'  => 'Normerk Barnehage Maler',
         'cms.products.normerk_stempel'      => 'Normerk Product Stempel',
          'cms.products.standard_stempel'      => 'Normerk Standard Stempel',
          'cms.products.accessories'        => 'Category: Accessories',
         'cms.products.list-show-first'    => 'Product list, show first product',
         'cms.products.list-no-from-price' => 'Product list without from-price',
         'cms.products.span-24_with_product_list' => 'Wide article with product',
         'cms.mainpages.tips-and-tricks'   => 'Tips & Tricks - Mainpage',
         'cms.mainpages.faq'               => 'FAQpage',
         'cms.mainpages.contact'           => "Contact",

      

      ),
      
      // available templates for content entities
      'textentity' => array(
         'cms.content'                     => 'Default Content Rendering',
         'cms.articles.standard'           => 'Article Rendering',
         'cms.products.show_one_product'   => 'Product Page Rendering',
         'cms.products.textproduct'        => 'Text product',
    
      ),
      
      // setup some defaults for new objects
      'textentity_defaults' => array(
         'article' => 'cms.articles.standard',
         'product' => 'cms.products.show_one_product',
      ),
      
   ) );
   
?>
