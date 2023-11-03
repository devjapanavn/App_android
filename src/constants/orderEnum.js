ORDER_STATUS = {
  1: 'Mới đặt',
  2: 'Đang giao hàng',
  3: 'Hoàn thành',
  0: 'Hủy',
};
export const orderEnum = {
  convertToString(val) {
    return ORDER_STATUS[val];
  },
  convertToColor(val) {
    switch (val) {
      case '0':
        return '#ffa200';
      case '1':
        return '#2bd600';
      case '2':
        return '#2367ff';
      case '3':
        return '#2367ff';
      default:
        return '#dc0000';
    }
  },
};
