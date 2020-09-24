<?PHP
   
   // set the current version
   Settings::set( 'cms', 'templates', array(
      
      // available templates for menu items
      'menuitem' => array(
         'frontpage.index'                 => 'Frontpage',
         'cms.articles.standard'           => 'Standard Article Listing',
         'cms.articles.list'               => 'Article list',
         'cms.articles.standard_wide'      => 'Standard Article Wide',
         'cms.mainpages.products'          => 'Products Main Page',

         // product categories
               'cms.products.category'           => 'Product Category',
               'cms.products.categories_2column' => 'Product Categories 2 column',
               'cms.products.accessories'        => 'Category: Accessories',
               'cms.products.tusj'        => 'Category: Tusj',
               'cms.products.ukeplan'            => 'Category: Ukeplan',
               'cms.products.piktogrammer'       => 'Category: Piktogrammer',
               'cms.products.dagplan'            => 'Category: Dagplan',
               'cms.products.ukeplanmagnet'      => 'Category: Magnet',
               'cms.products.magnetisk'      => 'Category: Magnetisk',
               'cms.products.ugeplanmedfigurer'      => 'Category: Figurer',
               'cms.products.madplan'            => 'Category: Madplan DK',
               'cms.products.menyplan'            => 'Category: Menyplan',
               'cms.products.ukeplangavekort'   => 'Category: GavekortUkeplan',
               'cms.products.monthplan'          => 'Category: Månedplan',
               'cms.products.monthplandk'          => 'Category: MånedplanDK',
               'cms.products.dorskilt'          => 'Category: Dørskilt',
               'cms.products.skilter'          => 'Category: Skilter',
               'cms.products.giftcard'          => 'Category: Gavekort',
               
               'cms.products.subcategory'        => 'Product Sub-category',
               'cms.products.list'               => 'Product list',
               'cms.products.list-show-first'    => 'Product list, show first product',
               'cms.products.list-no-from-price' => 'Product list without from-price',
      
      ),
      // available templates for content entities
      'textentity' => array(
         'cms.content'                     => 'Default Content Rendering',
         'cms.articles.standard'           => 'Article Rendering',
         'cms.products.show_one_product'   => 'Product Page Rendering',
      ),
      
      // setup some defaults for new objects
      'textentity_defaults' => array(
         'article' => 'cms.articles.standard',
         'product' => 'cms.products.show_one_product',
      )
  ));
   
?>