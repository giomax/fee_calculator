import './jquery-3.6.1.min';

$(document).on('change','[name="file"]',function(){
    let $this = $(this);
    $this.prop('disabled',true);
    let formData = new FormData();           
    formData.append("file", $this[0].files[0]);
    $.ajax({
        headers:{'X-CSRF-TOKEN':$('[name="csrf-token"]').attr('content')},
        url: base_url+'uploadFile',
        data: formData,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'json',
        success: function(data){
            alert(data.message);
            reset_file($this);
            if(data.code==1){
                $('ul').append("<li>RESULTS</li>");
                for(let i =0;i<data.data.length;i++){
                    $('ul').append("<li>"+data.data[i]+"</li>");
                }
            }
        },
        error: function(err){    
            alert(err.responseJSON.message);
            console.log(err);
            reset_file($this);
        }
    });

    function reset_file(file_input){        
        file_input.prop('disabled',false);
        file_input.val('');        
        $('ul li').remove();
    }

});