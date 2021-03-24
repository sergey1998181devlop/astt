var dropZone;
$(document).ready(function () {
    if ($('#dropzone').length) {
        $('#files-previews').sortable({
            stop: function (event, ui) {
                sort();
            }
        });
        $('#files-previews').disableSelection();

        Dropzone.autoDiscover = false;
        var dropZonePath = getCurPath,
            dropZoneMaxFiles = maxFiles,
            dropZoneMaxFilesSize = maxSizeAva;

        let categoryId = $('#dropzone').data('id');
        dropZone = new Dropzone(
            '#dropzone',
            {
                url: BX.message('portfolioTemplatePath') + '/ajax.php',
                paramName: '__file',
                maxFilesize: dropZoneMaxFilesSize,
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                maxFiles: dropZoneMaxFiles,
                init: function () {
                    this.on('sending', function (file, xhr, formData) {
                        formData.append('type', 'file');
                        formData.append('categoryId', categoryId);
                    });
                },
                success: function (file, response) {
                    let jsonResponse = JSON.parse(response);

                    $('.dropzone .error').remove();
                    $(document).find(file.previewElement).remove();
                    $(".dropzone-files__files").removeClass('dz-started');

                    if (!jsonResponse.error) {
                        $('<div class="preview-pict" data-id="' + jsonResponse.result['id'] + '"><div class="uploadedPreview"><a href="#" class="remove"><svg class="icon icon_bin"><use xlink:href="' + templatePath + '/images/sprite-svg.svg#bin"></use></svg></a><div class="object-fit-container"><img data-object-fit="cover" data-object-position="50% 50%" src="' + file.dataURL + '"></div><a href="#" class="preview-pict-ttl">' + dropzoneImageTitle + '<svg class="icon icon_pen"><use xlink:href="' + templatePath + '/images/sprite-svg.svg#pen"></use></svg></a></div></div>').appendTo('#files-previews');
                        $('#files-previews').sortable({
                            stop: function (event, ui) {
                                sort();
                            }
                        });
                        $('#files-previews').disableSelection();
                    }
                },
                error: function (file, response) {
                    $('.dropzone .error').remove();
                    $(document).find(file.previewElement).remove();
                    $(".dropzone-files__files").addClass('dz-limit');
                },
                removedfile: function (file) {
                    $("#" + file.upload.uuid).remove();
                    $(document).find(file.previewElement).remove();
                }
            }
        );
        $(document).on('click', '.files-previews a.remove', function (e) {
            e.preventDefault();
            removePict($(this).closest('.preview-pict').data('id'));
        });
        $(document).on('click', '.preview-pict-ttl', function (e) {
            e.preventDefault();
            var id = $(this).closest('.preview-pict').data('id');
            $.fancybox.open([
                {
                    src: '#popupTitleImg'
                }
            ], {
                padding: 0,
                openEffect: 'fade',
                closeEffect: 'fade',
                nextEffect: 'none',
                prevEffect: 'none',
                afterShow: function (instance, current) {
                    $('#imageDescriptionId').val(id);
                }
            });
        });
    }

    $('#imageDescriptionSave').click(function () {
        let categoryId = $('#dropzone').data('id');
        let imageDescription = $('#imageDescription').val();
        let fileId = $('#imageDescriptionId').val();
        if (imageDescription.length > 0) {
            $.ajax({
                type: 'POST',
                url: BX.message('portfolioTemplatePath') + '/ajax.php',
                dataType: "json",
                data: 'type=changeDescription&categoryId=' + categoryId + '&id=' + fileId + '&description=' + imageDescription,
                success: function (data) {
                    if (!data.error) {
                        window.location.reload();
                    }
                },
                error: function (data) {
                }
            });
        }
        $.fancybox.close();
    });

    $('.removeCategory').click(function () {
        if (confirm(BX.message('portfolioRemoveCategoryConfirm'))) {
            let id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: BX.message('portfolioTemplatePath') + '/ajax.php',
                dataType: "json",
                data: 'type=removeCategory&categoryId=' + id,
                success: function (data) {
                    if (!data.error) {
                        $('#category' + id).remove();
                        window.location.reload();
                    }
                },
                error: function (data) {
                }
            });
        }

        return false;
    });
});

function sort() {
    if ($('#dropzone').length) {
        let ids = [];
        let categoryId = $('#dropzone').data('id');

        $('.preview-pict').each(function () {
            ids.push($(this).data('id'));
        });

        $.ajax({
            type: 'POST',
            url: BX.message('portfolioTemplatePath') + '/ajax.php',
            dataType: "json",
            data: 'type=sort&ids=' + ids + '&categoryId=' + categoryId,
            success: function (data) {
            },
            error: function (data) {
            }
        });
    }
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#ava-image').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}


function removePict(id) {
    let categoryId = $('#dropzone').data('id');
    $('#' + id + ', [data-id="' + id + '"]').remove();
    $('#dropzone').removeClass('dz-max-files-reached');

    $.ajax({
        type: 'POST',
        url: BX.message('portfolioTemplatePath') + '/ajax.php',
        dataType: "json",
        data: 'type=removeFile&id=' + id + '&categoryId=' + categoryId,
        success: function (data) {
            if (!data.error) {
                sort();
            }
        },
        error: function (data) {
        }
    });
}
