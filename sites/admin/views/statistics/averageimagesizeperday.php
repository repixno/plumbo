<?PHP
   
   Dispatcher::extendView('statistics.newusersperday');
   
   class StatisticsAverageImageSizePerDay extends StatisticsNewUsersPerDay implements IView {
      
      protected $query = 'SELECT size / objects / 1024 as count, period as date FROM bildeinfo_stats WHERE period BETWEEN ? AND ?';
      protected $modulename = 'averageimagesizeperday';
      protected $moduletitle = 'Average image size per day';
      
   }
   
?>