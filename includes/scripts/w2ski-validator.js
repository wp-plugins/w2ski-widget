jQuery("#w2sopt").validate({
   rules: {
     "w2ski_settings[w2ski_text_field_0]": {
         minlength: 10,
         maxlength: 10
     }
   }
 });


jQuery("input[name='w2ski_settings[w2ski_text_field_1]']").on('click', function(){
    if ( jQuery(this).is(':checked') ){
        jQuery(".w2s-opt").show();
        jQuery('.w2s-opt2').hide();
    }
    else {
        jQuery(".w2s-opt").hide();
        jQuery(".w2s-opt2").show();
    }
} );