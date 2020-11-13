jQuery(document).ready(function ($) {
  $('#analyse-media').click(function (e) {
    $('#extract-spinner').addClass('is-active');
    $('#extract-messages').html('Analysing media ...');
    $('#analyse-media').attr('disabled', true);

    analyseMedia(true);

    return false;
  });

  $('#resume-analysing-media').click(function (e) {
    $('#extract-spinner').addClass('is-active');
    $('#extract-messages').html('Resuming analysing media ...');
    $('#resume-analysing-media').attr('disabled', true);

    analyseMedia(false);

    return false;
  });

  function analyseMedia(fresh) {
    var data = {
      action: 'analyse_media',
    };

    if (fresh) {
      data.fresh = true;
    }

    adminAjax(
      data,
      function (response) {
        var status = response.status;
        if (status) {
          if (!status.completed) {
            console.log('More media to analyse');
            $('#extract-messages').html(
              `Analysed ${status.count} of ${status.total} attachments`
            );
            analyseMedia();
          } else {
            $('#extract-messages').html(
              `All ${status.total} attachments analysed`
            );
            $('#extract-spinner').removeClass('is-active');
            $("#extract-container input[type='submit']").attr(
              'disabled',
              false
            );
            console.log('All media analysed');
          }
        }
      },
      function () {
        console.log('error');
      }
    );
  }

  function adminAjax(data, success, error) {
    var url = window.mediaTextExtractorManager.ajaxUrl;
    $.ajax({
      url: url,
      type: 'post',
      data: data,
      success: success,
      error: error,
      dataType: 'json',
    });
  }
});
