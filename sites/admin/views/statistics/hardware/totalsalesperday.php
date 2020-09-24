<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsTotalHardwareSalesPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      /*
      protected $query = 'SELECT SUM(pris) as count, 
                                 date(tidspunkt) as date 
                            FROM historie_ordre 
                           WHERE tidspunkt BETWEEN ? AND ? 
                           GROUP BY date(tidspunkt)';
      */
      protected $query = 'SELECT SUM(hol.pris*hol.antall) as count, 
                                 date(ho.tidspunkt) as date 
                            FROM historie_ordrelinje hol
                        LEFT JOIN historie_ordre ho ON ho.ordrenr=hol.ordrenr
                           WHERE ho.tidspunkt BETWEEN ? AND ? 
                             AND ho.ordrenr NOT IN (SELECT ordrenr from sportsim) 
                             AND hol.artikkelnr BETWEEN 6000 AND 6999
                        GROUP BY date(ho.tidspunkt)';
      protected $modulename = 'hardware/totalsalesperday';
      protected $moduletitle = 'Total sales per day (Hardware)';
      
   }
   
?>