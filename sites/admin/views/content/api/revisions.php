<?php

import( 'website.menu' );
import( 'pages.json' );

class APIFetchRevisions extends JSONPage implements IView {


    public function Execute() {
        
        $textentityid = $_POST['textentityid'];
        $language = $_POST['language'];
        
        
        $languagearray = array(
            'en_US' => 2,
            'sv_SE' => 7,
            'nb_NO' => 1,
            'da_DK' => 8,
            'fi_FI' => 9
        );
        
        if( $textentityid && $language ){
            $revisions = DB::query( 'SELECT textrevisionid, updated, languageid
                                    FROM site_textentity_revisions
                                    WHERE textentityid = ? and languageid = ?order by updated desc;', $textentityid, $languagearray[$language] )->fetchAll( DB::FETCH_ASSOC );
        }
        
        
        // Set return values.
        $this->revisions = $revisions;
        $this->result = true;
        $this->message = 'OK';
      
   }

}

?>
