function update(){
     var line1 = encodeURIComponent($('input[name$="line1"]').val());
     var line2 = encodeURIComponent($('input[name$="line2"]').val());
     

     var projectid = 'test';
    
     $('#addtocart').removeAttr('disabled'); 
     var previewlink = '/api/1.0/merkelapp/navnepakke?projectid=' + projectid +
                        '&line1=' + line1 +
                        '&line2=' + line2 +
                       
                        '&mal=' + mal + blank;

    console.log(previewlink);
    
    
    
     $('img#preview').each(function () {
         $(this).attr('src', previewlink);
         //update();
     });
     
     
    
}