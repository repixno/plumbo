<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsNewUsersPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      protected $query = 'SELECT COUNT(uid) as count, date(registrert) as date FROM brukar WHERE xxxBYPORTALxxx registrert BETWEEN ? AND ? GROUP BY date(registrert)';
      protected $modulename = 'newusersperday';
      protected $moduletitle = 'Number of new users per day';
      
   }
   
?>