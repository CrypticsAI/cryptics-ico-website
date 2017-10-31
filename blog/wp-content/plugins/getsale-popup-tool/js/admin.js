jQuery(document).ready(function () {
    jQuery('.form-table th').css('width', '70px');
    jQuery('th').css('padding-bottom', '12px').css('padding-top', '12px');
    jQuery('.form-table td').css('padding', '5px 10px');
    jQuery('input[name=submit_btn]').css('margin-top', '8px').css('margin-bottom', '8px').css('-webkit-appearance', 'button');
    jQuery('#getsale_project_id').parent().parent().hide();
    jQuery('#getsale_reg_error').parent().parent().hide();
    jQuery('input[name=submit_btn]').attr('value', gs.authorization);

    jQuery('#getsale_api_key, #getsale_email').each(function () {
        if (jQuery(this).val() == '') {
            jQuery('input[name=submit_btn]').attr('disabled', 'disabled');
        }
    });

    jQuery('#getsale_api_key, #getsale_email').keyup(function () {
        var empty = false;
        jQuery('#getsale_api_key, #getsale_email').each(function () {
            if (jQuery(this).val() == '') {
                empty = true;
            }
        });
        if (!empty) {
            jQuery('[name=submit_btn]').removeAttr('disabled');
        } else {
            jQuery('[name=submit_btn]').attr('disabled', 'disabled');
        }
    });
    var app_key_selector = '#getsale_api_key';
    var getsale_images_ok = gs.path;
    var email_selector = '#getsale_email';
    var text_after = '<p>' + gs.enter_value + '</p><p>' + gs.registration + '</p>';
    var sup_text = '<p>' + gs.support + '</p>' +
        '<p>WordPress ' + gs.getsale_name + ' ' + gs.getsale_ver + '</p>';
    var success_text = '<div class=\'updated\'><p>' + gs.congrats + '</p></div>' + gs.widgets_create;
    if ((!jQuery('#getsale_reg_error').val()) && (jQuery('#getsale_project_id').val())) {
        window.getsale_succes_reg = true;
    } else {
        window.getsale_reg_error = jQuery('#getsale_reg_error').val();
        window.getsale_succes_reg = false;
    }
    if ((jQuery(app_key_selector).val() !== '') && (jQuery(email_selector).val() !== '')) {
        if (window.getsale_succes_reg == true) {
            jQuery(app_key_selector).after('<img title="' + gs.api_key_success + '" class="gtsl_ok" src="' + getsale_images_ok + '">');
            jQuery(email_selector).after('<img title="' + gs.email_success + '" class="gtsl_ok" src="' + getsale_images_ok + '">');
            jQuery(app_key_selector).attr('disabled', 'disabled');
            jQuery(email_selector).attr('disabled', 'disabled');
            jQuery('[name=submit_btn]').before('<br>' + success_text + sup_text);
            jQuery('[name=submit_btn]').hide();
        } else if (window.getsale_succes_reg == false) {
            jQuery('input[name=submit_btn]').after(text_after + sup_text);
            if (window.getsale_reg_error == 403) {
                var error_text = '<div class="error"><p>' + gs.error403 + '</p></div>';
            } else if (window.getsale_reg_error == 500) {
                var error_text = '<div class="error"><p>' + gs.error500 + '</p></div>';

            } else if (window.getsale_reg_error == 404) {
                var error_text = '<div class="error"><p>' + gs.error404 + '</p></div>';
            } else if (window.getsale_reg_error == 0) {
                var error_text = '<div class="error"><p>' + gs.error0 + '</p></div>';
            }
            var gtsl_btn_html = '<div style="width:100%;margin-top: 5px;">' +
                '<div style="padding-top: 7px;">' + error_text +
                '</div>' +
                '</div>';
            jQuery('input[name=submit_btn]').after(gtsl_btn_html);
        }
        else {
            jQuery('input[name=submit_btn]').after(text_after + sup_text);
        }
    } else {
        jQuery('input[name=submit_btn]').after(text_after + sup_text);
    }
    jQuery(app_key_selector).parent().css('margin-left', '70px');
    jQuery(email_selector).parent().css('margin-left', '70px');
});
var text_after2 = '<p><b>' + gs.getsale_name + '</b> &mdash; ' + gs.desc + '</p>' +
    '<p>' + gs.description  + '</p>';
jQuery('.readmore').parent().hide();
jQuery('.info-labels').after(text_after2);
