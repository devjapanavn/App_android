export const genderFormatString = val => {
  switch (val) {
    case '0':
      return 'Nữ';
    case '1':
      return 'Nam';

    default:
      return 'Khác';
  }
};
