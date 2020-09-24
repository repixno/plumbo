<?php

class AdminStatistikk extends WebPage implements IView {
    
    protected $template = 'order.statistikk';
      
    public function Execute(){
        
        
        $from = '2015-01-01';
        
        
        $totaltordre = DB::query('SELECT hol.artikkelnr, max(hol.tekst) as tekst, count(*) FROM historie_ordre ho
                                    left  join historie_ordrelinje hol on ho.ordrenr = hol.ordrenr
                                    WHERE ho.kampanje_kode = ? AND ho.uid != 941275
                                    GROUP BY hol.artikkelnr order by hol.artikkelnr
                                    ', 'RF-001')->fetchAll( DB::FETCH_ASSOC );
        
        
        
        $artnr = array( 1,  2, 5, 439 );
        
        
        
        
        
        $now = date( 'Y-m-d' );
        
        //$ordrers[] = $totaltordre;
        
        while( $to < $now ) {
        
            $to = date( 'Y-m-d', strtotime( $from . '+1 month') );
            
            
            foreach( $artnr as $o ){
            
                $orders[$from][$o] = DB::query('SELECT hol.artikkelnr, max(hol.tekst) as tekst, count(*) FROM historie_ordre ho
                                    left  join historie_ordrelinje hol on ho.ordrenr = hol.ordrenr
                                    WHERE ho.kampanje_kode = ? AND ho.uid != 941275
                                    AND ho.tidspunkt BETWEEN ? AND ?
                                    AND hol.artikkelnr = ?
                                    GROUP BY hol.artikkelnr
                                    ', 'RF-001', $from, $to, $o )->fetchAll( DB::FETCH_ASSOC );
            
            
            } 
            
            $from = $to;
            
        }

        $this->artnr = $artnr;
        $this->orders = $orders;

        
        
    }
    
    
}
      