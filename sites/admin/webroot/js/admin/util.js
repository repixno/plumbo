function validateLocationField( field, length, nextfield ) {
   if( field.value.length == length ) {
      if( field.oldvalue != field.value ) {
         $(nextfield).focus();
         $(nextfield).select();
         field.oldvalue = field.value;
      }
   }
}

function validateLocationFieldValue( field, length ) {
   
   switch( length ) {
      
      case 1: if( field.value.length < 1 ) {
         $(field).focus();
         $(field).select();
      } break;
      
      case 2: if( field.value.length < 2 ) {
         if( field.value.length < 1 ) {
            $(field).focus();
            $(field).select();
         } else {
            field.value = '0'+field.value;
            field.oldvalue = field.value;
         }
      } break;
      
   }
   
}

function setTextAreaHeightAuto(obj){
   var padding=20;
   var shadow_div_id='shadow_'+obj.id;
   var shadow_div;
   if(!(shadow_div=$(shadow_div_id))){
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