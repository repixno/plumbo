<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsNewMegabytesPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      protected $query = 'SELECT size / 1024 / 1024 as count, period as date FROM bildeinfo_stats WHERE period BETWEEN ? AND ?';
      protected $modulename = 'newmegabytesperday';
      protected $moduletitle = 'Number of new MB of data per day';
      
   }
   
?>