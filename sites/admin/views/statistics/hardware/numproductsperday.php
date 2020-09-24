<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsNumHardwareProductsPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      protected $query = 'SELECT SUM(hol.antall) as count, 
                                 date(ho.tidspunkt) as date 
                            FROM historie_ordrelinje hol
                        LEFT JOIN historie_ordre ho ON ho.ordrenr=hol.ordrenr
                           WHERE ho.tidspunkt BETWEEN ? AND ? 
                             AND ho.ordrenr NOT IN (SELECT ordrenr from sportsim) 
                             AND hol.artikkelnr BETWEEN 6000 AND 6999
                        GROUP BY date(ho.tidspunkt)';
      protected $modulename = 'hardware/numproductsperday';
      protected $moduletitle = 'Products sold per day (Hardware)';
      
   }
   
?>