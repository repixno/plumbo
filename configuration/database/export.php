<?PHP

   if( !Settings::Has( 'database', 'export' ) )
   Settings::Set( 'database', 'export', array(
      'enabled' => true,
      'tables' => array(
         'i18n_language',
         'i18n_languagestring',
         'i18n_languagetranslation',
         'site_textentity',
         'site_textentity_content',
         'site_article',
         'site_product',
         'site_product_option',
         'site_menu',
         'site_menu_contents',
         'site_menu_portals',
         'site_delivery_options',
         'site_payment_options',
      ),
   ) );
   
?>