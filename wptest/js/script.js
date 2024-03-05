jQuery(document).ready(function ($) {
jQuery(document).on('click', '.pagination a', function (event) {
    event.preventDefault();
    var page;
    if (jQuery(this).hasClass("prev")) {
        page = jQuery(".pagination .current").html();
        page--;
    } else if (jQuery(this).hasClass("next")) {
        page = jQuery(".pagination .current").html();
        page++;
    } else {
        page = jQuery(this).html();
    }
    var direct_type = jQuery(document).find(".directory-filter #directory_tax li.active").attr('data-item');
    get_directories(page, direct_type);
});



jQuery(document).on("click", ".directory-filter #directory_tax li", function (e) {
    e.preventDefault();
    var direct_type=jQuery(this).attr('data-item');
    jQuery(".directory-filter #directory_tax li").removeClass('active');
    jQuery(this).addClass('active');
    console.log(direct_type);
    get_directories(1, direct_type);
});
});




function get_directories(page = 1, direct_type = '') {
jQuery.ajax({
    url: WPTESTPUBLIC.ajaxurl,
    type: 'post',
    dataType: 'json',
    data: {
        action: 'directory_filter',
        page: page,
        direct_type: direct_type,
    },
    success: function (data) {
        var html_data = data.html_data
        jQuery("#load-directories-content").html(html_data);
    }

});
}
