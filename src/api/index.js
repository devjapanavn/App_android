import {FETCH} from '@app/utils';
import _ from 'lodash';
import DeviceInfo from 'react-native-device-info';
const deviceId = DeviceInfo.getUniqueId();

export default {
  getAppInfo() {
    const path = 'version';
    return FETCH({
      path,
      method: 'POST',
    });
  },
  pushToken(member_id, fcmtoken) {
    console.log('fcmtoken', fcmtoken);
    const path = 'user/fcmtoken';
    return FETCH({
      path,
      method: 'POST',
      body: {member_id, fcmtoken, deviceId},
    });
  },
  getStaticBlog(id) {
    const path = 'static/item';
    return FETCH({
      path,
      method: 'POST',
      body: {id},
    });
  },
  getListStaticBlog() {
    const path = 'static';
    return FETCH({
      path,
      method: 'POST',
    });
  },
  getPageBlock(id) {
    const path = 'index';
    return FETCH({
      path,
      method: 'POST',
      body: id ? {id_page: id} : null,
    });
  },
  generateLink(link) {
    const path = 'index/generate_link';
    return FETCH({
      path,
      method: 'POST',
      body: {link},
    });
  },

  //#region User
  login(username, password) {
    const path = 'user/login';
    const params = {username, password};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  logout(id) {
    const path = 'user/logout';
    const params = {id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  getOTP(phone, token, type) {
    const path = 'otp';
    let params = {phone, type};
    params['g-recaptcha-response'] = token;
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  registerOTP(phone,token='') {
    const path = 'otp/register';
    let params = {phone};
    params['g-recaptcha-response'] = token;
    console.log(params)
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  register(name, mobile, password, password_old) {
    const path = 'user/register';
    const params = {name, mobile, password, password_old};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  checkOTP(phone, otp_sms) {
    const path = 'otp/check_otp';
    const params = {phone, otp_sms};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  loginOTP(phone, otp_sms) {
    const path = 'otp/check_otp_login';
    const params = {phone, otp_sms};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  forgetPasswordOTP(phone, otp_sms) {
    const path = 'otp/check_otp_reset_password';
    const params = {phone, otp_sms};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  updateUser(params) {
    const path = 'user/updateuserprofiles';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },
  changePass(params) {
    const path = 'user/update_password';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },
  resetPassword(params) {
    const path = 'otp/reset_password';
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  getProfileUser(id) {
    const path = 'user/userprofiles';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: {id},
    });
  },
  //#endregion

  //#region Address
  getProvince() {
    const path = 'user/province';
    return FETCH({
      path,
      method: 'POST',
    });
  },
  getDistrict(province_id) {
    const path = 'user/district';
    return FETCH({
      path,
      method: 'POST',
      body: {province_id},
    });
  },
  getWard(district_id) {
    const path = 'user/ward';
    return FETCH({
      path,
      method: 'POST',
      body: {district_id},
    });
  },
  getListAddress(member_id) {
    const path = 'address';
    const params = {member_id};
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },
  getDetailAddress(member_id, id) {
    const path = 'address/item';
    const params = {member_id, id};
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },

  removeAddress(member_id, id) {
    const path = 'address/delete';
    const params = {member_id, id};
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },

  addAddress(
    params = {
      member_id,
      address,
      province_id,
      district_id,
      ward_id,
      fullname,
      mobile,
      note,
      province,
      district,
      ward,
    },
  ) {
    const path = 'address/add';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },

  updateAddress(
    params = {
      member_id,
      address,
      province_id,
      district_id,
      ward_id,
      fullname,
      mobile,
      note,
      id,
    },
  ) {
    const path = 'address/update';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },

  setDefaultAddress(member_id, id) {
    const path = 'address/update_default';
    const params = {
      member_id,
      id,
    };
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },

  //#endregion

  //#region product
  ////id_category:
  //id_brand:
  // sort:az: theo tên, paz: theo giá thấp đến cao, pza: theo giá cao đến thấp, default: mặc định
  // text_search:collagen
  // page:0
  //sale:1
  //beginMinPrice:
  //endMaxPrice:
  //category_check:13,205
  //brand_check:27,32
  searchProducts(params) {
    const path = 'product';
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
ddddd
  updateViewed(params = {id, member_id}) {
    const path = 'product/update_viewed';
    return FETCH({
      path,
      method: 'POST',
      body: {...params, device: deviceId},
    });
  },
  //#endregion

  //#region Categories-Product
  //category_id: 0: category lớn nhất - dành cho trang chủ
  //parent_id: chỉ lấy danh mục cấp lớn nhất
  getCategories(id_category = 0, parent_id = 0) {
    const path = 'category';
    const params = {id_category, parent_id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  countCategories(id_category, searchParams = null) {
    const path = 'category/count';
    const params = {id_category, ...searchParams};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  countBrands(id_brand, searchParams = null) {
    const path = 'brand/count';
    const params = {id_brand, ...searchParams};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  getFilter(id_category, id_brand, text_search) {
    const path = 'product/filter';
    const params = {id_category, id_brand, text_search};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },

  getDetailProduct(id, member_id = 0, direction = '', keyword = '') {
    const path = 'product/item';
    const params = {id, deviceId, member_id, direction, keyword};
    console.log('params', params);
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },

  getProductViewd(id, member_id = 0) {
    const path = 'product/list_viewed';
    const params = {id, device: deviceId, member_id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  addComment(params) {
    const path = 'comment/add';
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  getTotalComment(id_product, page = 1) {
    const path = 'comment/list_image';
    const params = {id_product, page};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  getListComment(id_product, list_rate, page = 1) {
    const path = 'comment';
    const params = {id_product, list_rate, page};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  //#endregion

  //#region cart
  //type: + hoac -
  //qty:số lượng item thêm vào giỏ,
  //id sản phẩm
  addToCart(id, qty, member_id, type = '+') {
    const path = 'cart/addcart';
    const params = {id, qty, type, member_id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  updateCartItem(member_id, id, qty) {
    const path = 'cart/updatecart';
    const params = {member_id, qty, id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },
  removeCartItem(member_id, id) {
    const path = 'cart/delete';
    const params = {member_id, id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  updateCheckBuyItem(member_id, check_item) {
    const path = 'cart/update_check';
    const params = {member_id, check_item};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  buyAgain(member_id, id) {
    const path = 'cart/buyagain';
    const params = {member_id, id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  getListCart(member_id) {
    const path = 'cart/list';
    const params = {member_id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  getTotalItemCart(member_id) {
    const path = 'cart/total_item';
    const params = {member_id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  //#endregion

  //#region checkout

  getCheckOutTemp(member_id) {
    const path = 'checkouttemp';
    const params = {member_id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },
  addCheckOutTemp(member_id) {
    const path = 'checkouttemp/add';
    const params = {member_id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  updateCheckOutTemp(
    params = {
      member_id,
      info_notes,
      type_payment,
      info_email,
      id_member_address,
      point_payment,
    },
  ) {
    const path = 'checkouttemp/update';
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },
  // member_id:8061
  // province_id:48
  // district_id:490
  // ward_id:20197
  // default:1
  // fullname:LE VAN
  // mobile:0986801888
  // email:
  // address:123  Lê Văn Tám
  // note:
  // province:Thành phố Đà Nẵng
  // district:Quận Liên Chiểu
  // ward:Phường Hòa Khánh Bắc
  addAddressCheckoutTemp(
    params = {
      member_id,
      province_id,
      district_id,
      ward_id,
      fullname,
      mobile,
      email,
      address,
      note,
      province,
      district,
      ward,
      default: 1,
    },
  ) {
    const path = 'checkouttemp/add_address';
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },
  checkCoupon(member_id, id_cart_temp, coupon) {
    const path = 'checkouttemp/checkcoupon';
    const params = {member_id, id_cart_temp, coupon};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },
  removeCoupon(member_id, id_cart_temp, coupon) {
    const path = 'checkouttemp/remove_coupon';
    const params = {member_id, id_cart_temp, coupon};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },
  //#endregion

  //#region payment
  getPaymentMethod() {
    const path = 'payment';
    return FETCH({
      path,
      method: 'POST',
    });
  },
  //#endregion

  //#region checkout
  addCheckOut(data) {
    const path = 'checkout/add';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: data,
    });
  },

  paymentThanks(member_id, nguonkh = 23) {
    const path = 'checkout/thank';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: {member_id, nguonkh},
    });
  },
  //#endregion

  //#region Order
  getTotalOrderStatus(member_id, show_home = 1) {
    const path = 'order/status_member';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: {member_id, show_home},
    });
  },
  getListOrder(member_id, status_cart, page = 1, code = '') {
    const path = 'order';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: {member_id, status_cart, page, code},
    });
  },
  getListOrderStatus() {
    const path = 'order/status';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
    });
  },
  getDetailOrder(member_id, id) {
    const path = 'order/item';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: {member_id, id},
    });
  },
  //#endregion

  getDetailNews(id) {
    const path = 'news/item';
    const params = {id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  getNotificationCategory() {
    const path = 'notification/category';
    return FETCH({
      path,
      method: 'POST',
    });
  },
  getNotification(id_category) {
    const path = 'notification';
    const params = {id_category};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  getNotificationDetail(id) {
    const path = 'notification/item';
    const params = {id};
    return FETCH({
      path,
      method: 'POST',
      body: params,
      requireToken: true,
    });
  },

  getPopupNotification(type) {
    const path = 'notification/item_popup';
    const params = {screen_show: type};
    return FETCH({
      path,
      method: 'POST',
      body: params,
    });
  },
  //#region voucher
  getVoucherAnother(data) {
    const params = {voucher_code: data};
    console.log(params)
    const path = 'checkout/handlePromotionAnother';
    return FETCH({
      path,
      method: 'POST',
      requireToken: true,
      body: params,
    });
  },
  //#endregion
};
