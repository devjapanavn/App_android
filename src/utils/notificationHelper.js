import { NOTIFICATION_ENUM, ROUTES } from '@app/constants';
import { navigateRoute, tabNavigate } from '@app/route';
import _ from 'lodash';

function onHandleNotification(data) {
    if (data && data.type) {
        switch (data.type) {
            case NOTIFICATION_ENUM.NOTIFICATION:
                setTimeout(() => {
                    tabNavigate(ROUTES.NOTIFICATION, data)
                }, 300);
                break;

            default:
                break;
        }
    }
}

export { onHandleNotification };
