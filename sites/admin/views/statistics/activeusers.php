<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsActiveUsersPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      protected $query = "SELECT COUNT(distinct(uid)) as count, date(tidspunkt) as date FROM historie_ordre ho
                            WHERE
                                xxxBYPORTALxxx
                                ho.tidspunkt BETWEEN ? AND ?
                            AND
                                ho.ordrenr not in (SELECT ordrenr from sportsim)
                            AND
                                ho.uid not in ( SELECT uid FROM brukar WHERE brukarnamn ilike '%@eurofoto.no' )
                            AND
                                ho.uid not in ( 969748, 941275 )
                            AND
                                uid IN ( SELECT uid FROM historie_ordre WHERE tidspunkt BETWEEN ( date(?) - interval '1 year' ) AND ? )
                            GROUP BY
                                date(tidspunkt)";
      protected $modulename = 'activeusers';
      protected $moduletitle = 'Active customers per day';
      
   }
   
?>