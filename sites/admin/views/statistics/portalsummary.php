<?PHP

    import( 'pages.admin' );

    class PortalSummary extends AdminPage implements IView {
    
        protected $template = 'statistics.portalsummary';
      
        public function Execute( $portal = '', $date = '2001-01-01' ) {
            
            $registred = DB::query( "SELECT count(*) FROM brukar b, kunde k WHERE k.uid = b.uid AND b.kode = ? AND registrert > ? ", $portal, $date )->fetchSingle();
            $registrednewsletter = DB::query( "SELECT count(*) FROM brukar b, kunde k WHERE k.uid = b.uid AND b.kode = ? AND k.newsletter = 't' AND registrert > ?", $portal, $date )->fetchSingle();
            $orderstat = DB::query( "SELECT count(*), sum(pris) FROM historie_ordre WHERE kampanje_kode = ? AND deleted is null AND tidspunkt > ?", $portal, $date )->fetchAll();
            list( $ordercount, $ordersum ) = $orderstat[0];
            
            
            $this->postalStatistics = array(
                'registred' => $registred,
                'portal' => $portal,
                'registrednewsletter' => $registrednewsletter,
                'ordercount' => $ordercount,
                'ordersum'  => $ordersum
            );
            
            Util::Debug( $this->postalStatistics );
            
        }
        
    }


?>