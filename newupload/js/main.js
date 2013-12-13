/*
 * jQuery File Upload Plugin JS Example 8.8.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, regexp: true */
/*global $, window, blueimp */

$(function () {
    'use strict';

    function sortUsingNestedText(parent, childSelector, keySelector) {
        var items = parent.children(childSelector).sort(function(a, b) {
            var vA = parseInt($(keySelector, a).val());
            var vB = parseInt($(keySelector, b).val());
            return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
        });
        parent.append(items);
    }

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'index.php?type=uploadv3&key=QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP&resp=jqu',
        paramName: 'uploadfile'
    }).on('fileuploadsubmit', function(e, data) {
        $.each($('tbody.files tr.fade.in'), function(i, e) {
            $(e).find('.positionid.input').val(i);
        });

        var inputs = data.context.find(':input');
        data.formData = inputs.serializeArray();
    }).on('fileuploadcompleted', function(e, data) {
        sortUsingNestedText($('tbody.files'), '.fade.in', '.positionid');

        $('.all-links-list').text('');
        $.each($('tbody.files .template-download .dlurl'), function (index, file) {
            $('.all-links-list').append($(file).val() + '\n');
        });
    });

    // Load existing files:
 /*   $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        url: 'index.php',
        dataType: 'json',
        type: 'HEAD',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, null, {result: result});
    });*/

});
