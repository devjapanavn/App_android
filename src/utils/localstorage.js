import AsyncStorage from '@react-native-async-storage/async-storage'

export const localStorage = {
    async save(key, data) {
        try {
            await AsyncStorage.setItem(key, JSON.stringify(data));
            return true;
        } catch (error) {
            console.warn(error);
            return false;
        }
    },
    async delete(key) {
        try {
            await AsyncStorage.removeItem(key);
            return true;
        } catch (error) {
            console.warn(error);
            return false;
        }
    },
    async get(key) {
        if (key && key.length > 0) {
            try {
                const stringData = await AsyncStorage.getItem(key);
                if (stringData && stringData.length > 0) {
                    return JSON.parse(stringData)
                }
            } catch (error) {
                return false;
            }
        }
        return false;
    }
}
