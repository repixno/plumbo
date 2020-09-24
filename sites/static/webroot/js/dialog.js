function messageDialog(title, msg, closebutton) {
   
   var buttons = {};
   buttons[ closebutton ] = function() { $('#message').dialog('close'); };

   $('#message').
      attr('title',title).
      find('p').text(msg).
      end().
      dialog({
      autoOpen: false,
      modal: true,
      buttons: buttons
   });
   
   $('#message').dialog('open');
   
}