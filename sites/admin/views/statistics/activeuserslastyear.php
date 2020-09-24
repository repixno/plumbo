<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsActiveUsersPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      protected $query = "SELECT
                                (SELECT  count(distinct(uid))
                                    FROM
                                        historie_ordre
                                    WHERE
                                        xxxBYPORTALxxx
                                        tidspunkt BETWEEN date(xxxFROMxxx) - INTERVAL '1 year'  AND xxxTOxxx
                                ) AS count,
                                date(tidspunkt) AS date
                            FROM
                                historie_ordre ho
                            WHERE
                                xxxBYPORTALxxx
                                ho.tidspunkt BETWEEN xxxFROMxxx AND xxxTOxxx
                            GROUP BY
                                date(tidspunkt)";
                                
                                
                                
      protected $modulename = 'activeuserslastyear';
      protected $moduletitle = 'Active customers last year';
      
   }
   
?>