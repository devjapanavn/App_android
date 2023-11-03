import PushNotificationIOS from "@react-native-community/push-notification-ios";
import PushNotification from "react-native-push-notification";

function initNoti(onPressNoti) {
    PushNotification.configure({
        onRegister: (token) => {
            console.log("TOKEN", token);
        },
        // (required) Called when a remote is received or opened, or local notification is opened
        onNotification: function (notification) {
            console.log("NOTIFICATION:", notification);

            // process the notification

            // (required) Called when a remote is received or opened, or local notification is opened
            notification.finish(PushNotificationIOS.FetchResult.NoData);

            if (onPressNoti && notification.userInteraction) {
                onPressNoti(notification)
            }
        },

        // (optional) Called when Registered Action is pressed and invokeApp is false, if true onNotification will be called (Android)
        onAction: function (notification) {
            console.log("ACTION:", notification.action);
            console.log("NOTIFICATION:", notification);

            // process the action
        },

        // (optional) Called when the user fails to register for remote notifications. Typically occurs when APNS is having issues, or the device is a simulator. (iOS)
        onRegistrationError: function (err) {
            console.error(err.message, err);
        },

        // IOS ONLY (optional): default: all - Permissions to register.
        permissions: {
            alert: true,
            badge: true,
            sound: true,
        },

        // Should the initial notification be popped automatically
        // default: true
        popInitialNotification: true,

        /**
         * (optional) default: true
         * - Specified if permissions (ios) and token (android and ios) will requested or not,
         * - if not, you must call PushNotificationsHandler.requestPermissions() later
         * - if you are not using remote notification or do not have Firebase installed, use this:
         *     requestPermissions: Platform.OS === 'ios'
         */
        requestPermissions: true,
    })
}

function remind(scheduleId, contentId, contentName, programName, bannerUrl, remindDate) {
    let name = "";
    if (contentName && programName) {
        name = `Đến giờ chiếu ${programName} trên kênh ${contentName}`;
    } else if (programName) {
        name = `Đến giờ chiếu ${programName}`;
    }
    PushNotification.localNotificationSchedule({
        /* Android Only Properties */
        channelId: "schedule_live", // (required) channelId, if the channel doesn't exist, it will be created with options passed above (importance, vibration, sound). Once the channel is created, the channel will not be update. Make sure your channelId is different if you change these options. If you have created a custom channel, it will apply options of the channel.
        // ticker: name, // (optional)
        showWhen: true, // (optional) default: true
        autoCancel: true, // (optional) default: true
        // largeIcon: "ic_notification", // (optional) default: "ic_launcher". Use "" for no large icon.
        // largeIconUrl: "https://www.example.tld/picture.jpg", // (optional) default: undefined
        smallIcon: "ic_notification", // (optional) default: "ic_notification" with fallback for "ic_launcher". Use "" for default small icon.
        // bigText: "Đã đến giờ chiếu.", // (optional) default: "message" prop
        subText: contentName, // (optional) default: none
        bigPictureUrl: bannerUrl ? bannerUrl : undefined, // (optional) default: undefined
        // color: "red", // (optional) default: system default
        vibrate: true, // (optional) default: true
        vibration: 300, // vibration length in milliseconds, ignored if vibrate=false, default: 1000
        // tag: "some_tag", // (optional) add tag to message
        // group: "group", // (optional) add group to message
        // groupSummary: false, // (optional) set this notification to be the group summary for a group of notifications, default: false
        // ongoing: false, // (optional) set whether this is an "ongoing" notification
        priority: "high", // (optional) set notification priority, default: high
        visibility: "private", // (optional) set notification visibility, default: private
        ignoreInForeground: false, // (optional) if true, the notification will not be visible when the app is in the foreground (useful for parity with how iOS notifications appear)
        // shortcutId: "shortcut-id", // (optional) If this notification is duplicative of a Launcher shortcut, sets the id of the shortcut, in case the Launcher wants to hide the shortcut, default undefined
        onlyAlertOnce: false, // (optional) alert will open only once with sound and notify, default: false

        when: null, // (optionnal) Add a timestamp pertaining to the notification (usually the time the event occurred). For apps targeting Build.VERSION_CODES.N and above, this time is not shown anymore by default and must be opted into by using `showWhen`, default: null.
        usesChronometer: false, // (optional) Show the `when` field as a stopwatch. Instead of presenting `when` as a timestamp, the notification will show an automatically updating display of the minutes and seconds since when. Useful when showing an elapsed time (like an ongoing phone call), default: false.
        timeoutAfter: null, // (optional) Specifies a duration in milliseconds after which this notification should be canceled, if it is not already canceled, default: null

        messageId: "google:message_id", // (optional) added as `message_id` to intent extras so opening push notification can find data stored by @react-native-firebase/messaging module. 

        // actions: ["Yes", "No"], // (Android only) See the doc for notification actions to know more
        invokeApp: true, // (optional) This enable click on actions to bring back the application to foreground or stay in background, default: true

        /* iOS only properties */
        alertAction: "view", // (optional) default: view
        category: "", // (optional) default: empty string

        /* iOS and Android properties */
        id: scheduleId, // (optional) Valid unique 32 bit integer specified as string. default: Autogenerated Unique ID
        title: contentName + ": " + programName, // (optional)
        message: name, // (required)
        userInfo: {
            contentId,
            name: contentName,
            programName
        }, // (optional) default: {} (using null throws a JSON value '<null>' error)
        // playSound: false, // (optional) default: true
        soundName: "default", // (optional) Sound to play when the notification is shown. Value of 'default' plays the default sound. It can be set to a custom sound such as 'android.resource://com.xyz/raw/my_sound'. It will look for the 'my_sound' audio file in 'res/raw' directory and play it. default: 'default' (default sound is played)
        // number: 1, // (optional) Valid 32 bit integer specified as string. default: none (Cannot be zero)
        // repeatType: "day", // (optional) Repeating interval. Check 'Repeat
        date: remindDate,
    });
}

function pushForeground(messageId, title, description, data) {
    PushNotification.localNotification({
        /* Android Only Properties */
        channelId: "fb_notification", // (required) channelId, if the channel doesn't exist, it will be created with options passed above (importance, vibration, sound). Once the channel is created, the channel will not be update. Make sure your channelId is different if you change these options. If you have created a custom channel, it will apply options of the channel.
        showWhen: true, // (optional) default: true
        autoCancel: true, // (optional) default: true
        largeIcon: "ic_launcher", // (optional) default: "ic_launcher". Use "" for no large icon.
        smallIcon: "ic_notification", // (optional) default: "ic_notification" with fallback for "ic_launcher". Use "" for default small icon.
        vibrate: true, // (optional) default: true
        vibration: 300, // vibration length in milliseconds, ignored if vibrate=false, default: 1000
        group: "fb_noti", // (optional) add group to message
        groupSummary: false, // (optional) set this notification to be the group summary for a group of notifications, default: false
        ongoing: false, // (optional) set whether this is an "ongoing" notification
        priority: "default", // (optional) set notification priority, default: high
        visibility: "private", // (optional) set notification visibility, default: private
        ignoreInForeground: false, // (optional) if true, the notification will not be visible when the app is in the foreground (useful for parity with how iOS notifications appear)
        onlyAlertOnce: false, // (optional) alert will open only once with sound and notify, default: false

        when: null, // (optionnal) Add a timestamp pertaining to the notification (usually the time the event occurred). For apps targeting Build.VERSION_CODES.N and above, this time is not shown anymore by default and must be opted into by using `showWhen`, default: null.
        usesChronometer: false, // (optional) Show the `when` field as a stopwatch. Instead of presenting `when` as a timestamp, the notification will show an automatically updating display of the minutes and seconds since when. Useful when showing an elapsed time (like an ongoing phone call), default: false.
        timeoutAfter: null, // (optional) Specifies a duration in milliseconds after which this notification should be canceled, if it is not already canceled, default: null

        messageId: messageId, // (optional) added as `message_id` to intent extras so opening push notification can find data stored by @react-native-firebase/messaging module. 
        invokeApp: true, // (optional) This enable click on actions to bring back the application to foreground or stay in background, default: true
        /* iOS only properties */
        alertAction: "view", // (optional) default: view
        category: "", // (optional) default: empty string

        /* iOS and Android properties */
        // id: 0, // (optional) Valid unique 32 bit integer specified as string. default: Autogenerated Unique ID
        title: title, // (optional)
        message: description, // (required)
        userInfo: data, // (optional) default: {} (using null throws a JSON value '<null>' error)
        playSound: false, // (optional) default: true
        soundName: "default", // (optional) Sound to play when the notification is shown. Value of 'default' plays the default sound. It can be set to a custom sound such as 'android.resource://com.xyz/raw/my_sound'. It will look for the 'my_sound' audio file in 'res/raw' directory and play it. default: 'default' (default sound is played)
    });
}
export {
    initNoti, remind, pushForeground
}