$.ef = function( apiname, params, callback ) {
   
   var result = [];
   
   $.ajax( {
      dataType: 'json',
      data: params,
      type: 'POST',
      async: callback ? true : false,
      url: '/api/1.0/'+(apiname.replace(/\./g,'/')),
      success: callback ? callback : function( response ) {
         if( response.result ) {
            result = response;
         } else {
         	alert( 'Error: ' + response.message );
         }
         
      }
   });
   
   return callback ? true : result;
   
}