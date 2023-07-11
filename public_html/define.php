<?php
define("TIME_SESSION", 3600 * 24 * 30);

define("ADMINCP", 'admincp');
define("KEY_ADMIN", 'JAPANAU4VgBmRKS0');
define("SECRET_KEY", 'FNDQONRZXDGJEDIXEIDLJETTQHJJAHDEJAPANAU4VgBmRKS0');

define("URL", 'http://' . $_SERVER['SERVER_NAME'] . '/');
define("URL_WEB", 'http://japana_pageblock.local/');
define("URL_REAL_IMAGE", 'https://outsource.japana.vn/');


/*momo payment*/
define("URL_MOMO_WEBHOOK", 'http://demo.japana.vn/webhook_momo');
define("URL_MOMO", 'https://test-payment.momo.vn/v2/gateway/api/create');
define("URL_MOMO_APP", 'https://test-payment.momo.vn/pay/app');
define("MOMO_PARTNER_CODE", 'MOMOVHR820221226');
define("MOMO_KEY_ACCESS", 'O5JNbaWoVVQeS0OY');
define("MOMO_KEY_SECRET", '0mjDwxFICJZrNwpMENpRwv1BsfLrCMAN');
define("MOMO_KEY_PUBLIC", 'MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA4h0oFjOmCdELj76qshdJwNXQ5ONDA6r7ThP3R+oE5aFCwAOeCt0SqoN1PKiqv+z1iNWI19OsxBt03RYSidhXc+Y6+wPCGDBv3LnhOak2hNIAnuZ6bjQXNcK+CIyBylHIv5rVYY4BySh7Yc7mrD5rA1cUvvG8FNmyx7avQ9JKPgG5K/Vv4REHIEgi6twn2OpBsQxApU/f53CRsbxbY/Vq6DB9RZmy3SmMZfiHYtrbahAjLXgfNheKo8SBxxqV/6pTDu0BswZ+jC7ZBI+D7qYlW4XwTfatBml83Tje9NeeDVF7fx5svcQIg2z/IVMb0uhU7eMkBlTZRuzCHsiL8kNWhxw5pbipL4n1Vcb2pIShohxZZqA1s7A8B7K4U7x0XKYoHoWiz9VrfFW7J2y25D0omEHhcUqkPB70hF0GCQ55oyR2xJl52FIPjFHzTSaguYXDdojw66nXcXxDY3NhOAlKAGUn0QRPMgOmY61RP1M+Buwl8Ac5xjOqvMkEvcIT6Vg2oIVO9wmE07L6wxm7l/6hSHfk30ObQKWwUdJsTMatJ83ed+FVQUmV7n6XfnR8nAu4/GHH79fCulvZXBr7gqOw+A6wmv0a4D8bzX5ekKrIuuR56WGbVZyv0KOo6///egsYQ2M66fsT86SXzss8KXUNnI2QVFIZfhIO0Khnh6KUKyECAwEAAQ==');
define("MOMO_IOS_ID", 'momovhr820221226');
define("KEY_MOMO_PAYMENT", 'JAPANAU4VgBmRKS02022');
define("MOMO_MAX_PAYMENT", 50000000);
define("PAYMENT_MOMO_ID", 5);
define("PAYMENT_MOMO_PAY_LETER_ID", 6);
/*momo payment*/

/*elastic search*/
define("ELASTIC_URL", 'https://elas7-17-for-php7-1.ent.asia-southeast1.gcp.elastic-cloud.com/api/as/v1/engines/elastic-japana-vn/');
define("ELASTIC_ID", 'elas717_for_php71:YXNpYS1zb3V0aGVhc3QxLmdjcC5lbGFzdGljLWNsb3VkLmNvbTo0NDMkZDQ0MTEzMGVjYjBkNDdjODkzNTg1ZGYzNzE1NzMxNTEkNzFiZmZjY2I3ZWJlNGY1MmFjNjIzZmQxYzU5OWRhZjk=');
define("ELASTIC_PRIVATE_ID", 'pyZseoQBTrlsQaj-IFMX');
define("ELASTIC_PRIVATE_KEY", 'ufhojJ6KT_e0VLts0kWkcQ');

define("ELASTIC_TOKEN", 'private-5yg1zbn87tzh6e2akc5gi4bf');
define("ELASTIC_TOKEN_SEARCH", 'search-o9z5uqqcmymemwo5tjirvu3a');

/*elastic search*/

define("URL_VNPAY", 'http://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
define("VNP_TMNCODE", 'JAPANA02');
define("VNP_HASHSECRET", 'FNDQONRZXDGJEDIXEIDLJETTQHJJAHDE');

define("BRAND_NAME", 'JAPANA.VN');
define("OTP_TIME_EXPIRED", '3');//minutes
define("BRAND_SMS_CLIENT_ID", 'C24B5D9F26C52CD5D82E62F0B36F7E');
define("BRAND_SMS_CLIENT_SECRET", 'B00EA244E4055C45021CCA438BC6F6');
define("BRAND_SMS_LINK_SEND", "http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post_json/");
define("BRAND_SMS_LINK_CALLBACK", 'https://japana.vn/frontend/webhook/sms');

define("LIMIT_FREE_SHIP", 500000);
define("ID_CATE_MARKETING", "458,461,463,464,460,459,465,441");

define("URL_ADMIN", 'http://' . $_SERVER['SERVER_NAME'] . '/admincp/');
define("URL_EMS_API", "http://ws.ems.com.vn");
define("URL_BEST_API", "http://ems.vncpost.com");
define("ID_DELIVERY_BEST", 23);
define("ID_DELIVERY_KHOHAIPHONG", 25);
define("ID_KHOHAIPHONG", 16);
define("ID_PAGE_DETAIL", 2975);//8
define("URL_ASSET", URL_REAL_IMAGE . "assets/");
define("URL_FRONTEND", URL_REAL_IMAGE . "assets/frontend/assets/");
define("URL_MOBILE", URL_REAL_IMAGE . "assets/mobile/assets/");
define("URL_NO", URL_REAL_IMAGE . "assets/mobile/");
define("PageRange", 10);
define("PATH_IMAGE_BLOCK_DISPLAY", URL_REAL_IMAGE . "uploads/block/");

define("PATH_IMAGE_COUPON_IMPORT_UPLOAD", "public_html/uploads/coupon/");
define("ID_BLOCK_PAGE_HOME", 6);//6//2890//2939
define("ID_BLOCK_PAGE_BRAND", 10);//6
define("ID_BLOCK_PAGE_PRO_DETAIL", 2975);//6
define("ID_BLOCK_PAGE_PROMOTION", 1840);//6
define("ID_BLOCK_PAGE_EMPTY_SEARCH", 2966);//6
define("ID_BLOCK_PAGE_EMPTY_CART", 2967);//6
define("ID_STATIC_VE_CHUNG_TOI", 3);//6

define("LIMIT_PAGE", 20);
define("START_PAGE", 1);
define("LIMIT_PAGE_LOADMORE", 6);
define("LIMIT_PAGE_SCROLL", 4);

define("BLOCK", "public_html/uploads/block/");

define("PATH_IMAGE_BRAND", URL_REAL_IMAGE . "uploads/brand/");
define("PATH_IMAGE_ATT", URL_REAL_IMAGE . "uploads/att/");
define("PATH_IMAGE_NEWS", URL_REAL_IMAGE . "uploads/news/");
define("PATH_IMAGE_EMAIL", URL_REAL_IMAGE . "uploads/email/");
define("PATH_IMAGE_CUSTOMER", "https://japana.vn/uploads/customer/");
define("PATH_IMAGE_USER", URL_REAL_IMAGE . "uploads/user/");
define("PATH_IMAGE_PRO", URL_REAL_IMAGE . "uploads/product/");
define("PATH_IMAGE_RESIZE_PRO", URL_REAL_IMAGE . "uploads/product/imageresize/");
define("PATH_IMAGE_CATE", URL_REAL_IMAGE . "uploads/category/");
define("PATH_IMAGE_SYSTEM", URL_REAL_IMAGE . "uploads/system/");
define("PATH_IMAGE_BLOCK", URL_REAL_IMAGE . "uploads/block/");
define("PATH_IMAGE_PAGES", URL_REAL_IMAGE . "uploads/pages/");
define("PATH_IMAGE_BANNER", URL_REAL_IMAGE . "uploads/banner/");
define("PATH_EXCEL", "public_html/uploads/excel/");
define("PATH_EXCEL_INVENTORY", "public_html/exportfile/kho/");
define("PATH_EXCEL_UPLOAD", URL_REAL_IMAGE . "uploads/excelproduct/");
define("PATH_EXCEL_UPLOAD_NEW", "/uploads/excelproduct/");
define("PATH_IMAGE_COMMENT", URL_REAL_IMAGE . "uploads/comment/");
define("PATH_IMAGE_NOTIFICATION", URL_REAL_IMAGE . "uploads/notifications/");

define("PATH_EXCEL_EXPORT", "public_html/uploads/");
/*define("PATH_EXCEL_EXPORT_PRODUCT","public_html/uploads/");
define("PATH_EXCEL_EXPORT_HISTORY","public_html/uploads/");*/

define("PATH_IMAGE_PROMOTION", URL_REAL_IMAGE . "uploads/promotion/");

define("PATH_IMAGE_PRO_VIDEO","/uploads/product/video/");
define("PATH_IMAGE_PRO_VIDEO_UPLOAD","/uploads/product/video/");
define("PATH_IMAGE_worhour_UPLOAD", "public_html/uploads/other/");
define("PATH_IMAGE_PRO_UPLOAD", "/uploads/product/");
define("PATH_IMAGE_PRO_UPLOAD_RESIZE", "/uploads/product/imageresize/");
define("PATH_IMAGE_BRAND_UPLOAD", "public_html/uploads/brand/");
define("PATH_IMAGE_ATT_UPLOAD", "public_html/uploads/att/");
define("PATH_IMAGE_NEWS_UPLOAD", "public_html/uploads/news/");
define("PATH_IMAGE_EMAIL_UPLOAD", "public_html/uploads/email/");
define("PATH_IMAGE_CUSTOMER_UPLOAD", "/home/japana.vn/public_html/uploads/customer/");
define("PATH_IMAGE_USER_UPLOAD", "public_html/uploads/user/");
define("PATH_IMAGE_PRODUCT_UPLOAD", "public_html/uploads/product");
define("PATH_IMAGE_PRODUCT_UPLOAD_NON", "uploads/product");
define("PATH_IMAGE_CATEGORY_UPLOAD", "public_html/uploads/category/");
define("PATH_IMAGE_SYSTEM_UPLOAD", "public_html/uploads/system/");
define("PATH_IMAGE_BLOCK_UPLOAD", "public_html/uploads/block/");
define("PATH_IMAGE_PAGES_UPLOAD", "public_html/uploads/pages/");
define("PATH_IMAGE_BANNER_UPLOAD", "public_html/uploads/banner/");
define("PATH_IMAGE_COMMENT_UPLOAD", "public_html/uploads/comment/"); //
define("PATH_IMAGE_NOTIFICATION_UPLOAD", "public_html/uploads/notifications/");

define("PATH_IMAGE_PROMOTION_UPLOAD", "public_html/uploads/promotion/");
define("PATH_RESIZE_268", "268x268");

define("KEY_LOGIN_ADMIN", 'BmRKS0');
define("KEY_SESSION_LOGIN_ADMIN", 'AU4VgBmRK');
define("KEY_PASSWORD_ADMIN", 'JAPANAU4VgBmRKS0');

define("KEY_LOGIN_FRONTEND", 'lgfrontend');
define("KEY_SESSION_LOGIN_FRONTEND", 'lgfrontend');
define("KEY_PASSWORD_FRONTEND", 'JAPANAU4VgBmRKS0aaa');

define("LBL_SES_LOGIN_USER", 'lguser');
define("LBL_SES_LOGIN_VALUE", 'ADMINJAPANA');

/************ THONG BAO ***********/
define("MSG_ACCOUNT_LOGIN_ERROR", 'Tài khoản không tồn tại');

define("MSG_PDW_WRONG", 'Mật khẩu không đúng');
define("MSG_USER_WRONG", 'User không tồn tại');

define("MSG_ERROR_CODE", 'CODE ERROR');

/******** LABEL ADMIN***********/
/// form thay đổi mật khẩu
define("JAPANA_ADMIN", 'Japana Admin');
define("CHANGE_PASSWORD", 'Thay đổi mật khẩu');

define("LBL_AD_TEXT1", 'Đổi mật khẩu');
define("LBL_AD_TEXT2", 'Thông tin mật khẩu');
define("LBL_AD_TEXT3", 'Mật khẩu hiện tại');
define("LBL_AD_TEXT4", 'Mật khẩu mới');
define("LBL_AD_TEXT5", 'Xác nhận mật khẩu mới');
define("LBL_AD_TEXT6", 'Gõ vào đây');
define("LBL_AD_TEXT7", 'Thực hiện');
define("LBL_AD_TEXT8", 'Bạn đã thay đổi mật khẩu thành công');
define("LBL_AD_TEXT9", 'Mật khẩu hiện tại không đúng');

define("LBL_AD_DESC1", 'Vui lòng nhập mật khẩu hiện tại , sau đó nhập mật khẩu mới !');

/// form nhóm sản phẩm
define("LBL_AD_PRO_TEXT0", 'Thêm mới');
define("LBL_AD_PRO_TEXT1", 'Tạo mới thông tin danh mục sản phẩm');
define("LBL_AD_PRO_TEXT2", 'Danh mục sản phẩm');
define("LBL_AD_PRO_TEXT3", 'Tên nhóm');
define("LBL_AD_PRO_TEXT4", 'Tags');
define("LBL_AD_PRO_TEXT5", 'Links url');
/// form sản phẩm
define("LBL_AD_PRO_TEXT6", 'Sản phẩm');
define("STATUS_SUCCESS", 'success');
define("STATUS_ERROR", 'error');

define("MAX_ID_BLOCk_OLD", 123);
define("FREESHIP_ORDER_MIN", 500000);
define("NGUONKH_APP", 25);

/// menu bên trái
define("MENU_LEFT_AD_TEXT0", 'Dashboard');
define("MENU_LEFT_AD_TEXT1", 'Danh mục sản phẩm');
define("MENU_LEFT_AD_TEXT2", 'Sản phẩm');

define("APP_ID", "626517987552557");
define("APP_SECRET", "487003c90a4c6cf5c81c07a08e064cd5");

define("FREESHIP_ORDER_MIN", 500000);
define("SHIP_HCM", 20000);
define("SHIP_OUT_HCM", 40000);

define("NGUON_KH_APP", 25);

define("VIP_DIAMOND_ID",15);
define("VIP_PLATINUM_ID",14);
define("VIP_GOLD_ID",13);
define("VIP_NORMAL_ID",4);
define("ID_TYPE_KH_CU",1);//KH CŨ
define("ID_TYPE_KH_MOI",2);//KH MOI

define("TYPE_POINT_TICHDIEM",1);// loai quy doi tich diem sau khi don hoan thanh
define("TYPE_POINT_MUAHANG",2);// loai quy doi dung diem mua hang

define("TYPE_POINT_RETURN",3);// DANH CHO luu log tra hang tra diem da mua va diem tich luy
define("TYPE_POINT_CANCEL",4);// DANH CHO luu log huy don tra diem da mua

define("GG_KMNM","KMNM");//KMNM	Khuyến mãi nhập mã
define("GG_GV","GV");//GV	Giảm Vip
define("GG_KMDH","KMDH");//KMDH	Khuuyến mãi đơn hàng
define("GG_CKVNPAY","CKVNPAY");//CKVNPAY	Chiiết khấu VNPAY
define("GG_PPS","PPS");// id 1 phí phát sinh
define("GG_TBP","TBP");// id 2 trưởng bộ phận
define("GG_BGD","BGD");// id 3 ban giám đốc
define("GG_TICHDIEM","TICHDIEM");// value_money_point_payment

define("CODE_KHO_CHINH","JP");
define("CODE_KHO_HAIPHONG","HP");
define("CODE_PHIEU_NHAP","PN");
define("CODE_PHIEU_NHAP_HAIPHONG","PN-HP");
define("CODE_PHIEU_TRA","HT");
define("CODE_PHIEU_TRA_HAIPHONG","HT-HP");
define("CODE_PHIEU_TRA_NCC","PT");
define("CODE_PHIEU_TRA_NCC_HAIPHONG","PT-HP");

define("ARRAY_ID_KHO_HAI_PHONG",[3,8,9,16,18]);

define("IS_DEVELOPER",0);//false