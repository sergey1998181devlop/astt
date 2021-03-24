var socket;
var chatNotification = true;
var chatNotificationRoomId = '';
var pageTitle = '';

$(document).ready(function () {
    pageTitle = document.title;
    let socketUsers = {};
    socket = io.connect(__nodeJsHost, {query: 'username=' + username});
    socket.emit('add user', username);
    socket.on(username + ' new message', (data) => {
        if (chatNotification) {
            if (data.roomId !== chatNotificationRoomId) {
                mediaMessage();
                $.notify({
                    title: decodeURIComponent(data.userName),
                    message: decodeURIComponent(data.message),
                    url: __siteDir + 'task' + data.taskId + '-' + data.iBlockId + '/?roomId=' + data.roomId,
                }, {
                    position: 'fixed',
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                    animate: {
                        enter: 'animated fadeInUp',
                        exit: 'animated fadeOutDown'
                    },
                    type: 'success',
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-11 col-sm-3  notifications-mes" >' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<div data-notify="title" class="title bold">{1}</div> ' +
                        '<div data-notify="message" class="message">{2}</div>' +
                        '<div class="progress" data-notify="progressbar">' +
                        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                        '</div>' +
                        '<a href="{3}" target="{4}" data-notify="url" class="lnk-abs"></a>' +
                        '</div>'
                });
            }
            getUnreadedMessages();
        }
    });

    $('.status-indicator').each(function () {
        let classList = $(this).attr('class').split(/\s+/);
        for (let i in classList) {
            let className = classList[i];
            switch (className) {
                case 'status-indicator':
                    break;
                default:
                    if (className.length === 35) {
                        let uid = className.substr(3, className.length);
                        socketUsers[uid] = uid;
                    }
            }
        }
    });

    for (let i in socketUsers) {
        socket.on(socketUsers[i] + ' user status', (data) => {
            switch (data.status) {
                case 'online':
                    $('.uid' + data.id).addClass('active');
                    break;
                default:
                    $('.uid' + data.id).removeClass('active');
            }
        });
    }

    for (let i in socketUsers) {
        socket.emit('status', socketUsers[i]);
    }

    getUnreadedMessages();
});

function getUnreadedMessages() {
    $.ajax({
        type: 'POST',
        url: __socketIoTemplatePath + "/ajax.php",
        dataType: "json",
        data: 'unreaded=1',
        success: function (data) {
            if (data.result > 0) {
                if ($('header nav .itm-message .count').length > 0) {
                    $('header nav .itm-message .count').text(data.result);
                } else {
                    $('header nav .itm-message svg').after('<span class="count">' + data.result + '</span>');
                }
                document.title = '(' + data.result + ') ' + pageTitle;
            } else {
                $('header nav .itm-message .count').remove();
                document.title = pageTitle;
            }
        },
        error: function (data) {
        }
    });
}

function ChatLnk(count) {
    var regex = __siteDir + 'user/chat/',
        url = document.location.href;
    if (!url.match(regex)) {
        if ($('.chat-lnk').length > 0) {
            $('.chat-lnk').remove();
        }
        $('body').append('<a class="chat-lnk btn btn-round btn-blue" target="_blank" href="' + __siteDir + 'user/chat/"><div class="icon-wrap"><div class="count">' + count + '</div><svg class="icon icon_chat"> <use xlink:href="' + __siteTemplatePath + '/images/sprite-svg.svg#chat"></use></svg> </div>Чат</a>');
    }
}

function mediaMessage() {
    var audio = new Audio();
    audio.src = __socketIoTemplatePath + '/mediaNewMessage.mp3';
    audio.autoplay = true;
}

function mediaMessageShipped() {
    var audio = new Audio();
    audio.src = __socketIoTemplatePath + '/shipped.mp3';
    audio.autoplay = true;
}