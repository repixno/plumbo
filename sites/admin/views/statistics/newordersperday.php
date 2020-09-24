<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsNewOrdersPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      protected $query = 'SELECT COUNT(ordrenr) as count, date(tidspunkt) as date FROM historie_ordre
                              WHERE
                                 xxxBYPORTALxxx
                                 tidspunkt BETWEEN ? AND ?
                              AND
                                 ordrenr not in (SELECT ordrenr from sportsim)
                              AND
                                 uid not in ( SELECT uid FROM brukar WHERE brukarnamn ilike \'%@eurofoto.no\' )
                              AND
                                 uid not in ( SELECT uid FROM brukar WHERE brukarnamn ilike \'siri.s.engebretsen@gmail.com\' )
                              AND
                                 uid not in ( 969748, 941275, 983136, 1370892  )
                              AND kampanje_kode not ilike \'Netlife\'
                              AND kampanje_kode not in ( \'FE-001\')
                              GROUP BY
                                    date(tidspunkt)';
      protected $modulename = 'newordersperday';
      protected $moduletitle = 'Number of new orders per day';
      
   }
   
?>