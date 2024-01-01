jQuery(document).ready(function() {
    jQuery('#poll_seletect').change(function() {
      // Perform AJAX request here
      var pollid = jQuery(this).val();
    const data = {
        pollid: pollid,
    };
    
    var siteUrl =wbpollpublic.url;
    jQuery.ajax({
        url: siteUrl + '/wp-json/wbpoll/v1/listpoll/result/poll',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.success) {
                jQuery('.all_polll_result').html(response.result);
            } else {
                alert('Failed to unpublish poll.');
            }
        }
    });
    });
  });