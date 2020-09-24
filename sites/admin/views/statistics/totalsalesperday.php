<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsTotalSalesPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      /*
      protected $query = 'SELECT SUM(pris) as count, 
                                 date(tidspunkt) as date 
                            FROM historie_ordre 
                           WHERE tidspunkt BETWEEN ? AND ? 
                           GROUP BY date(tidspunkt)';
      */
      protected $query = "SELECT SUM(hol.pris*hol.antall) as count, 
                                 date(ho.tidspunkt) as date 
                            FROM historie_ordrelinje hol
                        LEFT JOIN historie_ordre ho ON ho.ordrenr=hol.ordrenr
                           WHERE xxxBYPORTALxxx ho.tidspunkt BETWEEN ? AND ? 
                             AND ho.ordrenr NOT IN (SELECT ordrenr from sportsim)
                             AND ho.uid not in ( SELECT uid FROM brukar WHERE brukarnamn ilike '%@eurofoto.no' )
                             AND ho.uid not in ( SELECT uid FROM brukar WHERE brukarnamn ilike 'siri.s.engebretsen@gmail.com' )
                             AND ho.uid not in ( 969748, 941275, 983136, 1370892 )
                             AND ho.kampanje_kode not ilike 'Netlife'
                             AND hol.artikkelnr NOT BETWEEN 6000 AND 6999
                        GROUP BY date(ho.tidspunkt)";
      protected $modulename = 'totalsalesperday';
      protected $moduletitle = 'Total sales per day (non-hardware)';
      
   }
   
?>