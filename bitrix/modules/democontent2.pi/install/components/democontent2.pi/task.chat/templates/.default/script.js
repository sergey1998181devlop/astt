var dropZone,
    openChat = false,
    chatCounter = 0,
    typing = false;

var URL = {
    get: function () {
        return document.location.href;
    },
    parse: function () {
        var url = this.get();

        return url.split("#!");
    }
};

!function (e) {
    e.parseQuery = function (r) {
        var a = {query: window.location.search || ""}, n = {};
        return "string" == typeof r && (r = {query: r}), e.extend(a, e.parseQuery, r), a.query = a.query.replace(/^\?/, ""), a.query.length > 0 && e.each(a.query.split(a.separator), function (e, r) {
            var t = r.split("="), u = "" + a.decode(t.shift(), null), o = a.decode(t.length ? t.join("=") : null, u);
            (a.array_keys.test ? a.array_keys.test(u) : a.array_keys(u)) ? (n[u] = n[u] || [], n[u].push(o)) : n[u] = o
        }), n
    }, e.parseQuery.decode = e.parseQuery.default_decode = function (e) {
        return decodeURIComponent((e || "").replace(/\+/g, " "))
    }, e.parseQuery.array_keys = function () {
        return !1
    }, e.parseQuery.separator = "&"
}(window.jQuery || window.Zepto);

function formatDate(date) {
    date = new Date(date);
    var month = (date.getMonth() + 1 < 10) ? '0' + (date.getMonth() + 1) : date.getMonth() + 1,
        days = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate(),
        h = (date.getHours() < 10) ? '0' + date.getHours() : date.getHours(),
        m = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
    return date.getFullYear() + '-' + month + '-' + days + ' ' + h + ':' + m;
}

function showChatContent() {
    const regex = /#!([\da-z]{24})/gm;
    const str = URL.get();
    let m;
    var i = 0,
        id_ = '';

    while ((m = regex.exec(str)) !== null) {
        if (m.index === regex.lastIndex) {
            regex.lastIndex++;
        }

        m.forEach((match, groupIndex) => {
            i++;
            if (groupIndex === 1) {
                id_ = match;
            }
        });
    }

    return id_;
}

function chatHeight() {
    var top = $('header').offset().top + $('header').outerHeight(),
        height = $(window).height() - top;
    $('#chat-container').css({'height': height, 'top': top});
}

function changeTimes() {
    let localTimeZone = new Date().getTimezoneOffset();
    if (localTimeZone >= 0) {
        console.log(localTimeZone);
        console.log('+');
    } else {
        console.log(localTimeZone);
        console.log('-');
    }
}

$(document).ready(function () {
    chatNotificationRoomId = BX.message('taskChatRoomId');

    if (showChatContent()) {
        chatHeight();
        $(window).scrollTop(0);
        $('html').addClass('open-chat');
        $('#chat-container').show();
        openChat = true;

        socket.emit('read', BX.message('taskChatRoomId'));

        chatCounter = 0;
        $('.chatCounter').removeClass('active').html('');
        scrBtm();
    }

    $('body')
        .bind('dragover', function () {
            if ($('.dropzone').is(':visible')) {
                $('body').addClass('dropzone-active');
            }
        })
        .bind('dragleave', function (e) {
            if (!$(e.target).closest('.dropzone').length) {
                $('body').removeClass('dropzone-active');
            } else {

            }
        });
    $('.chat-container .btn-file, .chat-individual .btn-file').click(function (e) {
        e.preventDefault();
        $('.dropzone').click();
    });
    chatCounter = BX.message('taskUnread');

    $(function () {
        var TYPING_TIMER_LENGTH = 300;
        var $window = $(window);
        var $inputMessage = $('.inputMessage');
        var connected = true;
        var lastTypingTime;

        const updateTyping = () => {
            if (connected) {
                if (!typing) {
                    typing = true;
                    socket.emit(
                        'typing',
                        {
                            roomId: BX.message('taskChatRoomId'),
                            username: username,
                            realName: BX.message('realName')
                        }
                    );
                }
                lastTypingTime = (new Date()).getTime();

                setTimeout(() => {
                    var typingTimer = (new Date()).getTime();
                    var timeDiff = typingTimer - lastTypingTime;
                    if (timeDiff >= TYPING_TIMER_LENGTH && typing) {
                        socket.emit(
                            'stop typing',
                            {
                                roomId: BX.message('taskChatRoomId'),
                                realName: BX.message('realName')
                            }
                        );
                        typing = false;
                    }
                }, TYPING_TIMER_LENGTH);
            }
        };

        function sendMessage() {
            let inputMessage = $inputMessage.val().trim();
            if (inputMessage.length) {
                $.ajax({
                    type: 'POST',
                    url: BX.message('taskChatPath'),
                    dataType: "json",
                    data: 'type=message&text=' + inputMessage + '&taskId=' + BX.message('taskId') + '&iBlockId=' + BX.message('taskIBlockId'),
                    success: function (data) {
                        if (!data.error) {
                            socket.emit('new message', data.result);
                            mediaMessageShipped();
                            $(
                                '<div class="item clearfix readStatus' + BX.message('taskChatRoomId') + '">' +
                                '<div class="info">' +
                                '<span class="bold">' + BX.message('taskChatYou') + '</span>: ' +
                                '<span class="timestamp" data-time="' + data.result['time'] + '" title="' + data.result['date'] + '"></span>' +
                                '</div>' +
                                '<div class="message">' +
                                data.result['message'] +
                                '<span id="' + data.result['id'] + '" class="message-status">' +
                                '<svg class="icon icon_check"><use xlink:href="' + BX.message('siteTemplatePath') + '/images/sprite-svg.svg#check"></use></svg>' +
                                '<svg class="icon icon_check"><use xlink:href="' + BX.message('siteTemplatePath') + '/images/sprite-svg.svg#check"></use></svg>' +
                                '</span>' +
                                '</div>' +
                                '</div>'
                            ).appendTo('#messages-items');
                            scrBtm();
                            $(".timestamp").timeago();
                        }
                    },
                    error: function (data) {
                    }
                });

                $inputMessage.val('');

                updateTyping();
                socket.emit(
                    'stop typing',
                    {
                        roomId: BX.message('taskChatRoomId'),
                        realName: BX.message('realName')
                    }
                );
                typing = false;
            }
        }

        socket.on(BX.message('taskChatRoomId') + ' typing', (data) => {
            if (data.username !== username) {
                $('#typingStatus').addClass('active');
                $('#typingStatusName').text(data.realName);
            }
        });

        socket.on(BX.message('taskChatRoomId') + ' stop typing', (data) => {
            if (data.username !== username) {
                $('#typingStatus').removeClass('active');
                $('#typingStatusName').text('');
            }
        });

        socket.on(BX.message('taskChatRoomId') + ' read', () => {
            $('.readStatus' + BX.message('taskChatRoomId')).addClass('read');
        });

        socket.on(BX.message('taskChatRoomId') + ' new message', (data) => {
            if (data.userId !== BX.message('taskUid')) {
                if (openChat) {
                    chatCounter = 0;
                    $('.chatCounter').removeClass('active').html('');
                    socket.emit('read', data.roomId);
                } else {
                    chatCounter += 1;
                    $('.chatCounter').addClass('active').html(chatCounter);
                }

                switch (data.type) {
                    case 'file':
                        mediaMessage();
                        $(
                            '<div class="item clearfix you">' +
                            '<div class="info">' +
                            '<span class="bold">' + data.userName + '</span>: ' +
                            '<span class="timestamp" data-time="' + data.time + '" title="' + data.date + '"></span>' +
                            '</div>' +
                            '<div class="message">' +
                            BX.message('taskChatFile') + ' <a href="' + data.file['path'] + '" target="_blank">' + data.file['description'] + '</a>' +
                            '<span id="' + data.id + '" class="message-status">' +
                            '<svg class="icon icon_check">' +
                            '<use xlink:href="' + BX.message('siteTemplatePath') + '/images/sprite-svg.svg#check"></use>' +
                            '</svg>' +
                            '</span>' +
                            '</div>' +
                            '</div>'
                        ).appendTo('#messages-items');
                        break;
                    default:
                        mediaMessage();
                        $(
                            '<div class="item clearfix you">' +
                            '<div class="info">' +
                            '<span class="bold">' + data.userName + '</span>: ' +
                            '<span class="timestamp" data-time="' + data.time + '" title="' + data.date + '"></span>' +
                            '</div>' +
                            '<div class="message">' +
                            data.message +
                            '</div>' +
                            '</div>'
                        ).appendTo('#messages-items');
                }
                scrBtm();
                $(".timestamp").timeago();
            }
        });

        socket.on('disconnect', () => {
            console.log('you have been disconnected');
        });

        socket.on('reconnect', () => {
            if (username) {
                socket.emit('add user', username);
            }
        });

        $window.keydown(event => {
            if (event.keyCode === 13 && $inputMessage.val() !== '' && !event.ctrlKey) {
                    if (username) {
                        sendMessage();
                        socket.emit(
                            'stop typing',
                            {
                                roomId: BX.message('taskChatRoomId'),
                                realName: BX.message('realName')
                            }
                        );
                        typing = false;
                    }
                    return false;

            } else if (event.keyCode === 13  && event.ctrlKey) {
                $('#inputMessage').val($('#inputMessage').val() + '\n');
            }
        });

        $inputMessage.on('keyup', () => {
            updateTyping();
        });

        $inputMessage.on('input', () => {
            updateTyping();
        });

        $inputMessage.click(() => {
            $inputMessage.focus();
        });

        Dropzone.autoDiscover = false;
        $(".dropzone-files__files").each(function () {
            dropZone = new Dropzone(
                this,
                {
                    url: BX.message('taskChatPath'),
                    paramName: '__file',
                    params: {
                        taskId: BX.message('taskId'),
                        iBlockId: BX.message('taskIBlockId'),
                        type: 'file'
                    },
                    maxFilesize: 10,
                    acceptedFiles: "image/*, application/pdf",
                    addRemoveLinks: true,
                    maxFiles: 10,
                    init: function () {
                    },
                    success: function (file, response) {
                        response = JSON.parse(response);
                        if (!response.error) {
                            socket.emit('new message', response.result);
                            mediaMessageShipped();
                            $(
                                '<div class="item clearfix readStatus' + BX.message('taskChatRoomId') + '">' +
                                '<div class="info">' +
                                '<span class="bold">' + BX.message('taskChatYou') + '</span>: ' +
                                '<span class="timestamp" data-time="' + response.result['date'] + '" title="' + response.result['date'] + '"></span>' +
                                '</div>' +
                                '<div class="message">' +
                                BX.message('taskChatFile') + ' <a href="' + response.result['file']['path'] + '" target="_blank">' + response.result['file']['description'] + '</a>' +
                                '<span id="' + response.result['id'] + '" class="message-status">' +
                                '<svg class="icon icon_check"><use xlink:href="' + BX.message('siteTemplatePath') + '/images/sprite-svg.svg#check"></use></svg>' +
                                '<svg class="icon icon_check"><use xlink:href="' + BX.message('siteTemplatePath') + '/images/sprite-svg.svg#check"></use></svg>' +
                                '</span>' +
                                '</div>' +
                                '</div>'
                            ).appendTo('#messages-items');
                            scrBtm();
                            $(".timestamp").timeago();
                        }

                        $(document).find(file.previewElement).remove();
                    },
                    error: function (file, response) {
                        $(document).find(file.previewElement).remove();
                    },
                    removedfile: function (file) {
                        $(document).find(file.previewElement).remove();
                    }
                }
            );
        });

        $('#chatButton').html('<a href="#" class="btn btn-green bnt-green btn-write">' + '<span class="icon-wrap"><svg class="icon icon_chat"><use xlink:href="' + BX.message('siteTemplatePath') + '/images/sprite-svg.svg#chat"></use></svg> <span class="chatCounter' + ((chatCounter > 0) ? ' active' : '') + '">' + ((chatCounter > 0) ? chatCounter : '') + '</span></span>' + BX.message('taskChatButton') + '</a>');

        chatHeight();

        $('.btn-write').click(function (e) {
            e.preventDefault();
            chatHeight();
            $(window).scrollTop(0);
            $('html').addClass('open-chat');
            $('#chat-container').show();
            openChat = true;

            let urlParts = URL.parse();
            window.history.pushState(null, null, urlParts[0] + '#!' + BX.message('taskChatRoomId'));

            socket.emit('read', BX.message('taskChatRoomId'));

            chatCounter = 0;
            $('.chatCounter').removeClass('active').html('');
            scrBtm();
        });
        $('#chat-container .close').click(function (e) {
            e.preventDefault();
            $('html').removeClass('open-chat');
            $('#chat-container').hide();
            openChat = false;

            let urlParts = URL.parse();
            window.history.pushState(null, null, urlParts[0]);

            chatCounter = 0;
            $('.chatCounter').removeClass('active').html('');
        });

        $('.sendMessage').click(function () {
            sendMessage();
        });
    });
});

$(window).resize(function () {
    chatHeight();
});


function scrBtm() {

    if ($('#chat-container').is(':visible')) {
        $('.chat-individual .messages-list').scrollTop($('#messages-items').outerHeight());
        $.ajax({
            type: 'POST',
            url: BX.message('taskChatPath'),
            dataType: "json",
            data: 'type=read&taskId=' + BX.message('taskId'),
            success: function (data) {
                if (!data.error) {
                    getUnreadedMessages();
                }
            },
            error: function (data) {
            }
        });
    }
}