<?PHP
   
   Dispatcher::extendView('partner.apressen.template');
   
   class StatisticsApressenNewOrdersPerDay extends StatisticsApressenTemplateObjectsPerDay implements IView {
      
      protected $query = "SELECT COUNT(ordrenr) as count, date(tidspunkt) as date FROM historie_ordre WHERE kampanje_kode = 'AP-997' AND tidspunkt BETWEEN ? AND ? AND ordrenr not in (SELECT ordrenr from sportsim) GROUP BY date(tidspunkt)";
      protected $modulename = 'ordersperday';
      protected $moduletitle = 'Number of new orders per day';
      
   }
   
?>