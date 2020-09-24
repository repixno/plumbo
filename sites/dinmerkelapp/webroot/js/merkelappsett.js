function update(){
     var line1 = encodeURIComponent($('input[name$="line1"]').val());
     var line2 = encodeURIComponent($('input[name$="line2"]').val());
     var line3 = encodeURIComponent($('input[name$="line3"]').val());
    
     $('#addtocart').removeAttr('disabled'); 
     var previewlink = '/api/1.0/merkelapp/merkelappsett?projectid=' + projectid +
                        '&line1=' + line1 +
                        '&line2=' + line2 +
                        '&line3=' + line3 +
                        '&mal=' + mal + blank;

    console.log(previewlink);
    
    
    
     $('img#preview').each(function () {
         $(this).attr('src', previewlink);
         //update();
     });
     
     
     
     $('#addtocart').click(function () {
         /*if(disabled == true ){
            return false;
         }*/
         
         console.log("test2");
         
         //$('.productline').each( function(){
            var productoptionid = parseFloat(  $("#productoptionid").val() ) ;
            var quantity = parseFloat(  $('#productquantity').val() );
            
            
            if(  quantity > 0 ){
                  $.post('/bestilling/addItemByProductOptionIdAPI/', {
                     productoptionid: productoptionid,
                     quantity: quantity,
                     projectid: projectid
                  }, function (response) {
                     //console.log(response);
                  }, 'json');
            }
            
            
            
         //});
         
         $('#myModal').modal();
            
         /*alertmessage = 'false';
        if( disabled == true ){
            return false;
        }else{   
            document.location.href = '/bestilling/addItemByProductOptionId/' + $('input[name$="choose_product"]:checked').val() + '/' + projectid + '/' + type;
        }*/
         return false;
     });
     
     
    
}