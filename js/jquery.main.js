/* global window, document, console jQuery, ipAjaxVar, Masonry */
/* eslint quotes: ["error", "single"] */
/* eslint-env browser */
/* jslint-env browser */

/**
 * roar - v1.0.5 - 2018-05-25
 * https://getbutterfly.com/roarjs-vanilla-javascript-alert-confirm-replacement/
 * Copyright (c) 2018 Ciprian Popescu
 * Licensed GPLv3
 */
function roar(title, message, options) {
    'use strict';

    if (typeof options !== 'object') {
        options = {};
    }

    if (!window.roarAlert) {
        var RoarObject = {
            element: null,
            cancelElement: null,
            confirmElement: null
        };
        RoarObject.element = document.querySelector('.roar-alert');
    } else {
        // Clear style
        if (window.roarAlert.cancel) {
            window.roarAlert.cancelElement.style = '';
        }
        if (window.roarAlert.confirm) {
            window.roarAlert.confirmElement.style = '';
        }
        // Show alert
        document.body.classList.add('roar-open');
        window.roarAlert.element.style.display = 'block';

        RoarObject = window.roarAlert;
    }

    // Define default options
    RoarObject.cancel = options.cancel !== undefined ? options.cancel : false;
    RoarObject.cancelText = options.cancelText !== undefined ? options.cancelText : 'Cancel';
    RoarObject.cancelCallBack = function (event) {
        document.body.classList.remove('roar-open');
        window.roarAlert.element.style.display = 'none';
        // Cancel callback
        if (typeof options.cancelCallBack === 'function') {
            options.cancelCallBack(event);
        }

        // Cancelled
        return true;
    };

    // Close alert on click outside
    if (document.querySelector('.roar-alert-mask')) {
        document.querySelector('.roar-alert-mask').addEventListener('click', function (event) {
            document.body.classList.remove('roar-open');
            window.roarAlert.element.style.display = 'none';
            // Cancel callback
            if (typeof options.cancelCallBack === 'function') {
                options.cancelCallBack(event);
            }

            // Clicked outside
            return true;
        });
    }

    RoarObject.message = message;
    RoarObject.title = title;
    RoarObject.confirm = options.confirm !== undefined ? options.confirm : true;
    RoarObject.confirmText = options.confirmText !== undefined ? options.confirmText : 'Confirm';
    RoarObject.confirmCallBack = function (event) {
        document.body.classList.remove('roar-open');
        window.roarAlert.element.style.display = 'none';
        // Confirm callback
        if (typeof options.confirmCallBack === 'function') {
            options.confirmCallBack(event);
        }

        // Confirmed
        return true;
    };

    if (!RoarObject.element) {
        RoarObject.html =
            '<div class="roar-alert" id="roar-alert" role="alertdialog">' +
            '<div class="roar-alert-mask"></div>' +
            '<div class="roar-alert-message-body" role="alert" aria-relevant="all">' +
            '<div class="roar-alert-message-tbf roar-alert-message-title">' +
            RoarObject.title +
            '</div>' +
            '<div class="roar-alert-message-tbf roar-alert-message-content">' +
            RoarObject.message +
            '</div>' +
            '<div class="roar-alert-message-tbf roar-alert-message-button">';

        if (RoarObject.cancel || true) {
            RoarObject.html += '<a href="javascript:;" class="roar-alert-message-tbf roar-alert-message-button-cancel">' + RoarObject.cancelText + '</a>';
        }

        if (RoarObject.confirm || true) {
            RoarObject.html += '<a href="javascript:;" class="roar-alert-message-tbf roar-alert-message-button-confirm">' + RoarObject.confirmText + '</a>';
        }

        RoarObject.html += '</div></div></div>';

        var element = document.createElement('div');
        element.id = 'roar-alert-wrap';
        element.innerHTML = RoarObject.html;
        document.body.appendChild(element);

        RoarObject.element = document.querySelector('.roar-alert');
        RoarObject.cancelElement = document.querySelector('.roar-alert-message-button-cancel');

        // Enabled cancel button callback
        if (RoarObject.cancel) {
            document.querySelector('.roar-alert-message-button-cancel').style.display = 'block';
        } else {
            document.querySelector('.roar-alert-message-button-cancel').style.display = 'none';
        }

        // Enabled cancel button callback
        RoarObject.confirmElement = document.querySelector('.roar-alert-message-button-confirm');
        if (RoarObject.confirm) {
            document.querySelector('.roar-alert-message-button-confirm').style.display = 'block';
        } else {
            document.querySelector('.roar-alert-message-button-confirm').style.display = 'none';
        }

        RoarObject.cancelElement.onclick = RoarObject.cancelCallBack;
        RoarObject.confirmElement.onclick = RoarObject.confirmCallBack;

        window.roarAlert = RoarObject;
    }

    document.querySelector('.roar-alert-message-title').innerHTML = '';
    document.querySelector('.roar-alert-message-content').innerHTML = '';
    document.querySelector('.roar-alert-message-button-cancel').innerHTML = RoarObject.cancelText;
    document.querySelector('.roar-alert-message-button-confirm').innerHTML = RoarObject.confirmText;

    RoarObject.cancelElement = document.querySelector('.roar-alert-message-button-cancel');

    // Enabled cancel button callback
    if (RoarObject.cancel) {
        document.querySelector('.roar-alert-message-button-cancel').style.display = 'block';
    } else {
        document.querySelector('.roar-alert-message-button-cancel').style.display = 'none';
    }

    // Enabled cancel button callback
    RoarObject.confirmElement = document.querySelector('.roar-alert-message-button-confirm');
    if (RoarObject.confirm) {
        document.querySelector('.roar-alert-message-button-confirm').style.display = 'block';
    } else {
        document.querySelector('.roar-alert-message-button-confirm').style.display = 'none';
    }
    RoarObject.cancelElement.onclick = RoarObject.cancelCallBack;
    RoarObject.confirmElement.onclick = RoarObject.confirmCallBack;

    // Set title and message
    RoarObject.title = RoarObject.title || '';
    RoarObject.message = RoarObject.message || '';

    document.querySelector('.roar-alert-message-title').innerHTML = RoarObject.title;
    document.querySelector('.roar-alert-message-content').innerHTML = RoarObject.message;

    window.roarAlert = RoarObject;
}



/**
 * ImagePress Javascript functions
 */
document.addEventListener('DOMContentLoaded', function (event) {
    /*
     * Drag & Drop Uploader
     */
    if (document.getElementById('dropContainer')) {
        document.getElementById('dropContainer').ondragover = document.getElementById('dropContainer').ondragenter = function(evt) {
            evt.preventDefault();
        };

        document.getElementById('dropContainer').ondrop = function(evt) {
            document.getElementById('imagepress_image_file').files = evt.dataTransfer.files;
            // Display selected image
            // document.getElementById("dropContainer").innerHTML = document.getElementById('imagepress_image_file').files[0].name;

            evt.preventDefault();
        };
    }



    if (document.getElementById('imagepress_upload_image_form')) {
        document.getElementById('imagepress_upload_image_form').addEventListener('submit', function () {
            document.getElementById('imagepress_submit').disabled = true;
            document.getElementById('imagepress_submit').style.setProperty('opacity', '0.5');
            document.getElementById('ipload').innerHTML = '<i class="ai-more-horizontal-fill"></i> Uploading...';
        });
    }
    /* end upload */

    /* ip_editor() related actions */
    if (document.querySelector('.ip-editor')) {
        var container = document.querySelector('.ip-editor');

        document.getElementById('ip-editor-open').addEventListener('click', function (event) {
            event.preventDefault();

            if (!container.classList.contains('active')) {
                container.classList.add('active');
                container.style.height = 'auto';

                var height = container.clientHeight + 'px';

                container.style.height = '0px';

                setTimeout(function () {
                    container.style.height = height;
                }, 0);
            } else {
                container.style.height = '0px';

                container.addEventListener('transitionend', function () {
                    container.classList.remove('active');
                }, {
                    once: true
                });
            }
        });

        jQuery(document).on('click', '#ip-editor-delete-image', function (e) {
            var id = this.dataset.imageId,
                redirect = this.dataset.redirect,
                options = {
                    cancel: true,
                    cancelText: ipAjaxVar.swal_cancel_button,
                    cancelCallBack: function () {
                        // Do nothing
                    },
                    confirm: true,
                    confirmText: ipAjaxVar.swal_confirm_button,
                    confirmCallBack: function () {
                        var request = new XMLHttpRequest();

                        request.open('POST', ipAjaxVar.ajaxurl, true);
                        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                        request.onload = function () {
                            if (this.status >= 200 && this.status < 400) {
                                window.location = redirect;
                            } else {
                                // Response error
                            }
                        };
                        request.onerror = function() {
                            // Connection error
                        };
                        request.send('action=ip_delete_post&nonce=' + ipAjaxVar.nonce + '&id=' + id);
                    }
                };

            roar('', ipAjaxVar.swal_confirm_operation, options);

            e.preventDefault();
            return false;
        });
    }

    jQuery(document).on('click', '.editor-image-delete', function (e) {
        var id = jQuery(this).data('image-id'),
            options = {
                cancel: true,
                cancelText: ipAjaxVar.swal_cancel_button,
                cancelCallBack: function () {
                    // Do nothing
                },
                confirm: true,
                confirmText: ipAjaxVar.swal_confirm_button,
                confirmCallBack: function () {

                    var request = new XMLHttpRequest();

                    request.open('POST', ipAjaxVar.ajaxurl, true);
                    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                    request.onload = function () {
                        if (this.status >= 200 && this.status < 400) {
                            jQuery('#listItem_' + id).fadeOut(function(){
                                jQuery('#listItem_' + id).remove();
                            });
                        } else {
                            // Response error
                        }
                    };
                    request.onerror = function() {
                        // Connection error
                    };
                    request.send('action=ip_delete_post&nonce=' + ipAjaxVar.nonce + '&id=' + id);
                }
            };

        roar('', ipAjaxVar.swal_confirm_operation, options);

        e.preventDefault();
        return false;
    });

    jQuery('.editableImage').click(function () {
        jQuery(this).addClass('editableImageActive');
    });
    jQuery('.editableImage').blur(function () {
        jQuery(this).removeClass('editableImageActive');
    });

    jQuery('.editableImage').keypress(function (e) {
        if (e.keyCode === 10 || e.keyCode === 13) {
            e.preventDefault();

            var id = jQuery(this).data('image-id');
            var title = jQuery(this).val();

            jQuery('.editableImageStatus_' + id).show().html('<i class="ai-more-horizontal-fill"></i>');

            jQuery.ajax({
                type: 'post',
                url: ipAjaxVar.ajaxurl,
                data: {
                    action: 'ip_update_post_title',
                    title: title,
                    id: id,
                },
                success: function (result) {
                    if (result === 'success') {
                        jQuery('#listImage_' + id).removeClass('editableImageActive');
                        jQuery('.editableImageStatus_' + id).show().html('<i class="ai-check"></i>');
                    }
                }
            });
        }
    });

    if (jQuery('.ip-uploader').length) {
        var userUploads = jQuery('.ip-uploader').data('user-uploads'),
            uploadLimit = jQuery('.ip-uploader').data('upload-limit'),
            globalUploadLimitMessage = ipAjaxVar.ip_global_upload_limit_message;

        if (!isNaN(uploadLimit) && userUploads >= uploadLimit) {
            jQuery('<div>' + globalUploadLimitMessage + ' (' + userUploads + '/' + uploadLimit + ')</div>').insertBefore('.ip-uploader');
            jQuery('.ip-uploader').remove();
        }
    }
});


function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return typeof sParameterName[1] === 'undefined' ? true : sParameterName[1];
        }
    }
}

function addEqualsToEmptyParams(requestUri) {
    // Remove the '?' at the beginning if present
    const queryString = requestUri.startsWith('?') ? requestUri.substring(1) : requestUri;

    // Split the query string into key-value pairs
    const params = queryString.split('&').map(param => {
        // If the param does not contain an equal sign, add `=`
        return param.includes('=') ? param : param + '=';
    });

    // Rebuild the modified query string
    const modifiedQueryString = params.length > 0 ? '?' + params.join('&') : '';

    return modifiedQueryString;
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('ip-sorter-primary')) {
        var request_uri = addEqualsToEmptyParams(window.location.search),
            sorterDropdown = document.getElementById('sorter'),
            rangerDropdown = document.getElementById('ranger'),
            taxxxerDropdown = document.getElementById('taxxxer');

        const params = new URLSearchParams(window.location.search);
        const sortValue = params.get('sort');
        const rangeValue = params.get('range');
        const tValue = params.get('t');

        // Check URI parameters, select default values, and redirect based on user selection
        if (sortValue === '' || sortValue === null) {
            //
        } else {
            sorterDropdown.value = request_uri;
        }

        if (rangeValue === '' || rangeValue === null) {
            //
        } else {
            rangerDropdown.value = request_uri;
        }

        if (tValue === '' || tValue === null) {
            //
        } else {
            taxxxerDropdown.value = request_uri;
        }

        // Check if dropdown has changed on page load
        sorterDropdown.onchange = function () {
            document.getElementById('ip-sorter-loader').innerHTML = '<i class="ai-more-horizontal-fill"></i>';
            window.location.href = sorterDropdown.value;
        };
        rangerDropdown.onchange = function () {
            document.getElementById('ip-sorter-loader').innerHTML = '<i class="ai-more-horizontal-fill"></i>';
            window.location.href = rangerDropdown.value;
        };
        taxxxerDropdown.onchange = function () {
            document.getElementById('ip-sorter-loader').innerHTML = '<i class="ai-more-horizontal-fill"></i>';
            window.location.href = taxxxerDropdown.value;
        };
    }

    // ImagePress Grid UI
    var gridUi = ipAjaxVar.grid_ui,
        currentDiv;

    if (gridUi === 'masonry') {
        if (jQuery('#ip-boxes').length) {
            var container = document.querySelector('#ip-boxes');
            var msnry = new Masonry(container, {
                itemSelector: '.ip_box ',
                columnWidth: '.ip_box',
                gutter: 0,
            });
        }
    } else if (gridUi === 'default') {
        var equalHeight = function(container) {
            var currentTallest = 0,
                currentRowStart = 0,
                rowDivs = new Array(),
                $el,
                topPosition = 0;

            jQuery(container).each(function() {
                $el = jQuery(this);
                jQuery($el).height('auto');
                topPosition = $el.position().top;

                if (currentRowStart !== topPosition) {
                    for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
                        rowDivs[currentDiv].height(currentTallest);
                    }
                    rowDivs.length = 0; // empty the array
                    currentRowStart = topPosition;
                    currentTallest = $el.height();
                    rowDivs.push($el);
                } else {
                    rowDivs.push($el);
                    currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
                }

                for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
                    rowDivs[currentDiv].height(currentTallest);
                }
            });
        };

        // Create equal height image containers // onload
        if (jQuery('.list .ip_box').length) {
            equalHeight('.list .ip_box');
        }
        // Create equal height image containers // onload
        if (jQuery('.ip-box-container-default .ip_box').length) {
            equalHeight('.ip-box-container-default .ip_box');
        }
    }
}, !1);
