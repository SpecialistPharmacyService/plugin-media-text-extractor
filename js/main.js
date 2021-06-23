jQuery(document).ready(function ($) {
  $("#analyse-media").click(function (e) {
    $("#extract-spinner").addClass("is-active");
    $("#extract-messages").html("Analysing media ...");
    $("#analyse-media").attr("disabled", true);

    analyseMedia(true);

    return false;
  });

  $("#resume-analysing-media").click(function (e) {
    $("#extract-spinner").addClass("is-active");
    $("#extract-messages").html("Resuming analysing media ...");
    $("#resume-analysing-media").attr("disabled", true);

    analyseMedia(false);

    return false;
  });

  $("#analyse-individual-media").click(function (e) {
    $("#extract-individual-spinner").addClass("is-active");
    $("#extract-individual-messages").html("Analysing individual media ...");
    $("#analyse-individual-media").attr("disabled", true);

    analyseIndividualMedia();

    return false;
  });

  function analyseMedia(fresh) {
    var data = {
      action: "analyse_media",
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
            logIfAvailable("More media to analyse");

            $("#extract-messages").html(
              `Analysed ${status.count} of ${status.total} attachments`
            );
            analyseMedia();
          } else {
            $("#extract-messages").html(
              `All ${status.total} attachments analysed`
            );
            $("#extract-spinner").removeClass("is-active");
            $("#extract-container input[type='submit']").attr(
              "disabled",
              false
            );
            logIfAvailable("All media analysed");
          }
        }
      },
      function () {
        logIfAvailable("error");
      }
    );
  }

  function analyseIndividualMedia() {
    var data = {
      action: "analyse_individual_media",
    };

    var id = $("input[name='media_text_extractor_id']").val();

    if (id) {
      data.id = id;
    }

    adminAjax(
      data,
      function (response) {
        var status = response.status;
        if (status) {
          $("#extract-individual-messages").html(`Attachment analysed`);
          $("#extract-individual-spinner").removeClass("is-active");
          $("#indexing-individual-container input[type='submit']").attr(
            "disabled",
            false
          );
          logIfAvailable("Individual media analysed");
        }
      },
      function () {
        logIfAvailable("error");
      }
    );
  }

  function adminAjax(data, success, error) {
    var url = window.mediaTextExtractorManager.ajaxUrl;
    $.ajax({
      url: url,
      type: "post",
      data: data,
      success: success,
      error: error,
      dataType: "json",
    });
  }

  function logIfAvailable(message) {
    if (console.log) {
      console.log(message);
    }
  }
});
