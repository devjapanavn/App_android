import Toast from 'react-native-root-toast';


export const toastAlert = (
    message,
    duration = Toast.durations.SHORT,
    position = Toast.positions.BOTTOM
) => {
    setTimeout(() => {
        let toast = Toast.show(message, {
            duration,
            position,
            animation: true,
            hideOnPress: true,
            shadow: true,
            opacity: 0.7,
            textColor: '#fff',
            containerStyle: {
                borderRadius: 20,
                backgroundColor: "rgba(0,0,0,0.9)"
            },
            textStyle: {
                lineHeight: 20
            },
        })
    }, 250);
}
