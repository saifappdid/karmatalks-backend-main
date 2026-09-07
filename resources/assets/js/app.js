const { default: axios } = require('axios');

require('./bootstrap');


const messages_el = document.getElementById("messages");
const message_input = document.getElementById("message");
const message_file = document.getElementById("attach-doc");
const message_form = document.getElementById("message_form");

message_form.addEventListener('submit', function (e) {
    e.preventDefault();
    $("#clone-input .btn").attr('disabled', true);
    let has_error = false;

    if (message_input.value == '') {
        alert('Please Enter message');
        has_error = true;
    }

    if (has_error) {
        return;
    }

    var form = $('#message_form')[0];
    var formData = new FormData(form);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "post",
        url: "/admin/send-message",
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
            ($("#msg-modal").data('bs.modal')?._isShown) ? ($("#msg-modal").modal('hide')) : '';
            console.info(response);

        },
        error: function (response) {
            console.log(response.responseText);
            $("#clone-input .btn").attr('disabled', false);
            snb('error', 'something went Wrong', 'Error');
        },
    });

    // axios.post('/admin/send-message', {
    //     message: message_input.value,
    //     file: message_file.value,
    // })
    //     .then(function (response) {
    //         console.log(response);
    //     })
    //     .catch(function (error) {
    //         console.log(error);
    //     });


    // const options = {
    //     method: 'post',
    //     url: '/admin/send-message',
    //     data: {
    //         message: message_input.value,
    //         file: message_file.value,
    //     }
    // }


    // console.log(axios(options));


});

window.Echo.channel('chat')
    .listen('.message', (e) => {
        console.log(e);
        var reply = "";
        if (e.replied_to_message !== "") { reply = '<div class="reply"> <div class="to">' + e.replied_to_user + '</div><div class="to-message">' + e.replied_to_message + '</div></div>' }

        var img = '';
        var cls = '';
        if (e.file !== '') { img = '<img class="chat-image" src="/' + e.file + '" >'; cls = 'has-image'; }
        var chatDiv = document.getElementById('user-chats');
        $('#user-chats').animate({
            scrollTop: chatDiv.scrollHeight - (chatDiv.clientHeight - 75)
        }, 50);
        if (e.id == '0') {
            $("#clone-input .btn").attr('disabled', false);
            messages_el.innerHTML += '<div class="chat ">\
            <div class="chat-avatar">\
                <span class="avatar box-shadow-1 bg-gold cursor-pointer">\
                  <span class="avatar-content  ">'+ (e.user).substring(0, 2) + '</span>\
                    </span>\
            </div>\
            <div class="chat-body ">\
                <div div class="chat-content '+ cls + ' " >\
                <div class="sender-class">'+ e.user + '</div>' + img + '\
                <span class="d-none msg-id">'+ e.message_id + '</span>\
                '+ reply + '\
                    <p class="msg">'+ e.message + '</p>\
                    <span class="og-time" >'+ e.time + '</span>\
                    <span class="chat-time">'+ moment(e.time).fromNow() + '</span>\
                </div>\
            </div>\
        </div>';
            messages.className = 'chats move right';
            $(".user-profile-sidebar.chats").removeClass('show');
            initData();
        } else {
            messages_el.innerHTML += '<div class="chat chat-left">\
            <div class="chat-avatar">\
                <span class="avatar box-shadow-1 cursor-pointer">\
                        <span class="avatar-content">'+ (e.user).substring(0, 2) + '</span>\
                </span>\
            </div>\
            <div class="chat-body">\
            <div div class="chat-content '+ cls + ' " >\
            <span class="d-none msg-id">'+ e.message_id + '</span>\
            <div class="sender-class">'+ e.user + '</div>' + img + '\
            '+ reply + '\
                    <p class="msg">'+ e.message + '</p>\
                    <span class="og-time" >'+ e.time + '</span>\
             <span class="chat-time">'+ moment(e.time).fromNow() + '</span>\
                </div>\
            </div>\
        </div>';
            messages.className = 'chats move left';
        }


        message_input.value = '';
        message_file.value = '';
    }).listen('.status-update', (e) => { console.log(e); });


window.Echo.channel('is_active')
    .listen('.active', (e) => {
        console.log(e);
    });

messages.addEventListener('animationend', function () {
    messages.className = 'chats';
});

var offset;
var is_last = 'false';
(() => {
    var chatDiv = document.getElementById('user-chats');
    $('#user-chats').animate({
        scrollTop: chatDiv.scrollHeight - (chatDiv.clientHeight - 50)
    }, 1000);
    offset = 1;
    got_data = 'true';

    $("#user-chats").on('scroll', function () {
        if ($('#user-chats').scrollTop() === 0) {
            if (got_data == 'true' && is_last == 'false') {
                getMsg(++offset);
            }

        }

    });


    function getMsg(offset) {
        got_data = 'false';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "get",
            url: "chats/offset/" + offset,
            data: '',
            success: function (response) {
                if ((response.data).length > 0) {
                    const data = (response.data).reverse();

                    // console.log(data);
                    got_data = 'true'
                    data.map(function (msg, index) {
                        var img = '';
                        var cls = '';
                        console.log((msg.attachment == null));
                        if (msg.attachment != null) { img = '<img class="chat-image" src="/' + msg.attachment + '" >'; cls = 'has-image'; }
                        var reply = "";
                        if (msg.replied_to != null) { reply = '<div class="reply"> <div class="to"></div>' + msg.reply.app_user.fullname + '<div class="to-message">' + msg.message + '</div></div>' }
                        // console.log(msg);
                        if (msg.from == '0') {
                            // $('#messages').prepend('');

                            $('<div data-aos="fade-right" class="chat ">\
                       <div class="chat-avatar">\
                           <span class="avatar box-shadow-1 bg-gold cursor-pointer">\
                             <span class="avatar-content  ">'+ (msg.sender).slice(0, 2) + '</span>\
                               </span>\
                       </div>\
                       <div class="chat-body ">\
                           <div div class="chat-content '+ cls + ' " >\
                           <span class="d-none msg-id">'+ msg.id + '</span>\
                           <div class="sender-class">'+ msg.sender + '</div>' + img + '\
                                '+ reply + '\
                               <p class="msg">'+ msg.message + '</p>\
                               <span class="og-time" >'+ msg.created_at + '</span>\
                               <span class="chat-time">'+ moment(msg.created_at).fromNow() + '</span>\
                           </div>\
                       </div>\
                   </div>').hide().prependTo("#messages").fadeIn("slow");
                        } else {
                            $('#messages').prepend('<div class="chat chat-left">\
                            <div class="chat-avatar">\
                                <span class="avatar box-shadow-1 cursor-pointer">\
                                        <span class="avatar-content">'+ (msg.sender).slice(0, 2) + '</span>\
                                </span>\
                            </div>\
                            <div data-aos="fade-right" class="chat-body">\
                            <div div class="chat-content '+ cls + ' " >\
                            <span class="d-none msg-id">'+ msg.id + '</span>\
                            <div class="sender-class">'+ msg.sender + '</div>' + img + '\
                            '+ reply + '\
                                    <p class="msg">'+ msg.message + '</p>\
                                <span class="og-time" >'+ msg.created_at + '</span>\
                                    <span class="chat-time">'+ moment(msg.created_at).fromNow() + '</span>\
                                </div>\
                            </div>\
                        </div>');

                        }
                    })
                    messages.className = 'chats move-slow left';
                    message_input.value = '';
                    $('#user-chats').animate({
                        scrollTop: 3
                    }, 100);
                } else {
                    is_last = 'true';
                    snb('success', 'last', 'this was last message');
                }


            },
            error: function (response) {
                console.log(response);
                got_data = 'true'

            }
        });
    }
    function currentTime() {
        $(".og-time").each(function () {
            var inputString = $(this).html();
            $(this).parent().find('.chat-time').html(moment($(this).html()).fromNow());
        });
    }
    currentTime();
    var myInterval = setInterval(currentTime, 5000);

    $(document).on('dblclick', '.chat-content', function () {

        $("#reply").val($(this).find('.msg-id').html());
        $("#reply-to-user").val($(this).find('.sender-class').html());
        console.log($(this).find('.sender-class').html());

        $("#frames").html('');
        $("#frames").addClass('mh-auto');

        var image = $(this).find('.chat-image').attr('src');
        var img_div = "";
        console.log((image != 'undefined'));
        if (image) {
            img_div = '<div class="reply-img-holder "><img class="reply-img" src="' + image + '" ></div>';
        }

        var replied_msg = img_div + '<div class="reply">\
        <div class="to-message">\
            '+ $(this).find('.msg').html() + '\
        </div>\
    </div>';
        $("#frames").append(replied_msg);
        $(".user-profile-sidebar.chats").addClass('show');
        // $("#message-clone").focus();
    });
})();




