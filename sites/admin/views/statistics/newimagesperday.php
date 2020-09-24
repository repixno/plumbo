<?PHP
   
   Dispatcher::extendView('statistics.templateobjectsperday');
   
   class StatisticsNewImagesPerDay extends StatisticsTemplateObjectsPerDay implements IView {
      
      protected $query = 'SELECT objects as count, period as date FROM bildeinfo_stats WHERE period BETWEEN ? AND ?';
      protected $modulename = 'newimagesperday';
      protected $moduletitle = 'Number of new images per day';
      
   }
   
?>