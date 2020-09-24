function autoTranslate() {
   
   var translated = 0;
   
   $A(document.getElementsByTagName('textarea')).each( function( textarea ) {
      
      if( !$F( textarea.id ) ) {
         
         translated++;
         var text = $(textarea.id).parentNode.previousSibling.previousSibling.innerHTML;
         var targetlanguage = LANGUAGE.split('_')[0];
         if( targetlanguage == 'nb' ) targetlanguage = 'no';
         
         google.language.translate( text, 'en', targetlanguage, function(result) {
            
            if( result.translation ) {
               $(textarea.id).value = result.translation;
               $(textarea.id).style.backgroundColor = '#00cc00';
            }
            
         });
         
      }
      
   });
   
   alert( 'Automatically translated '+translated+' strings!' );
   
}

function urlize( field, value ) {
   
   $.ajax({
      url: "/streams/strings/urlize",
      type: 'post',
      cache: 'false',
      data: {
         string: value
      },
      dataType: 'json',
      success: function( data, status ) {
      
         if( data.result && data.string != '' ) {
      
            field.val( data.string );
            field.css( 'backgroundColor', '#00cc00' );
            
         }
         
      }
      
   });
   
}

function translateContent( field, currentlanguage, updateURLized ) {
   
   var enUSfound = false;
   
   $('#languagetabs ul li a').each( function() {
      var lang = $(this).attr('href').split('#block_' )[1];
      if( lang == currentlanguage ) return true;
      if( lang == 'en_US' ) {
         enUSfound = true;
      }
   });
   
   if( !enUSfound ) {
      
      alert( 'American English is not an active language, or it is the current language! It is required for automated translation, and it cannot be translated TO, only FROM!' );
      return false;
      
   }
   
   
   var text = $('#'+field+'_en_US').val();
   var targetlanguage = currentlanguage.split('_')[0];
   if( targetlanguage == 'nb' ) targetlanguage = 'no';
         
   google.language.translate( text, 'en', targetlanguage, function(result) {
      
      if( result.translation ) {
         
         $('#'+field+'_'+currentlanguage).val( result.translation );
         
         if( updateURLized ) {
            
            urlize( $(updateURLized), result.translation );
            
         }
         
         if( field == 'body' ) {
            if(editor_body) {
            	editor_body.setData( result.translation );
            } else {
            	alert('No editor found, contact webmaster, or try another browser.');
            }
         }
         
      }
      
   });
   
   
}

function setTextAreaHeightAuto(obj){
   var padding=20;
   var shadow_div_id='shadow_'+obj.id;
   var shadow_div;
   if(!(shadow_div=document.getElementById(shadow_div_id))){
      shadow_div=document.createElement('div');
      shadow_div.id=shadow_div_id;
      shadow_div.style.position="absolute";
      shadow_div.style.left="-10000px";
      shadow_div.style.top="-10000px";
      shadow_div.style.fontSize='13px'; //parseInt(obj.style.fontSize)+'px';
      shadow_div.style.fontFamily='monospace'; //obj.style.fontFamily;
      shadow_div.style.width=parseInt(obj.clientWidth-8)+'px';
      obj.setAttribute('startHeight',obj.clientHeight);
      obj.parentNode.appendChild(shadow_div);
   }
   
   var clientHeight=obj.clientHeight;
   shadow_div.innerHTML=obj.value.replace(/[\n]/g,'<br />&nbsp;');
   var shadowHeight=shadow_div.clientHeight;
   var to_height;
   var startHeight=obj.getAttribute('startHeight');
   if(shadowHeight<startHeight){
      to_height=startHeight;
   }else{
      to_height=shadowHeight+padding;
   }
   if(to_height && to_height!=clientHeight){
      obj.style.height=to_height+'px';
   }
}