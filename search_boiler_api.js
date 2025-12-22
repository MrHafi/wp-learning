jQuery(function ($) {

  $('#boiler-search').on('keyup', function () {

    let search = $(this).val();

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
          $('#search-results').append('<div>' + item.name + '</div>'); // append meanss adding a new html thign for each separate item
        });
      }
    });

  });

});
