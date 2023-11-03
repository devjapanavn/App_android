import { Alert } from "react-native";
import { check, PERMISSIONS, RESULTS } from "react-native-permissions";

export const permissionHelper = {
    async explainReadAndWriteStoragePermission(callback) {
        check(PERMISSIONS.ANDROID.WRITE_EXTERNAL_STORAGE)
        .then((res) => {
          console.log('explainReadAndWriteStoragePermission',res)
          switch (res) {
            case RESULTS.UNAVAILABLE:
              toastAlert("Thiếp bị này không hỗ trợ ");
              break;
            case RESULTS.GRANTED:
              if (callback) callback();
              break;
            case RESULTS.DENIED:
                Alert.alert(
                'Cung cấp quyền sử dụng',
                'Để sử dụng chức năng này, bạn cần cung cấp quyền ghi hình ảnh và video.',
                [
                  {
                    text: 'Cung cấp ngay',
                    onPress: () => {
                      request(PERMISSIONS.ANDROID.WRITE_EXTERNAL_STORAGE).then(res => {
                        if (res === "granted") {
                          if (callback) callback();
                        }
                      })
                    }
                  },
                  { text: 'Huỷ' },
                ]
              )
              break;
          }
        });
    },
}
