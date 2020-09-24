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

         // product categories
         'cms.products.category'           => 'Product Category',
         'cms.products.categories_2column' => 'Product Categories 2 column',
         'cms.products.calendars'          => 'Category: Calendars',
         'cms.products.photo-books'        => 'Category: Photo books',
         'cms.products.photo-gifts'        => 'Category: Photo gifts',
         'cms.products.cards'              => 'Category: Cards',
         'cms.products.canvas'             => 'Category: Canvas',
         'cms.products.enlargements'       => 'Category: Enlargements',
         'cms.products.scrapbooking'       => 'Category: Scrapbooking',
         'cms.products.accessories'        => 'Category: Accessories',
         'cms.products.scrapbook'          => 'Category: Scrapbook',
         'cms.products.photofolder'        => 'Fotohefte',
         'cms.products.unicef'             => 'UNICEF Productside',
         
         'cms.products.subcategory'        => 'Product Sub-category',
         'cms.products.matbokser'          => 'Matbokser',
         'cms.products.flasker'            => 'Flasker',
         'cms.products.brodering'          => 'Brodering',
         'cms.products.refleksproducts'    => 'Refleksprodukter',
           'cms.products.navnegaver'       => 'Navnegaver',
         'cms.products.list'               => 'Product list',
         'cms.products.list-show-first'    => 'Product list, show first product',
         'cms.products.list-no-from-price' => 'Product list without from-price',
         'cms.products.photo_books'        => 'Photo Books',
         'cms.products.cewe_photo_books'   => 'Cewe Photo Books',
         'cms.products.cewe_photo_books_download'   => 'Cewe Photo Books Download',
         'cms.products.spesial-print'      => 'Spesial print',
         'cms.products.span-24_with_product_list' => 'Wide article with product',
         'cms.mainpages.tips-and-tricks'   => 'Tips & Tricks - Mainpage',
         'cms.mainpages.faq'               => 'FAQpage',
         'cms.mainpages.contact'           => "Contact",
         'cms.products.blank_span-20'      => 'Blank span-20',
         'cms.products.blank_span-24'      => 'Blank span-24',
         'cms.products.christmas'          => 'Christmas',
         'cms.products.christmas-card'     => 'Christmas Card',
         'cms.products.valentine'          => 'Valentine',
         'cms.products.mothersday'         => 'Mothers day',
         'cms.products.fathersday'         => 'Fathers day',
         'cms.products.ukens-tilbud'       => 'Ukens tilbud',
         'cms.products.articlecategory'    => 'Article category',
         'cms.products.article-body-only'  => 'Article Body Only',
         'cms.products.camera-redirect'    => 'Camera redirect',

         
         'cms.functions.brydeg'           => 'Bry deg',
         'cms.functions.contest'           => 'Contest',
         'cms.functions.calendar'          => 'Calendar',
         'cms.functions.dyreparken_barcode'=> 'Dyreparken barcode',
         'cms.functions.one-image-mutiple-products' => 'Order one image on multiple products',
         
      ),
      
      // available templates for content entities
      'textentity' => array(
                  'cms.content'                     => 'Default Content Rendering',
                  'cms.articles.standard'           => 'Article Rendering',
                  'cms.products.show_one_product'   => 'Product Page Rendering',
                  'cms.articles.photo_books'        => 'Photo Books',
                  'cms.products.textproduct'        => 'Text product',
                  'cms.products.flaskeproduct'        => 'Flaske product',
                  'cms.products.refleksvest'        => 'Refleksvest',
                  'cms.products.lue'        => 'Lue',
      ),
      
      
      // setup some defaults for new objects
      'textentity_defaults' => array(
         'article' => 'cms.articles.standard',
         'product' => 'cms.products.show_one_product',
      ),
      
   ) );
   
?>