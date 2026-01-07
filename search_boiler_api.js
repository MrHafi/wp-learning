jQuery(function ($) {

  $('#boiler-search').on('keyup', function () {

    let search = $(this).val();

        //LESSER THAN 3 CHARS 
        if (search.length < 3 && search.length >0) {
        $('#search-results').html('<div>Enter at least 3 characters</div>');
        return;
        }



    $.ajax({
      url: ajax_obj.ajaxurl,
      type: 'GET',
      data: {
        action: 'boiler_search',
        data: search
      },
      success: function (res) {
        $('#search-results').html('');
       res.forEach(item => {
          let div = $('<div>')
            .addClass('list-group-item list-group-item-action cursor-pointer')
            .text(item.name)
            .click(() => {


             // ADDING LINK TO A SEPARATE PAGE
              window.location.href = "/single-boiler/?id=" + item.id;
});

          $('#search-results').append(div);
        });

      }

      
    });

  });

});
