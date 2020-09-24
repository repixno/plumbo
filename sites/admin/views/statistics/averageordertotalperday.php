<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsAverageOrderTotalPerDay extends StatisticsTemplateObjectsPerDay implements IView {

      protected $query = 'SELECT SUM(pris) / COUNT(ordrenr) as count, 
                                 date(tidspunkt) as date 
                            FROM historie_ordre 
                           WHERE xxxBYPORTALxxx tidspunkt BETWEEN ? AND ? 
                             AND ordrenr NOT IN (SELECT ordrenr FROM sportsim) 
                             AND ordrenr NOT IN (SELECT ordrenr FROM historie_ordrelinje WHERE artikkelnr BETWEEN 6000 AND 6999)
                        GROUP BY date(tidspunkt)';
      
      protected $modulename = 'averageordertotalperday';
      protected $moduletitle = 'Average order total per day';
      
   }
   
?>