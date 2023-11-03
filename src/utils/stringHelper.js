import { regexStringUser } from './regex';
import _ from 'lodash';

const OrderStatus = {
  new: 0,
  confirmed: 1,
  processed: 8,
  prepareOrder: 2,
  delivery: 3,
  completed: 4,
  completedOrder: 7,
  orderReject: 6,
};
export const stringHelper = {
  formatMoney(money) {
    if (typeof money === 'undefined') {
      return '0';
    }

    let moneyNumber = 0;
    if (typeof money === 'string' && money.length > 0) {
      moneyNumber = parseInt(money.replace(/[^0-9]/g, ''), 0) || 0;
    } else if (typeof money === 'number') {
      moneyNumber = money;
    }
    return moneyNumber.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  },

  numberToStringDigit(number, targetLength) {
    var output = number + '';
    while (output.length < targetLength) {
      output = '0' + output;
    }
    return output;
  },

  formatToNumber(value) {
    if (value === '') {
      return 0;
    } else if (typeof value === 'undefined') {
      return 0;
    } else if (value === null) {
      return 0;
    } else if (typeof value === 'number') {
      return value;
    } else {
      try {
        return parseInt(value, 10);
      } catch (error) {
        return 0;
      }
    }
  },

  checkStringUser(user) {
    return regexStringUser.test(user);
  },

  getTitleAvatar(title) {
    let avatarTitle = '';
    if (_.isString(title) && title.length > 0) {
      const splitName = title.split();
      if (splitName && splitName.length > 0) {
        avatarTitle = splitName[splitName.length - 1].slice(0, 1);
      }
    }
    return avatarTitle;
  },

  convertStringToJson(value) {
    if (!_.isEmpty(value)) {
      if (_.isArray(value)) {
        return value;
      } else if (_.isString(value)) {
        try {
          return JSON.parse(value);
        } catch (error) {
          return null;
        }
      }
    }
    return null;
  },

  generateFullAddress(address, ward, district, province) {
    let fullAddress = `${address},`;
    if (ward) {
      fullAddress += ` ${ward},`;
    }
    if (district) {
      fullAddress += ` ${district},`;
    }
    if (province) {
      fullAddress += ` ${province}`;
    }
    return fullAddress;
  },

  orderStatusString(orderStatus) {
    switch (orderStatus) {
      case OrderStatus.new:
        return 'Mới đặt';
      case OrderStatus.confirmed:
        return 'Đã xác nhận';
      case OrderStatus.processed:
        return 'Đã xử lý';
      case OrderStatus.prepareOrder:
        return 'Chuẩn bị hàng';
      case OrderStatus.delivery:
        return 'Đang giao hàng';
      case OrderStatus.completed:
        return 'Hoàn thành';
      case OrderStatus.completedOrder:
        return 'Hoàn thành - Trả hàng';
      case OrderStatus.orderReject:
        return 'Đơn huỷ';
      default:
        return 'Mới đặt';
    }
  },

  convertStringToColor(str, s = 46, l = 56) {
    var hash = 0;
    for (var i = 0; i < str.length; i++) {
      hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }

    var h = hash % 360;
    return 'hsl(' + h + ', ' + s + '%, ' + l + '%)';
  },

  convertGender(gender = 0) {
    switch (gender) {
      case 1:
        return 'Nam';
      case 2:
        return 'Nữ';
      default:
        return 'Khác';
    }
  },
  isValidURL(string) {
    var res = string.match(
      /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g,
    );
    return res !== null;
  },

  codeToArray(code) {
    return code?.split('') ?? [];
  },
};
