$(document).ready(function () {
    firebase.initializeApp({
        messagingSenderId: BX.message('senderId'),
    });

    if (
        'Notification' in window &&
        'serviceWorker' in navigator &&
        'localStorage' in window &&
        'fetch' in window &&
        'postMessage' in window
    ) {
        var messaging = firebase.messaging();

        messaging.usePublicVapidKey(BX.message('vapidKey'));
        messaging.onTokenRefresh(() => {
            messaging.getToken().then((refreshedToken) => {
                setTokenSentToServer(false);
                sendTokenToServer(refreshedToken);
                resetUI();
            }).catch((err) => {
                console.log('Unable to retrieve refreshed token ', err);
            });
        });

        messaging.onMessage(function (payload) {
            //console.log('Message received', payload);

            navigator.serviceWorker.register('/firebase-messaging-sw.js');
            Notification.requestPermission(function (permission) {
                if (permission === 'granted') {
                    navigator.serviceWorker.ready.then(function (registration) {
                        payload.data.data = JSON.parse(JSON.stringify(payload.data));
                        registration.showNotification(payload.data.title, payload.data);
                    }).catch(function (error) {
                    });
                }
            });
        });

        requestPermission();
    }

    function resetUI() {
        messaging.getToken().then((currentToken) => {
            if (currentToken) {
                sendTokenToServer(currentToken);
            } else {
                setTokenSentToServer(false);
            }
        }).catch((err) => {
            setTokenSentToServer(false);
        });
    }

    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer()) {
            $.ajax({type: 'POST', url: BX.message('pushAjaxPath'), dataType: "json", data: 'token=' + currentToken});
            setTokenSentToServer(true);
        }
    }

    function isTokenSentToServer() {
        return window.localStorage.getItem('sentToServer') === '1';
    }

    function setTokenSentToServer(sent) {
        window.localStorage.setItem('sentToServer', sent ? '1' : '0');
    }

    function requestPermission() {
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                resetUI();
            }
        });
    }

    function deleteToken() {
        messaging.getToken().then((currentToken) => {
            messaging.deleteToken(currentToken).then(() => {
                console.log('Token deleted.');
                setTokenSentToServer(false);
                resetUI();
            }).catch((err) => {
                console.log('Unable to delete token. ', err);
            });
        }).catch((err) => {
            console.log('Error retrieving Instance ID token. ', err);
        });
    }
});