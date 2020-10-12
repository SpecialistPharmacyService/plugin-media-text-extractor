jQuery(document).ready(function ($) {
  $('#analyse-media').click(function (e) {
    $('#extract-spinner').addClass('is-active');
    $('#extract-messages').html('Analysing media ...');
    $('#analyse-media').attr('disabled', true);

    var data = {
      action: 'analyse_media',
    };
    adminAjax(
      data,
      function () {
        $('#extract-spinner').removeClass('is-active');
        $('#extract-messages').html('Analysis run successfully');
        $('#analyse-media').attr('disabled', false);
        console.log('success');
      },
      function () {
        $('#extract-spinner').removeClass('is-active');
        $('#extract-messages').html('Text extraction failed');
        $('#analyse-media').attr('disabled', false);
        console.log('error');
      }
    );
    return false;
  });

  $('#resume-analysing-media').click(function (e) {
    $('#extract-spinner').addClass('is-active');
    $('#extract-messages').html('Resuming analysing media ...');
    $('#resume-analysing-media').attr('disabled', true);

    var data = {
      action: 'analyse_media',
    };
    adminAjax(
      data,
      function () {
        $('#extract-spinner').removeClass('is-active');
        $('#extract-messages').html('Analysis run successfully');
        $('#resume-analysing-media').attr('disabled', false);
        console.log('success');
      },
      function () {
        $('#extract-spinner').removeClass('is-active');
        $('#extract-messages').html('Text extraction failed');
        $('#resume-analysing-media').attr('disabled', false);
        console.log('error');
      }
    );
    return false;
  });

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
