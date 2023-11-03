import api from '@app/api';
import { appDimensions, colors, globalStyles, images, spacing } from '@app/assets';
import { ROUTES } from '@app/constants';
import { resetAndNavigateRoute, navigateRoute } from '@app/route';
import { convertTimeAgo } from '@app/utils';
import _ from 'lodash';
import React from 'react';
import {
    FlatList,
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import { Button, Card, Text } from 'react-native-elements';
import FastImage from 'react-native-fast-image';
import Spinner from 'react-native-spinkit';
import { iOSColors } from 'react-native-typography';
import { useQuery } from 'react-query';
const fetch = async (id_category) => {
    return await api.getNotification(id_category);
};

const NotificationItem = React.memo(({ notification }) => {
    return <TouchableOpacity onPress={() => navigateRoute(ROUTES.NOTIFICATION_DETAIL, { ...notification })}>
        <Card containerStyle={styles.containerItem} >
            <View style={{ flexDirection: 'row' }}>
                <FastImage source={{ uri: notification.images }} style={styles.notice_item_image} />
                <View style={{ flex: 1 }}>
                    <Card.Title style={styles.itemTitle} >{notification?.title}</Card.Title>
                    <Text  >{notification?.body}</Text>
                    <View style={[globalStyles.row, { marginVertical: 4 }]}>
                        <FastImage source={images.ic_event} style={{ width: 20, height: 20 }} />
                        <Text style={styles.footerItemText} >{convertTimeAgo(notification?.updated)}</Text>
                    </View>
                </View>
            </View>

        </Card>
    </TouchableOpacity>
}, () => true)

export const ListNotification = React.memo(({ categoryId = 0 }) => {
    const { status, data, error, refetch, isLoading, isRefetching } = useQuery(
        ['getNotificationList', { categoryId: categoryId }],
        () => fetch(categoryId),
        { enabled: categoryId !== 0 }
    );
    if (isLoading) {
        return (
            <View
                style={{
                    justifyContent: 'center',
                    alignItems: 'center',
                    margin: spacing.large,
                    flex: 1,
                }}>
                <Spinner type="Circle" color={colors.primary} size={40} />
            </View>
        );
    }

    return <FlatList
        key={'tab_notification_list'}
        data={data || []}
        extraData={(item, index) => `tab_notification_${item.id}`}
        ListEmptyComponent={() => <View style={{ margin: 10, justifyContent: 'center', flex: 1, alignItems: 'center' }}>
            <Text>Hiện bạn chưa có thông báo nào </Text>
            <Button title={"Tiếp tục mua sắm"} buttonStyle={{ backgroundColor: iOSColors.orange, width: appDimensions.width * 2 / 3, marginVertical: 10 }} onPress={() => resetAndNavigateRoute([{ name: ROUTES.MAIN_TABS }])} />
        </View>}
        renderItem={({ item }) => {
            return <NotificationItem notification={item} />
        }} />
}, () => false)


const styles = StyleSheet.create({

    containerItem: {
        margin: 0,
        borderWidth: 0,
        borderBottomWidth: 1,
    },
    notice_item_image: {
        width: 40, height: 40,
        resizeMode: 'contain',
        marginRight: 8
    },
    itemTitle: {
        ...globalStyles.text,
        textAlign: 'left',
        fontSize: 15,
    },
    footerItemText: {
        ...globalStyles.text,
        color: colors.gray,
        fontSize: 12,
        marginLeft: 8
    }

});
