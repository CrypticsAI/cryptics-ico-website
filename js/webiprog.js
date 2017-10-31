$(document).ready(function() {
    // timer for modal window
    // var cust_mousemove = false,
    //     cust_time = false,
    //     timeEnter = new Date();
    // $(window).on("mouseout", function(e) {
    //     var timeExit = new Date(),
    //         difTime = timeExit - timeEnter;
    //         console.log(difTime);
    //     if((!cust_mousemove) && (e.pageY <= 5) && (difTime > 20000)) {
    //         //Launch MODAL BOX
    //         $.modal("<div class='custom-modal-window'><a href='https://docs.google.com/forms/d/e/1FAIpQLScvgc31SqBX0Y6xCHYBknT6r7nhFU1nE-shPwX753tCQa1Q9g/viewform'>Don't forget to join Whitelist!</a></div>", {close: true});
    //         cust_mousemove = true;
    //     }
    // });

    // setTimeout(function() {
    //     if (!cust_time && !cust_mousemove) {
    //         $.modal("<div class='custom-modal-window'><a href='https://docs.google.com/forms/d/e/1FAIpQLScvgc31SqBX0Y6xCHYBknT6r7nhFU1nE-shPwX753tCQa1Q9g/viewform'>Don't forget to join Whitelist!</a></div>", {close: true});
    //         cust_time = true;
    //         cust_mousemove = true;
    //     }
    // },40000);
});

/**
 * When the open event is called, this function will be used to 'open'
 * the overlay, container and data portions of the modal dialog.
 *
 * onOpen callbacks need to handle 'opening' the overlay, container
 * and data.
 */
function modalOpen (dialog) {
    dialog.overlay.fadeIn('fast', function () {
        dialog.container.fadeIn('fast', function () {
            dialog.data.hide().slideDown('fast');
        });
    });
}

   /**
 * When the close event is called, this function will be used to 'close'
 * the overlay, container and data portions of the modal dialog.
 *
 * The SimpleModal close function will still perform some actions that
 * don't need to be handled here.
 *
 * onClose callbacks need to handle 'closing' the overlay, container
 * and data.
 */
function simplemodal_close (dialog) {
    dialog.data.fadeOut('fast', function () {
        dialog.container.hide('fast', function () {
            dialog.overlay.slideUp('fast', function () {
                $.modal.close();
            });
        });
    });
}
